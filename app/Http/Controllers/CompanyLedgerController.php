<?php

namespace App\Http\Controllers;


use App\Models\CompanyLedger;
use App\Models\TrackCompanyBillEntry;

use App\Models\company;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; //

//class
class CompanyLedgerController extends Controller
{
   
    use WithPagination;
    public $search = '';

    public function index()
    {
        
    if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View Company Payment',
            'link'=>'View Company Payment'
        ];
   

     return view('companyLedgerPayment.list',['breadcrumb'=>$breadcrumb]);   

    }
    return redirect('/login');
 }
    

    public function create()
    {

        
    if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Payment',
            'title'=>'Make  Company  Payment',
            'link'=>'Make Company  Payment'
        ]; 
     
        return view('companyLedgerPayment.create',['breadcrumb'=>$breadcrumb]);   
    }
    return redirect('/login');
 }



    public function store(Request $req)
   {
    
    if(Auth::check()){
    $validator=Validator::make($req->all(),[
        'companyid'=>'required',
        'date'=>'required',
        'particulars'=>'required',
        'amount'=>'required',
        'vt'=>'required',

           
     ]);

    if($validator->passes()){

        $companypanyment=new CompanyLedger();
        $companypanyment->companyid=$req->companyid;
        $companypanyment->date=$req->date;
        $companypanyment->particulars=$req->particulars;
        $companypanyment->voucher_type=$req->vt;
        $companypanyment->debit=$req->amount;

        $companypanyment->notes=$req->notes;
        $companypanyment->added_by = session('user_email');

        $companypanyment->save();

         // Construct the additional_info string with old and new values
         $additional_info = 
         'companyid: ' . $req->companyid . ', ' .
         'date: ' . $req->date . ', ' .
         'particulars: ' . $req->particulars . ', ' .
         'voucher_type: ' . $req->vt . ', ' .
         'debit: ' . $req->amount . ', ' .
         'notes: ' . $req->notes . ', ' .
         'added_by: ' . session('user_email') . '';


       // Insert into track table
       TrackCompanyBillEntry::create([
        'title' => "companyPayment_data_Inserted",
   'updated_by' => session('user_email'),
   'notes' => $additional_info,

]);
session()->put('lastInsertedId', $companypanyment->id);

        return redirect()->route('companyLedgerspay.index')->with('success','Added Sucessfully !!'); 
    }

    else{
        return redirect()->route('companyLedgerspay.create')->withErrors($validator)->withInput();

    }

   }
   return redirect('/login');
}
   
   public function edit($id)

    {
        
    if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Edit',
            'title'=>'Edit COMPANY LEDGER PAYMENT',
            'link'=>'Edit COMPANY LEDGER PAYMENT'
        ];
   
        $all=CompanyLedger::findOrfail($id);
      


  // Fetch the company name if company id exists
  $companyName = null;
  if ($all->companyid) {
      $companyName = Company::where('id', $all->companyid)->value('name');
  } 

        return view('companyLedgerPayment.edit',['all'=>$all,'breadcrumb'=>$breadcrumb,'companyName' => $companyName]);   
        
    }
    return redirect('/login');
}

public function update($id, Request $req)
{
    $validator = Validator::make($req->all(), [
        'companyid' => 'required',
        'date' => 'required',
        'particulars' => 'required',
        'amount' => 'required',
        'vt' => 'required',
    ]);

    if ($validator->passes()) {
        $companyPayment = CompanyLedger::find($id);
        $oldData = $companyPayment->toArray(); // Get old data as an array

        $companyPayment->companyid = $req->companyid;
        $companyPayment->date = $req->date;
        $companyPayment->particulars = $req->particulars;
        $companyPayment->voucher_type = $req->vt;
        $companyPayment->debit = $req->amount;
        $companyPayment->notes = $req->notes;
        $companyPayment->added_by = session('user_email');
        $companyPayment->save();

        // Construct the additional_info string with old and new values
        $additionalInfo = "Old Data: ";
        foreach ($oldData as $key => $value) {
            // Skip voucher_no and debit fields
            if ($key === 'voucher_no' || $key === 'credit') {
                continue;
            }
            $additionalInfo .= "$key: $value || ";
        }
        $additionalInfo .= "<br><br>"; // Add a line break after "Old Data"

        $additionalInfo .= "Updated to: ";
        foreach ($companyPayment->toArray() as $key => $value) {
            // Skip voucher_no and debit fields
            if ($key === 'voucher_no' || $key === 'credit') {
                continue;
            }
            $additionalInfo .= "$key: $value || ";
        }
        $additionalInfo .= "<br><br>"; // Add a line break after "Updated to:"


        // Insert into track table
        TrackCompanyBillEntry::create([

            'title' => "companyPayment_data_UPDATE",
            'updated_by' => session('user_email'),
            'notes' => $additionalInfo,
        ]);

        return redirect()->route('companyLedgerspay.index')->with('success', 'Updated Successfully !!');
    } else {
        return redirect()->route('companyLedgerspay.edit', ['companyLedgerspay' => $id])->withErrors($validator)->withInput();

    }
}



    public function destroy($id){


        $cusiddelete=CompanyLedger::findOrFail($id);

        // Log the operation before deleting ok
        TrackCompanyBillEntry::create([

            'title' => "companyPayment_DATA_DELETED",
            'updated_by' => session('user_email'),
            'notes' => 'Deleted companyid: ' . $cusiddelete->companyid . ', date: ' . $cusiddelete->date . ', particulars: ' . $cusiddelete->particulars . ', voucher_type: ' . $cusiddelete->voucher_type . ', debit: ' . $cusiddelete->debit . ', notes: ' . $cusiddelete->notes . ', added_by: ' . $cusiddelete->added_by,
        ]);

        $cusiddelete->delete();

         
      
  
        return redirect()->route('companyLedgerspay.index')->with('success',' Deleted sucessfully'); 
        
  }

}
