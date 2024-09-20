<?php

namespace App\Http\Livewire;

use App\Models\customerledgerdetails;
use App\Models\customerinfo;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

use Livewire\Component;
use Livewire\WithPagination;


class Allsalesdetailslivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm = "";

 
    public function render()
    {
        $cus = customerledgerdetails::orderBy('id', 'DESC')->select('*');
    
        if (!empty($this->searchTerm)) {
            $cus->orWhere('invoiceid', 'like', "%" . $this->searchTerm . "%");
            $cus->orWhere('particulars', 'like', "%" . $this->searchTerm . "%");
            $cus->orWhere('voucher_type', 'like', "%" . $this->searchTerm . "%");
            $cus->orWhere('date', 'like', "%" . $this->searchTerm . "%");
            $cus->orWhere('debit', 'like', "%" . $this->searchTerm . "%");
            $cus->orWhere('credit', 'like', "%" . $this->searchTerm . "%");
            $cus->orWhere('invoicetype', 'like', "%" . $this->searchTerm . "%");
            $cus->orWhere('notes', 'like', "%" . $this->searchTerm . "%");
    
            // Use whereHas to search by name in the customerinfo table
            $cus->orWhereHas('customerinfo', function ($query) {
                $query->where('name', 'like', "%" . $this->searchTerm . "%");
            });
        }
    
        // Get the results after applying the conditions
        $results = $cus->paginate(50);
    
        // Iterate over the results and fetch related data
        foreach ($results as $data) {
            if ($data->customerid) {
                $item = customerinfo::where('id', $data->customerid)->select('name')->first();
                if ($item) {
                    $data->cname = $item->name;
                }
            }
        }
    
        return view('livewire.allsalesdetailslivewire', ['all' => $results]);
    }

    public function generateTodaysalesdetailsPDF()
{
    // Get today's date
    $today = date('Y-m-d');

    // Query to fetch today's records
    $cus = customerledgerdetails::whereDate('date', $today)
        ->orderBy('id', 'DESC')
        ->select('*');

    // Apply search term if provided
    if (!empty($this->searchTerm)) {
        $cus->where(function ($query) {
            $query->where('invoiceid', 'like', "%" . $this->searchTerm . "%")
                  ->orWhere('particulars', 'like', "%" . $this->searchTerm . "%")
                  ->orWhere('voucher_type', 'like', "%" . $this->searchTerm . "%")
                  ->orWhere('debit', 'like', "%" . $this->searchTerm . "%")
                  ->orWhere('credit', 'like', "%" . $this->searchTerm . "%")
                  ->orWhere('invoicetype', 'like', "%" . $this->searchTerm . "%")
                  ->orWhere('notes', 'like', "%" . $this->searchTerm . "%")
                  ->orWhereHas('customerinfo', function ($query) {
                      $query->where('name', 'like', "%" . $this->searchTerm . "%");
                  });
        });
    }

    // Get the results after applying the conditions
    $results = $cus->get(); // Use get() since we're not paginating for the PDF

    // Iterate over the results and fetch related data
    foreach ($results as $data) {
        if ($data->customerid) {
            $item = customerinfo::where('id', $data->customerid)->select('name')->first();
            if ($item) {
                $data->cname = $item->name;
            }
        }
    }

    // Prepare the view for PDF
    $pdfView = view('allsalesdetails.todaysalesdetails', [
        'all' => $results,
    ])->render();

    // Generate PDF using FacadePdf
    $pdf = FacadePdf::setOptions(['dpi' => 150, 'defaultFont' => 'dejavu serif'])->loadHtml($pdfView);

    // Save the PDF to a temporary file
    $pdfFile = tempnam(sys_get_temp_dir(), 'TodaySalesList');
    $pdf->save($pdfFile);

    // Create a filename with today's date
    $todayDate = date('Y-m-d_H.i.s');
    $filename = "TodaySalesList{$todayDate}.pdf";

    // Send headers to instruct the browser to open the PDF in a new tab
    return response()->file($pdfFile, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => "inline; filename=\"{$filename}\"",
    ]);
}

}
    
