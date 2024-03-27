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
        if(Auth::check()){
     $validator=Validator::make($req->all(),[
        'customerid'=>'required',
        'date'=>'required',
        'particulars'=>'required',
        'amount'=>'required',
        'vt'=>'required',

           
     ]);
 
     if($validator->passes()){
 
        $statement  = DB::select("SHOW TABLE STATUS LIKE 'customerledgerdetails'");
         $nextUserId = $statement[0]->Auto_increment;       

         
         $cl=new customerledgerdetails();
         $cl->customerid=$req->customerid;
         $cl->date=$req->date;
         $cl->particulars=$req->particulars;
         $cl->invoicetype="payment";
         $cl->voucher_type=$req->vt;
         $cl->credit=$req->amount;
         $cl->notes=$req->notes;
         $cl->added_by = session('user_email');

         $cl->save();
 
       
         

         return redirect()->route('cashreceipt.search', ['receiptno' => $nextUserId])->with('success', 'Invoice Created Successfully !!');

         return redirect()->route('cashreceipt.search')->with('success','payment Sucess !!');  
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
            'title'=>'All Customer Credit Details List',
            'link'=>'View Customer Credit Details List'
        ];

      
       
       // $query=customerledgerdetails::orderBy('id','DESC')->get();
       
        return view('allsalesdetails.allcustomercreditlist', [ 'breadcrumb' => $breadcrumb]);
        

}
}
}