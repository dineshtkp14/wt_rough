<?php

namespace App\Http\Controllers;

use App\Models\customerledgerdetails;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\customerinfo;


class CustomerLedgerDetailsController extends Controller

{
    public function index(Request $req)
    {

        $breadcrumb= [
            'subtitle'=>'Payments',
            'title'=>'All Customers Payment Histrory',
            'link'=>'All Customers Payment Histrory'
        ];
        $cus=customerledgerdetails::orderBy('id','DESC')->get(); 
        foreach($cus as  $data){
            if($data->customerid){
                $cus_name=customerinfo::where('id',$data->customerid)->select('name')->first();
                $data->customerid = $cus_name->name;
            }

        }
         return view('customerdetails.list',['all'=>$cus,'breadcrumb'=>$breadcrumb]);   
    }

    public function create()
    
    {
        $breadcrumb= [
            'subtitle'=>'Payments',
            'title'=>' Customers Ledger Payment',
            'link'=>'Customers Ledger Payment'
        ];
        $cus=customerinfo::orderBy('id','DESC')->get();
       
        return view('customerdetails.create',['all'=>$cus,'breadcrumb'=>$breadcrumb]);   ;
    }

    public function store(Request $req)
    {
     $validator=Validator::make($req->all(),[
 
           
     ]);
 
     if($validator->passes()){
 
         $cl=new customerledgerdetails();
         $cl->customerid=$req->customerid;
         $cl->date=$req->date;
         $cl->particulars=$req->particulars;
         $cl->voucher_type=$req->vt;
         $cl->credit=$req->amount;
         $cl->notes=$req->notes;
         $cl->save();
 
       
 
         return redirect()->route('cpayments.create')->with('success','payment Sucess !!');  
     }
     else{
         return redirect()->route('cpayments.create')->withErrors($validator)->withInput();
 
     }
    }
}
