<?php


namespace App\Http\Livewire;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\DB;
use App\Models\customerledgerdetails;
use App\Models\customerinfo;
use Livewire\Component;
use Livewire\WithPagination;

class AllcustomerCreditduelistlivewire extends Component
{
    
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";
    public $sortBy = '';

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
        [$ledgerTotals, $creditNoteTotals] = $this->creditSummarySubqueries();
        $query = $this->buildCreditDueQuery($ledgerTotals, $creditNoteTotals);
        $this->applySearch($query);

        $totalDebitCreditDifferencewhole = $this->ledgerOnlyTotalCredit();
        
        
//forredlist
if ($this->sortBy === 'redlist') {
    $query->whereDate('lt.latest_date', '<=', now()->subYear()->format('Y-m-d'));
}

//forcredttimeexpired
// credit limit time expired customers
// credit limit time expired customers (CORRECT LOGIC)
// credit limit time expired customers (FINAL & CORRECT)
// credit limit time expired customers (FINAL & CORRECT)
if ($this->sortBy === 'credittime_expired') {
    $query->whereNotNull('lt.latest_credit_date')
        ->whereNotNull('lt.credit_limit_days')
        ->whereRaw('DATE_ADD(lt.latest_credit_date, INTERVAL lt.credit_limit_days DAY) < CURDATE()')
        ->having('debit_credit_difference', '>', 0);
}

//forshopithcredttimeexpiredonly
// FILTER: Shop + Credit Limit Time Expired



// /forshoponlytodisplay
// filter only shop customers withcredittimeexpired








// Apply sorting for debit_credit_difference if sortBy is not related to date
if ($this->sortBy !== 'date_asc' && $this->sortBy !== 'date_desc') {
if ($this->sortBy === 'asc') {
    $query->orderBy('debit_credit_difference', 'asc');
} elseif ($this->sortBy === 'desc') {
    $query->orderBy('debit_credit_difference', 'desc');
}
}

// Apply sorting for latest_date if sortBy is related to date
if ($this->sortBy === 'date_asc') {
$query->orderBy('latest_date', 'asc');
} elseif ($this->sortBy === 'date_desc') {
$query->orderBy('latest_date', 'desc');
}



// FILTER: Shop + Credit Limit Time Expired
if ($this->sortBy === 'shop_credit_expired') {
    $query->where('c.type', 'shop')
        ->whereNotNull('lt.latest_credit_date')
        ->whereNotNull('lt.credit_limit_days')
        ->whereRaw('DATE_ADD(lt.latest_credit_date, INTERVAL lt.credit_limit_days DAY) < CURDATE()')
        ->having('debit_credit_difference', '>', 0);
}

        // FILTER: Shop only (customerinfo.type = shop)
if ($this->sortBy === 'shop') {
    $query->where('c.type', 'shop');
}

// Calculate total negative debit-credit difference for display
$totalNegativeDebitCreditDifference = (clone $query)->get()->filter(function ($item) {
return $item->debit_credit_difference < 0;
})->sum('debit_credit_difference');



        // Paginate the results
        $allResults = $query->paginate(1000);

    


        // Calculate total debit-credit difference for display (considering only positive values)
$totalDebitCreditDifference = $allResults->filter(function ($item) {
return $item->debit_credit_difference >= 0; // Only consider positive or zero values
})->sum('debit_credit_difference');



     

        return view('livewire.allcustomer-creditduelistlivewire', [
            'all' => $allResults,
            'totalDebitCreditDifference' => $totalDebitCreditDifference,
            'totalDebitCreditDifferencewhole' => $totalDebitCreditDifferencewhole,
            'totalNegativeDebitCreditDifference' => $totalNegativeDebitCreditDifference
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

    private function buildCreditDueQuery($ledgerTotals, $creditNoteTotals)
    {
        return DB::table('customerinfos as c')
            ->leftJoinSub($ledgerTotals, 'lt', function ($join) {
                $join->on('lt.customerid', '=', 'c.id');
            })
            ->leftJoinSub($creditNoteTotals, 'cnt', function ($join) {
                $join->on('cnt.customerid', '=', 'c.id');
            })
            ->select(
                'c.id as customerid',
                'c.name as cname',
                'c.phoneno as cphoneno',
                'c.address',
                'c.type as ctype',
                'lt.latest_date',
                'lt.latest_credit_date',
                'lt.credit_limit_days',
                DB::raw('COALESCE(lt.total_debit, 0) as total_debit'),
                DB::raw('COALESCE(lt.total_credit, 0) as total_credit'),
                DB::raw('COALESCE(cnt.credit_note_credit, 0) as credit_note_credit'),
                DB::raw('(COALESCE(lt.total_debit, 0) - COALESCE(lt.total_credit, 0) - COALESCE(cnt.credit_note_credit, 0)) as debit_credit_difference')
            )
            ->havingRaw('ABS(debit_credit_difference) > 0.004');
    }

    private function applySearch($query)
    {
        if (trim($this->searchTerm) === '') {
            return;
        }

        $search = "%" . trim($this->searchTerm) . "%";

        $query->where(function ($searchQuery) use ($search) {
            $searchQuery->where('c.id', 'like', $search)
                ->orWhere('c.name', 'like', $search)
                ->orWhere('c.phoneno', 'like', $search)
                ->orWhere('c.alternate_phoneno', 'like', $search)
                ->orWhere('c.address', 'like', $search);
        });
    }

    private function ledgerOnlyTotalCredit()
    {
        $query = customerledgerdetails::select(
            'customerid',
            DB::raw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) AS debit_credit_difference')
        )
            ->whereIn('invoicetype', ['credit', 'payment'])
            ->groupBy('customerid');

        return $query->get()->reduce(function ($carry, $item) {
            return $carry + max(0, $item->debit_credit_difference);
        }, 0);
    }







    public function generateallcustomerPDF()

    
    {



[$ledgerTotals, $creditNoteTotals] = $this->creditSummarySubqueries();
$query = $this->buildCreditDueQuery($ledgerTotals, $creditNoteTotals);
$this->applySearch($query);

$totalDebitCreditDifferencewhole = $this->ledgerOnlyTotalCredit();


if ($this->sortBy === 'redlist') {
    $query->whereDate('lt.latest_date', '<=', now()->subYear()->format('Y-m-d'));
}

if ($this->sortBy === 'credittime_expired') {
    $query->whereNotNull('lt.latest_credit_date')
        ->whereNotNull('lt.credit_limit_days')
        ->whereRaw('DATE_ADD(lt.latest_credit_date, INTERVAL lt.credit_limit_days DAY) < CURDATE()')
        ->having('debit_credit_difference', '>', 0);
}

if ($this->sortBy === 'shop_credit_expired') {
    $query->where('c.type', 'shop')
        ->whereNotNull('lt.latest_credit_date')
        ->whereNotNull('lt.credit_limit_days')
        ->whereRaw('DATE_ADD(lt.latest_credit_date, INTERVAL lt.credit_limit_days DAY) < CURDATE()')
        ->having('debit_credit_difference', '>', 0);
}

if ($this->sortBy === 'shop') {
    $query->where('c.type', 'shop');
}

// Apply sorting for debit_credit_difference if sortBy is not related to date
if ($this->sortBy !== 'date_asc' && $this->sortBy !== 'date_desc') {
if ($this->sortBy === 'asc') {
    $query->orderBy('debit_credit_difference', 'asc');
} elseif ($this->sortBy === 'desc') {
    $query->orderBy('debit_credit_difference', 'desc');
}
}

// Apply sorting for latest_date if sortBy is related to date
if ($this->sortBy === 'date_asc') {
$query->orderBy('latest_date', 'asc');
} elseif ($this->sortBy === 'date_desc') {
$query->orderBy('latest_date', 'desc');
}




// Paginate the results
$allResults = $query->paginate(1000);

// Calculate total negative debit-credit difference for display
$totalNegativeDebitCreditDifference = $allResults->filter(function ($item) {
    return $item->debit_credit_difference < 0;
})->sum('debit_credit_difference');



    // Calculate total debit-credit difference for display (considering only positive values)
$totalDebitCreditDifference = $allResults->filter(function ($item) {
return $item->debit_credit_difference >= 0; // Only consider positive or zero values
})->sum('debit_credit_difference');




        $pdfView = view('allsalesdetails.allcustomercreditlistpdf', [
            'all' => $allResults,
            'totalDebitCreditDifference' => $totalDebitCreditDifference,
            'totalDebitCreditDifferencewhole' => $totalDebitCreditDifferencewhole,
            'totalNegativeDebitCreditDifference' => $totalNegativeDebitCreditDifference

    ]);
        // Generate PDF using FacadePdf
        $pdf = FacadePdf::setOptions(['dpi' => 150, 'defaultFont' => 'dejavu serif'])->loadHtml($pdfView);

        // Save the PDF to a temporary file
        $pdfFile = tempnam(sys_get_temp_dir(),'credit_Due_List');
        $pdf->save($pdfFile);

                // Get today's date in the format YYYY-MM-DD
            // $todayDate = date('Y-m-d');
            $todayDate =  date('Y-m-d_H.i.s');


            // Create a filename with today's date
            $filename = "credit_report_{$todayDate}.pdf";

        // Send headers to instruct the browser to open the PDF in a new tab
        return response()->file($pdfFile, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",

            // 'Content-Disposition' => 'inline; filename="rxxxcredit_Due_List_report.pdf"',
        ]);
    }
        // Your existing code to fetch data and prepare it for PDF

}
