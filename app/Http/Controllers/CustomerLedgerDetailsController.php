<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Models\customerledgerdetails;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\customerinfo;
use Illuminate\Support\Facades\Auth;



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
        foreach($cus as  $data){
            if($data->customerid){
                $cus_name=company::where('id',$data->customerid)->select('name')->first();
                $data->customerid = $cus_name->name;
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
       
        return view('customerdetails.create',['all'=>$cus,'breadcrumb'=>$breadcrumb]);   ;
    }
    return redirect('/login');
}

    public function store(Request $req)
    {
        if(Auth::check()){
     $validator=Validator::make($req->all(),[
        'customerid'=>'required',
        'date'=>'required',
        'particulars'=>'required',
        'amount'=>'required',
        'vt'=>'required',

           
     ]);
 
     if($validator->passes()){
 
         $cl=new customerledgerdetails();
         $cl->customerid=$req->customerid;
         $cl->date=$req->date;
         $cl->particulars=$req->particulars;
         $cl->invoicetype="credit";
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
    return redirect('/login');
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
            'title'=>'All Customer Credit Details Listx',
            'link'=>'View Customer Credit Details List'
        ];

      
       
        $query=customerledgerdetails::orderBy('id','DESC')->get();
       
        return view('allsalesdetails.allcustomercreditlist', ['all' => $query, 'breadcrumb' => $breadcrumb]);
        

}
}
}