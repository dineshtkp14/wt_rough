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

        $from=date($req->date1);
        $to=date($req->date2);
        $cusinfo=customerinfo::orderBy('id','DESC')->get();

        $cus=null;
        $cusinfo=null;

       
        if($from == "" || $to==""){

            $cus=customerledgerdetails::orderBy('id','DESC')->get();
            $cusinfo=customerinfo::orderBy('id','DESC')->get();


        }else{


            $cusid=$req->customerid;
            dd($cus);
            $cus=customerledgerdetails::whereBetween('created_at',  [$from,$to])->where('customerid', $cusid)->get();

        }
       
         return view('customerdetails.list',['all'=>$cus],['allcus'=>$cusinfo]);   
    }

    public function create()
    
    {
        $cus=customerinfo::orderBy('id','DESC')->get();
       
        return view('customerdetails.create',['all'=>$cus]);   ;
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
        

         $cl->credit=$req->credit;
       
         

        
         $cl->save();
 
       
 
         return redirect()->route('cpayments.create')->with('success','payment Sucess !!');  
     }
     else{
         return redirect()->route('cpayments.create')->withErrors($validator)->withInput();
 
     }
    }
}
