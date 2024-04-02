<?php

namespace App\Http\Controllers;

use App\Models\company;
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
        $cus=customerledgerdetails::orderBy('id','DESC')->get(); 
        foreach($cus as $data){
            if($data->customerid){
                $cus_name = customerinfo::where('id', $data->customerid)->select('name')->first();
                if ($cus_name) {
                    $data->customerid = $cus_name->name;
                } else {
                    $data->customerid = 'Unknown'; // or any default value
                }
            }
        }
         return view('customerdetails.list',['all'=>$cus,'breadcrumb'=>$breadcrumb]);   
    }
    return redirect('/login');
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
    // Check if the user is authenticated
    if (!Auth::check()) {
        return redirect('/login')->with('error', 'You must be logged in to perform this action.');
    }

    // Validate the input data
    $validator = Validator::make($req->all(), [
        'customerid' => 'required',
        'date' => 'required',
        'amount' => 'required',
        'particulars' => 'required_without:disableFields', // Only required if disableFields is not present
        'vt' => 'required_without:disableFields', // Only required if disableFields is not present
        'cninvoiceid' => 'required_without:cninvoiceid', // Only required if disableFields is not present
    ]);

    if ($validator->fails()) {
        return redirect()->route('cpayments.create')->withErrors($validator)->withInput();
    }

    // Check if cninvoiceid and customerid exist in your database
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

    // Save the record in the customerledgerdetails table
    $nextUserId = DB::select("SHOW TABLE STATUS LIKE 'customerledgerdetails'")[0]->Auto_increment;
    
    $cl = new customerledgerdetails();
    $cl->customerid = $req->customerid;
    $cl->date = $req->date;
    $cl->particulars = $req->has('disableFields') ? "salesreturn" : ($req->particulars ?? '');
    $cl->voucher_type = $req->has('disableFields') ? "return" : ($req->vt ?? '');
    $cl->cninvoiceid = $req->cninvoiceid ?? '';
    $cl->invoicetype = "payment";
    $cl->credit = $req->amount;
    $cl->notes = $req->notes;
    $cl->added_by = session('user_email');
    $cl->save();

    return redirect()->route('cashreceipt.search', ['receiptno' => $nextUserId])->with('success', 'Invoice Created Successfully !!');
}



//foralldetailsdisplay
public function showdetails()
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