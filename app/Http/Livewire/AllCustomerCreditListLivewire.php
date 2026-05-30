<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AllCustomerCreditListLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $searchTerm = '';

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function render()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $ledgerTotals = DB::table('customerledgerdetails')
            ->select(
                'customerid',
                DB::raw('MAX(date) as latest_date'),
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

        $query = DB::table('customerinfos as c')
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
                'lt.latest_date',
                DB::raw('COALESCE(lt.total_debit, 0) as total_debit'),
                DB::raw('COALESCE(lt.total_credit, 0) as total_credit'),
                DB::raw('COALESCE(cnt.credit_note_credit, 0) as credit_note_credit'),
                DB::raw('(COALESCE(lt.total_debit, 0) - COALESCE(lt.total_credit, 0) - COALESCE(cnt.credit_note_credit, 0)) as total_due')
            )
            ->when(trim($this->searchTerm) !== '', function ($query) {
                $search = '%' . trim($this->searchTerm) . '%';

                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('c.name', 'like', $search)
                        ->orWhere('c.address', 'like', $search)
                        ->orWhere('c.phoneno', 'like', $search)
                        ->orWhere('c.alternate_phoneno', 'like', $search)
                        ->orWhere('c.email', 'like', $search)
                        ->orWhere('c.id', 'like', $search);
                });
            })
            ->having('total_due', '>', 0)
            ->orderByDesc('total_due')
            ->paginate(100);

        $totalDue = DB::table('customerinfos as c')
            ->leftJoinSub($ledgerTotals, 'lt', function ($join) {
                $join->on('lt.customerid', '=', 'c.id');
            })
            ->leftJoinSub($creditNoteTotals, 'cnt', function ($join) {
                $join->on('cnt.customerid', '=', 'c.id');
            })
            ->select(DB::raw('(COALESCE(lt.total_debit, 0) - COALESCE(lt.total_credit, 0) - COALESCE(cnt.credit_note_credit, 0)) as total_due'))
            ->get()
            ->sum(function ($row) {
                return max(0, (float) $row->total_due);
            });

        return view('livewire.all-customer-credit-list-livewire', [
            'customers' => $query,
            'totalDue' => $totalDue,
        ]);
    }
}
