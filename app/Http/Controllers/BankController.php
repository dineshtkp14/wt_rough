<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\Bank;

class BankController extends Controller
{
    
    public function index(Request $req)
    {
        
        $from=date($req->date1);
        $to=date($req->date2);

        $custo=null;
        $count=null;
        if($from == "" || $to==""){

            $custo=bank::orderBy('id','DESC')->get();
        }else{
            $dataval=now()->format('Y-m-d');
            $count=Bank::whereBetween('date',[$from,$to])->sum('amount');
           
            $custo=Bank::whereBetween('date',  [$from,$to])->get();

        }
        
        return view('bank.list',['custo'=>$custo,'totalsum'=>$count]);
    }

   
    public function create()
    {

        return view('bank.create');
    }


    public function store(Request $req)
   {
    $validator=Validator::make($req->all(),[

        'date'=>'required', 
        'amount'=>'required|numeric', 
        'name'=>'required',
        'remarks'=>'required', 
       
            
    ]);

    if($validator->passes()){

        $bankbook=new Bank();
        $bankbook->name=$req->name;
        $bankbook->amount=$req->amount;
        $bankbook->remarks=$req->remarks;
        $bankbook->date=$req->date;
       
        $bankbook->save();

      

        return redirect()->route('banks.create')->with('success','Items Added Sucessfully !!');  
    }
    else{
        return redirect()->route('banks.create')->withErrors($validator)->withInput();

    }
   
   }
}
