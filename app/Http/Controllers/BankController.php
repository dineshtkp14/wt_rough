<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\Bank;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    
    public function index(Request $req)
    {
        if(Auth::check()){

        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View Bank Deposit Amount',
            'link'=>'View Bank Deposit Amount'
        ];
        
        $from=date($req->date1);
        $to=date($req->date2);

        $custo=null;
        $count=null;
        if($from == "" || $to==""){

            $custo=bank::orderBy('id','DESC')->paginate(5);
        }else{
            
           
            $count=Bank::whereBetween('date',[$from,$to])->sum('amount');
            $custo=Bank::whereBetween('date',  [$from,$to])->paginate(15);

        }
        
        return view('bank.list',['custo'=>$custo,'totalsum'=>$count,'breadcrumb'=>$breadcrumb]);
    }

    return redirect('/login');

    }
    public function show_intopdfbankdetails(Request $req)
    {
        if(Auth::check()){
        
        $from=date($req->date1);
        $to=date($req->date2);
       

        $custo=null;
        $count=null;
       
        if($from == "" || $to==""){

            $custo=bank::orderBy('id','DESC')->get();
        }else{
            
           
            $count=Bank::whereBetween('date',[$from,$to])->sum('amount');
            $custo=Bank::whereBetween('date',  [$from,$to])->get();

        }
       
        $pdfviewe=FacadePdf::setOptions(['dpi' => 150,'defaultFont' => 'dejavu serif'])->loadView('bank.converttopdfbankhistory',['custo'=>$custo,'totalsum'=>$count]);     

        return $pdfviewe->download('invoice.pdf');
    }
    
return redirect('/login');
} 
    public function create()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Add',
            'title'=>'Bank Deposit',
            'link'=>'Bank Deposit'
        ];
        return view('bank.create',['breadcrumb'=>$breadcrumb]);
    }
    return redirect('/login');
} 


    public function store(Request $req)
   {
    $validator=Validator::make($req->all(),[

        'date'=>'required', 
        'amount'=>'required|numeric', 
        'name'=>'required',
      
       
            
    ]);

    if($validator->passes()){

        $bankbook=new Bank();
        $bankbook->name=$req->name;
        $bankbook->amount=$req->amount;
        $bankbook->remarks=$req->remarks;
        $bankbook->date=$req->date;
       
        $bankbook->save();

      

        return redirect()->route('banks.index')->with('success','Deposited Sucessfully !!');  
    }
    else{
        return redirect()->route('banks.create')->withErrors($validator)->withInput();

    }
   
   }
}
