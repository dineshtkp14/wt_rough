<?php

namespace App\Http\Livewire;

use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AllCustomerCreditListLivewire extends Component
{
    use WithPagination;

    private const MIN_VISIBLE_BALANCE = 1.00;

    protected $paginationTheme = 'bootstrap';

    public $searchTerm = '';
    public $sortBy = 'high_to_low';

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function render()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        [$ledgerTotals, $creditNoteTotals] = $this->creditSummarySubqueries();
        $query = $this->buildCustomerCreditQuery($ledgerTotals, $creditNoteTotals);
        $this->applyFilters($query);
        $this->applySorting($query);

        $customers = $query->paginate(100);

        [$ledgerTotalsForTotal, $creditNoteTotalsForTotal] = $this->creditSummarySubqueries();
        $totalQuery = $this->buildCustomerCreditQuery($ledgerTotalsForTotal, $creditNoteTotalsForTotal);
        $this->applyFilters($totalQuery);

        $totalDue = $totalQuery->get()->sum(function ($row) {
            return max(0, (float) $row->total_due);
        });

        [$ledgerTotalsForAdvance, $creditNoteTotalsForAdvance] = $this->creditSummarySubqueries();
        $advanceQuery = $this->buildCustomerCreditQuery($ledgerTotalsForAdvance, $creditNoteTotalsForAdvance);
        $this->applyCommonFilters($advanceQuery);
        $advanceQuery->having('total_due', '<', -self::MIN_VISIBLE_BALANCE);
        $advanceQuery->orderBy('total_due', 'asc');

        $advanceCustomers = $advanceQuery->get();
        $totalAdvanceDeposit = abs($advanceCustomers->sum('total_due'));

        return view('livewire.all-customer-credit-list-livewire', [
            'customers' => $customers,
            'totalDue' => $totalDue,
            'totalAdvanceDeposit' => $totalAdvanceDeposit,
            'advanceCustomers' => $advanceCustomers,
        ]);
    }

    public function generatePDF()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        [$ledgerTotals, $creditNoteTotals] = $this->creditSummarySubqueries();
        $query = $this->buildCustomerCreditQuery($ledgerTotals, $creditNoteTotals);
        $this->applyFilters($query);
        $this->applySorting($query);

        $customers = $query->get();
        $totalDue = $customers->sum(function ($row) {
            return max(0, (float) $row->total_due);
        });
        $totalAdvanceDeposit = abs($customers->sum(function ($row) {
            return min(0, (float) $row->total_due);
        }));

        $pdfView = view('customerledgerhistory.all_customer_credit_list_pdf', [
            'customers' => $customers,
            'totalDue' => $totalDue,
            'totalAdvanceDeposit' => $totalAdvanceDeposit,
            'filterLabel' => $this->selectedFilterLabel(),
        ]);

        $pdf = FacadePdf::setOptions(['dpi' => 150, 'defaultFont' => 'dejavu serif'])->loadHtml($pdfView);
        $pdfFile = tempnam(sys_get_temp_dir(), 'all_customer_credit_list');
        $pdf->save($pdfFile);

        $todayDate = date('Y-m-d_H.i.s');
        $filename = 'all_customer_credit_list_' . $todayDate . '.pdf';

        return response()->file($pdfFile, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }

    private function creditSummarySubqueries()
    {
        $ledgerTotals = DB::table('customerledgerdetails')
            ->select(
                'customerid',
                DB::raw('MAX(date) as latest_date'),
                DB::raw('MAX(CASE WHEN invoicetype = "credit" THEN date END) as latest_credit_date'),
                DB::raw('MAX(CASE WHEN invoicetype = "credit" THEN credit_limit_days END) as credit_limit_days'),
                DB::raw('COALESCE(SUM(debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(credit), 0) as total_credit')
            )
            ->whereIn('invoicetype', ['credit', 'payment'])
            ->groupBy('customerid');

        $creditNoteTotals = DB::table('creditnotes_customerledgerdetails as cn')
            ->select(
                'cn.customerid',
                DB::raw('COALESCE(SUM(COALESCE(cn.debit, cn.credit, 0)), 0) as credit_note_credit')
            )
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('customerledgerdetails as oldcl')
                    ->whereColumn('oldcl.customerid', 'cn.customerid')
                    ->where(function ($match) {
                        $match->whereColumn('oldcl.cninvoiceid', 'cn.invoiceid')
                            ->orWhereColumn('oldcl.returnidforcreditnotes', 'cn.invoiceid')
                            ->orWhere(function ($sameReturn) {
                                $sameReturn->whereColumn('oldcl.date', 'cn.date')
                                    ->where(function ($returnType) {
                                        $returnType->whereIn('oldcl.particulars', ['salesreturn', 'Goods_Return'])
                                            ->orWhereIn('oldcl.voucher_type', ['return', 'Return']);
                                    })
                                    ->whereRaw('oldcl.credit BETWEEN COALESCE(cn.debit, cn.credit, 0) - 0.01 AND COALESCE(cn.debit, cn.credit, 0) + 0.01');
                            });
                    });
            })
            ->groupBy('cn.customerid');

        return [$ledgerTotals, $creditNoteTotals];
    }

    private function buildCustomerCreditQuery($ledgerTotals, $creditNoteTotals)
    {
        return DB::table('customerinfos as c')
            ->leftJoinSub($ledgerTotals, 'lt', function ($join) {
                $join->on('lt.customerid', '=', 'c.id');
            })
            ->leftJoinSub($creditNoteTotals, 'cnt', function ($join) {
                $join->on('cnt.customerid', '=', 'c.id');
            })
            ->select(
                'c.id',
                'c.name',
                'c.address',
                'c.phoneno',
                'c.alternate_phoneno',
                'c.email',
                'c.type',
                'lt.latest_date',
                'lt.latest_credit_date',
                'lt.credit_limit_days',
                DB::raw('COALESCE(lt.total_debit, 0) as total_debit'),
                DB::raw('COALESCE(lt.total_credit, 0) as total_credit'),
                DB::raw('COALESCE(cnt.credit_note_credit, 0) as credit_note_credit'),
                DB::raw('(COALESCE(lt.total_debit, 0) - COALESCE(lt.total_credit, 0) - COALESCE(cnt.credit_note_credit, 0)) as total_due')
            );
    }

    private function applyFilters($query)
    {
        $this->applyCommonFilters($query);

        if ($this->sortBy === 'advance_deposit') {
            $query->having('total_due', '<', -self::MIN_VISIBLE_BALANCE);
            return;
        }

        $query->having('total_due', '>', self::MIN_VISIBLE_BALANCE);
    }

    private function applyCommonFilters($query)
    {
        if (trim($this->searchTerm) !== '') {
            $search = '%' . trim($this->searchTerm) . '%';

            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('c.name', 'like', $search)
                    ->orWhere('c.address', 'like', $search)
                    ->orWhere('c.phoneno', 'like', $search)
                    ->orWhere('c.alternate_phoneno', 'like', $search)
                    ->orWhere('c.email', 'like', $search)
                    ->orWhere('c.id', 'like', $search);
            });
        }

        if ($this->sortBy === 'shop') {
            $query->where('c.type', 'shop');
        }

        if ($this->sortBy === 'customer') {
            $query->where('c.type', 'customer');
        }

        if ($this->sortBy === 'more_than_45_days') {
            $query->whereDate('lt.latest_date', '<=', now()->subDays(45)->toDateString());
        }

        if ($this->sortBy === 'credit_time_expired') {
            $query->whereNotNull('lt.latest_credit_date')
                ->whereNotNull('lt.credit_limit_days')
                ->whereRaw('DATE_ADD(lt.latest_credit_date, INTERVAL lt.credit_limit_days DAY) < CURDATE()');
        }
    }

    private function applySorting($query)
    {
        if ($this->sortBy === 'advance_deposit') {
            $query->orderBy('total_due', 'asc');
            return;
        }

        if ($this->sortBy === 'low_to_high') {
            $query->orderBy('total_due', 'asc');
            return;
        }

        if ($this->sortBy === 'newest') {
            $query->orderByDesc('lt.latest_date');
            return;
        }

        if ($this->sortBy === 'oldest') {
            $query->orderBy('lt.latest_date', 'asc');
            return;
        }

        $query->orderByDesc('total_due');
    }

    private function selectedFilterLabel()
    {
        return [
            'high_to_low' => 'High to Low',
            'low_to_high' => 'Low to High',
            'credit_time_expired' => 'Credit Time Expired',
            'shop' => 'Shop Only',
            'customer' => 'Customer Only',
            'advance_deposit' => 'Advance Deposit Only',
            'more_than_45_days' => 'More Than 45 Days',
            'newest' => 'Newest',
            'oldest' => 'Oldest',
        ][$this->sortBy] ?? 'High to Low';
    }
}
