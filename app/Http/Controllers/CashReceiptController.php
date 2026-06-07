<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditnotesCustomerledgerdetail;
use App\Models\customerledgerdetails;
use App\Models\customerinfo;

use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class CashReceiptController extends Controller
{
    private function hasExistingCreditNoteLedgerRow($customerid, $creditNoteRow)
    {
        $creditNoteAmount = (float) ($creditNoteRow->debit ?? $creditNoteRow->credit ?? 0);

        return customerledgerdetails::where('customerid', $customerid)
            ->where(function ($query) use ($creditNoteRow, $creditNoteAmount) {
                $query->where('cninvoiceid', $creditNoteRow->invoiceid)
                    ->orWhere('returnidforcreditnotes', $creditNoteRow->invoiceid)
                    ->orWhere(function ($returnQuery) use ($creditNoteRow, $creditNoteAmount) {
                        $returnQuery->where('date', $creditNoteRow->date)
                            ->where(function ($typeQuery) {
                                $typeQuery->where('particulars', 'salesreturn')
                                    ->orWhere('particulars', 'Goods_Return')
                                    ->orWhere('voucher_type', 'return')
                                    ->orWhere('voucher_type', 'Return');
                            })
                            ->whereBetween('credit', [
                                $creditNoteAmount - 0.01,
                                $creditNoteAmount + 0.01,
                            ]);
                    });
            })
            ->exists();
    }

    private function creditNoteRowsForLedger($customerid)
    {
        return CreditnotesCustomerledgerdetail::where('customerid', $customerid)
            ->get()
            ->reject(function ($row) use ($customerid) {
                return $this->hasExistingCreditNoteLedgerRow($customerid, $row);
            });
    }

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




    $receipt = $alldetails->first();
    $debittotalsumwithdate = 0;
    $credittotalsumwithdate = 0;

    if ($receipt) {
        $querycheck = customerledgerdetails::where('customerid', $receipt->customerid)
            ->where(function($query) {
                $query->where('invoicetype', 'credit')
                    ->orWhere('invoicetype', 'payment')
                    ->orWhere('invoicetype', 'settlement');
            })
            ->get();

        $creditNoteRows = $this->creditNoteRowsForLedger($receipt->customerid);

        $debittotalsumwithdate = $querycheck->sum('debit');
        $credittotalsumwithdate = $querycheck->sum('credit') + $creditNoteRows->sum(function ($row) {
            return (float) ($row->debit ?? $row->credit ?? 0);
        });
    }
    

   
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
    $pdf = FacadePdf::setOptions(['dpi' => 150, 'defaultFont' => 'dejavu serif'])->loadHtml($pdfView);

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
