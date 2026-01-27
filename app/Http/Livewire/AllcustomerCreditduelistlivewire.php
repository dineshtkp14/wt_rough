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

    public function render()
    {

        
        // Main query to get individual customer data
        $query = customerledgerdetails::select(
            'customerid',
            // 'date', // Assuming 'date' is the field containing the date
            \DB::raw('MAX(date) as latest_date'),
            \DB::raw('SUM(debit) AS total_debit'),
            \DB::raw('MAX(credit_limit_days) as credit_limit_days'),

            \DB::raw('COALESCE(SUM(credit), 0) AS total_credit'),
            \DB::raw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) AS debit_credit_difference'
            ))
            ->where('invoicetype', 'credit')
            ->orWhere('invoicetype', 'payment')
            ->groupBy('customerid'); // Group by date field as well

            // ->groupBy('customerid', 'date'); // Group by date field as well
            // ->orderBy('date', 'asc') // Order by date in descending order
            // ->limit(1);

        // Apply search conditions
        if (!empty($this->searchTerm)) {
            $query->where(function ($query) {
                $query->where('customerid', 'like', "%" . $this->searchTerm . "%")
                    ->orWhereHas('customerinfo', function ($subQuery) {
                        $subQuery->where('name', 'like', "%" . $this->searchTerm . "%");
                    });
            });
        }

       



        $totalDebitCreditDifferencewhole = $query->get()->reduce(function ($carry, $item) {
            return $carry + max(0, $item->debit_credit_difference);
        }, 0);
        
        
//forredlist
if ($this->sortBy === 'redlist') {
    $query->havingRaw('MAX(date) <= ?', [now()->subYear()->format('Y-m-d')]);
}

//forcredttimeexpired
// credit limit time expired customers
// credit limit time expired customers (CORRECT LOGIC)
// credit limit time expired customers (FINAL & CORRECT)
// credit limit time expired customers (FINAL & CORRECT)
if ($this->sortBy === 'credittime_expired') {
    $query->havingRaw(
        '
        DATE_ADD(
            MAX(CASE WHEN invoicetype = "credit" THEN date END),
            INTERVAL 
            MAX(
                CASE 
                    WHEN invoicetype = "credit" AND credit_limit_days IS NOT NULL 
                    THEN credit_limit_days 
                END
            ) DAY
        ) < CURDATE()
        '
    )
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



// Calculate total negative debit-credit difference for display
$totalNegativeDebitCreditDifference = $query->get()->filter(function ($item) {
return $item->debit_credit_difference < 0;
})->sum('debit_credit_difference');



        // Paginate the results
        $allResults = $query->paginate(1000);

        // Fetch additional data
        foreach ($allResults as $data) {
            if ($data->customerid) {
                $item = customerinfo::where('id', $data->customerid)->select('name', 'phoneno','type')->first();
                if ($item) {
                    $data->cname = $item->name;
                    $data->cphoneno = $item->phoneno;
                    $data->ctype = $item->type;

                }

                
            }
        }


        // FILTER: Shop + Credit Limit Time Expired
if ($this->sortBy === 'shop_credit_expired') {
    $allResults->setCollection(
        $allResults->getCollection()->filter(function ($row) {

            if (!isset($row->ctype) || $row->ctype !== 'shop') {
                return false;
            }

            if (empty($row->latest_date) || empty($row->credit_limit_days)) {
                return false;
            }

            $expiryDate = \Carbon\Carbon::parse($row->latest_date)
                ->addDays($row->credit_limit_days);

            return $expiryDate->lt(now()) && $row->debit_credit_difference > 0;
        })
    );
}

        // FILTER: Shop only (customerinfo.type = shop)
if ($this->sortBy === 'shop') {
    $allResults->setCollection(
        $allResults->getCollection()->filter(function ($row) {
            return isset($row->ctype) && $row->ctype === 'shop';
        })
    );
}


    


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







    public function generateallcustomerPDF()

    
    {



// Main query to get individual customer data
$query = customerledgerdetails::select(
    'customerid',
    \DB::raw('MAX(date) as latest_date'),
    \DB::raw('SUM(debit) AS total_debit'),
    \DB::raw('COALESCE(SUM(credit), 0) AS total_credit'),
    \DB::raw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) AS debit_credit_difference'
    ))
    ->where('invoicetype', 'credit')
    ->orWhere('invoicetype', 'payment')
    ->groupBy('customerid');

// Apply search conditions
if (!empty($this->searchTerm)) {
    $query->where(function ($query) {
        $query->where('customerid', 'like', "%" . $this->searchTerm . "%")
            ->orWhereHas('customerinfo', function ($subQuery) {
                $subQuery->where('name', 'like', "%" . $this->searchTerm . "%");
            });
    });
}

// Calculate total debit-credit difference whole
// $totalDebitCreditDifferencewhole = $query->get()->sum('debit_credit_difference');

$totalDebitCreditDifferencewhole = $query->get()->reduce(function ($carry, $item) {
    return $carry + max(0, $item->debit_credit_difference);
}, 0);


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

// Fetch additional data
foreach ($allResults as $data) {
    if ($data->customerid) {
        $item = CustomerInfo::where('id', $data->customerid)->select('name', 'phoneno','address')->first();
        if ($item) {
            $data->cname = $item->name;
            $data->cphoneno = $item->phoneno;
            $data->address = $item->address;

        }
    }
}

// // Calculate total debit-credit difference for display
// $totalDebitCreditDifference = $allResults->sum('debit_credit_difference');

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
