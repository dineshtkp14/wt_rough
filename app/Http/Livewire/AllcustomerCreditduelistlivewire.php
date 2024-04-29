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
        $allResults = $query->paginate(50);

        // Fetch additional data
        foreach ($allResults as $data) {
            if ($data->customerid) {
                $item = customerinfo::where('id', $data->customerid)->select('name', 'phoneno')->first();
                if ($item) {
                    $data->cname = $item->name;
                    $data->cphoneno = $item->phoneno;
                }
            }
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
$allResults = $query->paginate(50);

// Fetch additional data
foreach ($allResults as $data) {
    if ($data->customerid) {
        $item = CustomerInfo::where('id', $data->customerid)->select('name', 'phoneno')->first();
        if ($item) {
            $data->cname = $item->name;
            $data->cphoneno = $item->phoneno;
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
        $pdfFile = tempnam(sys_get_temp_dir(), 'credit_Due_List');
        $pdf->save($pdfFile);

        // Send headers to instruct the browser to open the PDF in a new tab
        return response()->file($pdfFile, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="credit_Due_List_report.pdf"',
        ]);
    }
        // Your existing code to fetch data and prepare it for PDFOK

}
