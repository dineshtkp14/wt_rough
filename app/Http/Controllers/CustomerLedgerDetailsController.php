<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Models\TrackCustomerLedger;

use App\Models\customerledgerdetails;

use Illuminate\Support\Facades\DB; //

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\customerinfo;
use Illuminate\Support\Facades\Auth;
use App\Models\CreditnotesInvoice;




class CustomerLedgerDetailsController extends Controller

{
    public function index(Request $req)
    {

        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Payments',
            'title'=>'All Customers Payment Histrory',
            'link'=>'All Customers Payment Histrory'
        ];
       
 // $cus = customerledgerdetails::where('invoicetype', 'payment')
        // ->orderBy('id', 'DESC');
        // $cus=customerledgerdetails::orderBy('id','DESC')->get(); 
        // foreach($cus as $data){
        //     if($data->customerid){
        //         $cus_name = customerinfo::where('id', $data->customerid)->select('name')->first();
        //         if ($cus_name) {
        //             $data->customerid = $cus_name->name;
        //         } else {
        //             $data->customerid = 'Unknown'; // or any default value
        //         }
        //     }
        }
         return view('customerdetails.list',['breadcrumb'=>$breadcrumb]);   
    
}

    public function create()
    
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Payments',
            'title'=>' Customers Ledger Payment',
            'link'=>'Customers Ledger Payment'
        ];
        $cus=customerinfo::orderBy('id','DESC')->get();

        $statement  = DB::select("SHOW TABLE STATUS LIKE 'customerledgerdetails'");
        $nextUserId = $statement[0]->Auto_increment;       
        return view('customerdetails.create',['all'=>$cus,'breadcrumb'=>$breadcrumb,'nextUserId'=>$nextUserId]);   ;
    }
    return redirect('/login');
}

public function store(Request $req)
{
    // Validate the input data
    $validator = Validator::make($req->all(), [
        'customerid' => 'required',
        'date' => 'required',
        'amount' => 'required',
        'particulars' => 'required_without:disableFields', // Only required if disableFields is not present
        'vt' => 'required_without:disableFields', // Only required if disableFields is not present
        // 'cninvoiceid' => 'required_without:disableFields', // Only required if disableFields is not present
    ]);

    if ($validator->fails()) {
        return redirect()->route('cpayments.create')->withErrors($validator)->withInput();
    }

    if ($req->has('disableFields')) { // Replace 'your_checkbox_name' with the name of your checkbox input
        // Check if cninvoiceid and customerid exist in your database
        if (empty($req->cninvoiceid)) {
            return redirect()->route('cpayments.create')->with('error', 'Please enter a value for Credit Notes Invoice ID.');
        }
       
        $cnInvoice = CreditNotesInvoice::where([
            ['id', $req->cninvoiceid],
            ['customerid', $req->customerid]
        ])->first();


        if (!$cnInvoice) {
            return redirect()->route('cpayments.create')->with('error', 'Credit Notes Invoice ID not found.');
        }

        // Validate the amount
        if ($req->amount != $cnInvoice->total) {
            return redirect()->route('cpayments.create')->with('error', 'Amount does not match the amount in the Credit Notes Invoice.');
        }

        // Check if cnvoiceid already exists in customerledgerdetails table
        $existingCnvoiceId = customerledgerdetails::where('cninvoiceid', $req->cninvoiceid)->exists();
        if ($existingCnvoiceId) {
            return redirect()->route('cpayments.create')->with('error', 'This Credit Notes Invoice ID has already been inserted.');
        }
    }

    // Save the record in the customerledgerdetails table
    $nextUserId = DB::select("SHOW TABLE STATUS LIKE 'customerledgerdetails'")[0]->Auto_increment;
    
    $cl = new customerledgerdetails();
    $cl->customerid = $req->customerid;
    $cl->date = $req->date;
    $cl->particulars = $req->has('disableFields') ? "salesreturn" : ($req->particulars ?? '');
    $cl->voucher_type = $req->has('disableFields') ? "return" : ($req->vt ?? '');
    $cl->cninvoiceid = $req->cninvoiceid ?? null;
    $cl->invoicetype = "payment";
    $cl->credit = $req->amount;
    $cl->notes = $req->notes;
    $cl->added_by = session('user_email');
    $cl->save();


    $notes = 'Customer ID ' . $req->customerid . ' inserted with particulars: ' . $cl->particulars . ', voucher type: ' . $cl->voucher_type . ', credit: ' . $cl->credit .', date: ' . $cl->date . ', by ' . session('user_email');;

   // Insert into track table
     // Insert into track table
     TrackCustomerLedger::create([
    // 'customerid' => $req->customerid,
    'title' => "Inserted_Payment",
    'updated_by' => session('user_email'),
    'notes' => $notes

]);



    return redirect()->route('cashreceipt.search', ['receiptno' => $nextUserId])->with('success', 'Invoice Created Successfully !!');
}

public function edit($id)

{
    if(Auth::check()){
    $breadcrumb= [
        'subtitle'=>'Edit',
        'title'=>'Edit Customer Payement',
        'link'=>'Edit Customers Payment'
    ];

    $distrinutors=customerledgerdetails::findOrfail($id);

    return view('customerdetails.edit',['payment'=>$distrinutors,'breadcrumb'=>$breadcrumb]);   
    
}
}
public function update(Request $req, $id)
{
    // Validate the input data
    $validator = Validator::make($req->all(), [
        'customerid' => 'required',
        'date' => 'required',
        'amount' => 'required',
        'particulars' => 'required_without:disableFields', // Only required if disableFields is not present
        'vt' => 'required_without:disableFields', // Only required if disableFields is not present
        // 'cninvoiceid' => 'required_without:disableFields', // Only required if disableFields is not present
    ]);

    if ($validator->fails()) {
        return redirect()->route('cpayments.edit', $id)->withErrors($validator)->withInput();
    }

    if ($req->has('disableFields')) { // Replace 'your_checkbox_name' with the name of your checkbox input
        // Check if cninvoiceid and customerid exist in your database
        if (empty($req->cninvoiceid)) {
            return redirect()->route('cpayments.edit', $id)->with('error', 'Please enter a value for Credit Notes Invoice ID.');
        }
       
        $cnInvoice = CreditNotesInvoice::where([
            ['id', $req->cninvoiceid],
            ['customerid', $req->customerid]
        ])->first();


        if (!$cnInvoice) {
            return redirect()->route('cpayments.edit', $id)->with('error', 'Credit Notes Invoice ID not found.');
        }

        // Validate the amount
        if ($req->amount != $cnInvoice->total) {
            return redirect()->route('cpayments.edit', $id)->with('error', 'Amount does not match the amount in the Credit Notes Invoice.');
        }

        // Check if cnvoiceid already exists in customerledgerdetails table
        $existingCnvoiceId = customerledgerdetails::where('cninvoiceid', $req->cninvoiceid)->where('id', '!=', $id)->exists();
        if ($existingCnvoiceId) {
            return redirect()->route('cpayments.edit', $id)->with('error', 'This Credit Notes Invoice ID has already been inserted.');
        }
    }

    // Retrieve the record to be updated
    $cl = customerledgerdetails::findOrFail($id);
    
    // Update record fields
    $cl->customerid = $req->customerid;
    $cl->date = $req->date;
    $cl->particulars = $req->has('disableFields') ? "salesreturn" : ($req->particulars ?? '');
    $cl->voucher_type = $req->has('disableFields') ? "return" : ($req->vt ?? '');
    $cl->cninvoiceid = $req->cninvoiceid ?? null;
    $cl->credit = $req->amount;
    $cl->notes = $req->notes;
    $cl->added_by = session('user_email');
    $cl->save();
    dd("success");

    return redirect()->route('cashreceipt.search', ['receiptno' => $id])->with('success', 'Invoice Updated Successfully !!');
}

public function destroy($id, Request $req)
{
    // Find the record to be deleted
    $cusiddelete = customerledgerdetails::findOrFail($id);

    // Store values for insertion into TrackCustomerLedger
    $title = "Deleted_CustomerPayment";
    $updated_by = session('user_email');

    $notes = 'Record ID: ' . $cusiddelete->id .
    ', Customer ID: ' . $cusiddelete->customerid .
    ', Date: ' . $cusiddelete->date .
    ', Particulars: ' . $cusiddelete->particulars .
    ', Voucher Type: ' . $cusiddelete->voucher_type .
    ', Credit: ' . $cusiddelete->credit .
    ', Deleted by: ' . $updated_by;
    // Check if the invoice type is "payment"
    if ($cusiddelete->invoicetype === 'payment') {
        // Delete the record
        $cusiddelete->delete();

        // Insert into track table
        TrackCustomerLedger::create([
            'title' => $title,
            'updated_by' => $updated_by,
            'notes' => $notes
        ]);

        return redirect()->route('cpayments.index')->with('success', 'Customer Payment Receipt Deleted successfully.');
    } else {
        // If invoice type is not "payment", return with an error message
        return redirect()->route('cpayments.index')->with('error', 'Cannot delete this record as invoice type is not "payment".');
    }
}





//foralldetailsdisplay
public function showdetails()
{
    if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'View ',
            'title'=>'Today sales',
            'link'=>'View All Details'
        ];

      
         $cus=customerledgerdetails::orderBy('id','DESC')->get();

         
         return view('allsalesdetails.index',['all'=>$cus,'breadcrumb'=>$breadcrumb]);
}
}


public function showtodaysalesdetailsforpdf()
{
    if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'View ',
            'title'=>'All Details',
            'link'=>'View All Details'
        ];

      
         $cus=customerledgerdetails::orderBy('id','DESC')->get();

         
         return view('allsalesdetails.index',['all'=>$cus,'breadcrumb'=>$breadcrumb]);
}

}
//foralldetailsdisplay
public function showallcuscreditdetails()
{
    if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'View ',
            'title'=>'All Customer Credit Details List',
            'link'=>'View Customer Credit Details List'
        ];

      
       
       // $query=customerledgerdetails::orderBy('id','DESC')->get();
       
        return view('allsalesdetails.allcustomercreditlist', [ 'breadcrumb' => $breadcrumb]);
        

}
}
}