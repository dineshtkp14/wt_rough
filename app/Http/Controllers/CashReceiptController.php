<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customerledgerdetails;
use App\Models\customerinfo;

use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class CashReceiptController extends Controller
{

    public function returnReceiptDeyailsbyReceiptNo(Request $req)
    {

        $breadcrumb = [
            'subtitle' => '',
            'title' => 'Search Receipt  No',
            'link' => 'Search Receipt No'
        ];
    
        $cusledgerdetails_id = $req->receiptno;
        $customerinfodetails = null;

        $alldetails = customerledgerdetails::where('id', $req->receiptno)
        ->where('invoicetype', 'payment')
        ->get();




    
       
    
        foreach ($alldetails as $data) {
            if ($data->customerid) {
                $customerinfodetails = customerinfo::where('id', $data->customerid)->get();
            }
        }
    
        return view('cash_receipt.searchcashreceipt', [
            'alldetails' => $alldetails,
            'receiptno' => $cusledgerdetails_id,
            'breadcrumb' => $breadcrumb,
            'customerinfodetails' => $customerinfodetails,

        ]);
    }






    public function returnReceiptDeyailsbyReceiptNoPDF(Request $req)
    {


    $breadcrumb = [
        'subtitle' => '',
        'title' => 'Search Receipt  No PDF test',
        'link' => 'Search Receipt No PDF'
    ];

    $cusledgerdetails_id = $req->receiptno;
    $customerinfodetails = null;
    $alldetails = customerledgerdetails::where('id', $req->receiptno)
    ->where('invoicetype', 'payment')
    ->get();

    
   

    foreach ($alldetails as $data) {
        if ($data->customerid) {
            $customerinfodetails = customerinfo::where('id', $data->customerid)->get();
        }
    }




// stratfortotaldueamount
$cusledgertails = Customerledgerdetails::where('customerid', $data->customerid)
->where(function($query) {
    $query->where('invoicetype', 'credit')
          ->orWhere('invoicetype', 'payment');
})
->get();
$querycheck = customerledgerdetails::where('customerid', $data->customerid)
->where(function($query) {
    $query->where('invoicetype', 'credit')
          ->orWhere('invoicetype', 'payment');
})
->get();
$debittotalsumwithdate = $querycheck->sum('debit');
$credittotalsumwithdate = $querycheck->sum('credit');

// $xd = customerinfo::where('id', $data->customerid)->get();
// $afn = $xd;

//end for totaldueamount
    

   
      // Load the Blade view for the PDF
      $pdfView = view('cash_receipt.searchcashreceiptPdf', [
        'alldetails' => $alldetails,
        'receiptno' => $cusledgerdetails_id,
        'breadcrumb' => $breadcrumb,
        'customerinfodetails' => $customerinfodetails,
        'dts' => $debittotalsumwithdate,
        'cts' => $credittotalsumwithdate,
    ]);

    // Generate PDF using FacadePdf
    $pdf = FacadePdf::setOptions(['dpi' => 150, 'defaultFont' => 'noto'])->loadHtml($pdfView);

    // Save the PDF to a temporary file
    $pdfFile = tempnam(sys_get_temp_dir(), 'invoice');
    $pdf->save($pdfFile);

    // Send headers to instruct the browser to open the PDF in a new tab
    return response()->file($pdfFile, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="chareceipt.pdf"',
    ]);
}
}