<?php

namespace App\Http\Controllers;


use App\Models\CompanyLedger;
use App\Models\company;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; //


class CompanyLedgerController extends Controller
{
   
    use WithPagination;
    public $search = '';

    public function index()
    {
        
    if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View All Customers',
            'link'=>'View All Customers'
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
            'title'=>'Company Ledgers Payment',
            'link'=>'Company Ledgers Payment'
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
   DB::table('trackcompanybillentry')->insert([
   'title' => "companyPayment_data_Inserted",
   'updated_by' => session('user_email'),
   'notes' => $additional_info,

]);
        return redirect()->route('companyLedgerspay.create')->with('success','Added Sucessfully !!'); 
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
       
        
 // Retrieve the company associated with the companyid
 $company = Company::find($all->companyid);

 // If company with given companyid exists
 if ($company) {
     // Assign the company name to $lastman
     $lastman = $company->name;
 } else {
     // Handle case where company with given companyid doesn't exist
     $lastman = null; // Or set it to a default value
 }    

        return view('companyLedgerPayment.edit',['all'=>$all,'breadcrumb'=>$breadcrumb,'companyname' => $lastman]);   
        
    }
    return redirect('/login');
}

    public function update($id, Request $req)
    {
        $validator=Validator::make($req->all(),[

            'companyid'=>'required',
            'date'=>'required',
            'particulars'=>'required',
            'amount'=>'required',
            'vt'=>'required',
    
               
        ]);
    
        if($validator->passes()){
    
        $companypanyment= CompanyLedger::find($id);
        $companypanyment->companyid=$req->companyid;
        $companypanyment->date=$req->date;
        $companypanyment->particulars=$req->particulars;
        $companypanyment->voucher_type=$req->vt;
        $companypanyment->debit=$req->amount;
        $companypanyment->notes=$req->notes;
        $companypanyment->added_by = session('user_email');
        $companypanyment->save();

           // Construct the additional_info string with old and new values

           $oldItemDetails = CompanyLedger::find($id);
           $additional_info =
           'companyid: ' . $oldItemDetails->companyid . ', ' .
           'date: ' . $oldItemDetails->date . ', ' .
           'particulars: ' . $oldItemDetails->particulars . ', ' .
           'voucher_type: ' . $oldItemDetails->voucher_type . ', ' .
           'debit: ' . $oldItemDetails->debit . ', ' .
           'notes: ' . $oldItemDetails->notes . ', ' .
           'added_by: ' . $oldItemDetails->added_by . '' .
           '<br><br>Updated to: ' .
           'companyid: ' . $req->companyid . ', ' .
           'date: ' . $req->date . ', ' .
           'particulars: ' . $req->particulars . ', ' .
           'voucher_no: ' . $req->vt . ', ' .
           'debit: ' . $req->amount . ', ' .
           'notes: ' . $req->notes . ', ' .
           'added_by: ' . session('user_email') . '';

       // Insert into track table
       DB::table('trackcompanybillentry')->insert([
           'title' => "companyPayment_data_UPDATE",
           'updated_by' => session('user_email'),
           'notes' => $additional_info,
       ]);

    
            return redirect()->route('companyLedgerspay.index')->with('success','Updated Sucessfully !!');  
        }
        else{
            return redirect()->route('companyLedgerspay.create')->withErrors($validator)->withInput();
    
        }
    
        
    

    return redirect('/login');
}

    public function destroy($id){


        $cusiddelete=CompanyLedger::findOrFail($id);

        // Log the operation before deleting
        DB::table('trackcompanybillentry')->insert([
            'title' => "companyPayment_DATA_DELETED",
            'updated_by' => session('user_email'),
            'notes' => 'Deleted companyid: ' . $cusiddelete->companyid . ', date: ' . $cusiddelete->date . ', particulars: ' . $cusiddelete->particulars . ', voucher_no: ' . $cusiddelete->voucher_no . ', credit: ' . $cusiddelete->credit . ', notes: ' . $cusiddelete->notes . ', added_by: ' . $cusiddelete->added_by,
        ]);

        $cusiddelete->delete();

         
      
  
        return redirect()->route('companyLedgerspay.index')->with('success','Customer Deleted sucessfully'); 
        
  }

}
