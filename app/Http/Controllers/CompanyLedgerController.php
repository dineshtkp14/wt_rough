<?php

namespace App\Http\Controllers;


use App\Models\CompanyLedger;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Validator;

class CompanyLedgerController extends Controller
{
   
    use WithPagination;
    public $search = '';

    public function index()
    {
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View All Customers',
            'link'=>'View All Customers'
        ];
   

     return view('companyLedgerPayment.list',['breadcrumb'=>$breadcrumb]);   

    }

    

    public function create()
    {

        $breadcrumb= [
            'subtitle'=>'Payment',
            'title'=>'Company Ledgers Payment',
            'link'=>'Company Ledgers Payment'
        ]; 
     
        return view('companyLedgerPayment.create',['breadcrumb'=>$breadcrumb]);   
    }




    public function store(Request $req)
   {
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
        $companypanyment->save();

        return redirect()->route('companyLedgers.index')->with('success','Customer Added Sucessfully !!');  
    }

    else{
        return redirect()->route('companyLedgers.create')->withErrors($validator)->withInput();

    }

   }
   
   public function edit($id)

    {
        $breadcrumb= [
            'subtitle'=>'Edit',
            'title'=>'Edit Customers Details',
            'link'=>'Edit Customers Details'
        ];
   
        $customers=CompanyLedger::findOrfail($id);

        return view('companyLedgerPayment.edit',['cus'=>$customers,'breadcrumb'=>$breadcrumb]);   
        
    }

    public function update($id, Request $req)
    {
        $validator=Validator::make($req->all(),[

            'name'=>'required',
            'address'=>'required',
            'phoneno'=>'required', 
           
               
        ]);
    
        if($validator->passes()){
    
            $cusinfo= CompanyLedger::find($id);
            $cusinfo->name=$req->name;
            $cusinfo->address=$req->address;
            $cusinfo->email=$req->email;
            $cusinfo->phoneno=$req->phoneno;
            $cusinfo->remarks=$req->remarks;
            $cusinfo->save();
    
            return redirect()->route('companyLedgers.index')->with('success','Customer updated Sucessfully !!');  
        }
        else{
            return redirect()->route('companyLedgers.create')->withErrors($validator)->withInput();
    
        }
    
        
    }

    public function destroy($id){


        $cusiddelete=CompanyLedger::findOrFail($id);
        $cusiddelete->delete();
  
        return redirect()->route('companyLedgers.index')->with('success','Customer Deleted sucessfully'); 
        
  }

}