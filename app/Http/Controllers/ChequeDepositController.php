<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Chequedeposit;
use App\Models\customerinfo;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;


class ChequeDepositController extends Controller
{
    public function index()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View cheque despoit',
            'link'=>'View Suppliers/Company'
        ];

         return view('cheque_deposit.list',['breadcrumb'=>$breadcrumb]);  
    } 
         return redirect('/login');
        
       
    }
    public function create()
    {

        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Add',
            'title'=>'Add New Cheque Despoit',
            'link'=>'Add New Cheque Despoit'
        ];

        return view('cheque_deposit.create',['breadcrumb'=>$breadcrumb]);
        
    }
    return redirect('/login');

}


public function store(Request $req)
{
    $validator = $req->validate([
        'bank_name' => 'required|string',
        'cheque_date' => 'required|date',
        'date' => 'required|date',
        'customerid' => 'required',
        'amount' => 'required',

    ]);

    if ($validator) {
        $chequeinfo = new Chequedeposit();
        $chequeinfo->bank_name = $req->bank_name;
        $chequeinfo->amount = $req->amount;

        // Assuming you're using 'customer_id' instead of 'customerid'
        $chequeinfo->customerid = $req->customerid;
        $chequeinfo->cheque_date = $req->cheque_date;
        $chequeinfo->date = $req->date;
        $chequeinfo->notes = $req->notes;
        // Ensure session('user_email') contains the correct value
        $chequeinfo->added_by = session('user_email');
        $chequeinfo->save();


        return redirect()->route('chequedeposit.index')->with('success', 'Cheque deposited successfully!');
    } else {
        return redirect()->route('chequedeposit.create')->withErrors($validator)->withInput();
    }
}
public function edit($id)
{
    if (Auth::check()) {
        $breadcrumb = [
            'subtitle' => 'Edit',
            'title' => 'Edit Cheque Deposit Details',
            'link' => 'Edit Cheque Deposit Details'
        ];

        $chequedepoedit = Chequedeposit::findOrFail($id);

        return view('cheque_deposit.edit', ['chequedepoedit' => $chequedepoedit, 'breadcrumb' => $breadcrumb]);
    }

    return redirect('/login');
}

public function update($id, Request $req)
{
    if (Auth::check()) {
        $validator = Validator::make($req->all(), [
            'bank_name' => 'required|string',
            'cheque_date' => 'required|date',
            'date' => 'required|date',
            'customerid' => 'required',
            'amount' => 'required',
        ]);

        if ($validator->passes()) {
            $chequeinfo = Chequedeposit::find($id);
            $chequeinfo->bank_name = $req->bank_name;
            $chequeinfo->amount = $req->amount;
            $chequeinfo->customerid = $req->customerid;
            $chequeinfo->cheque_date = $req->cheque_date;
            $chequeinfo->date = $req->date;
            $chequeinfo->notes = $req->notes;
            $chequeinfo->added_by = session('user_email');
            $chequeinfo->save();

            return redirect()->route('chequedeposit.index')->with('success', 'Cheque Deposit Details Updated Successfully!');
        } else {
            return redirect()->route('chequedeposit.edit', $id)->withErrors($validator)->withInput();
        }
    }
    return redirect('/login');
}



public function destroy($id,Request $req){

    $chqkdepo=Chequedeposit::findOrFail($id);
    $chqkdepo->delete();

    return redirect()->route('chequedeposit.index')->with('success','Company Deleted sucessfully'); 
    

}


//chequereceiptsearchandpdf

public function returnReceiptDeyailsbyReceiptNo(Request $req)
{

    $breadcrumb = [
        'subtitle' => '',
        'title' => 'Search Receipt  No',
        'link' => 'Search Receipt No'
    ];

    $cusledgerdetails_id = $req->receiptno;
    $customerinfodetails = null;

    $alldetails = Chequedeposit::where('id', $req->receiptno)
   
    ->get();





   

    foreach ($alldetails as $data) {
        if ($data->customerid) {
            $customerinfodetails = customerinfo::where('id', $data->customerid)->get();
        }
    }

    return view('cheque_deposit.searchchequereceipt', [
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
    'title' => 'Search Receipt  No PDF',
    'link' => 'Search Receipt No PDF'
];

$cusledgerdetails_id = $req->receiptno;
$customerinfodetails = null;

$alldetails = Chequedeposit::where('id', $req->receiptno)->get();







foreach ($alldetails as $data) {
    if ($data->customerid) {
        $customerinfodetails = customerinfo::where('id', $data->customerid)->get();
    }
}


  // Load the Blade view for the PDF
  $pdfView = view('cheque_deposit.searchchequreeceiptPdf', [
    'alldetails' => $alldetails,
    'receiptno' => $cusledgerdetails_id,
    'breadcrumb' => $breadcrumb,
    'customerinfodetails' => $customerinfodetails,
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
