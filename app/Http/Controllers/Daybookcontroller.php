<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\daybook;




class Daybookcontroller extends Controller
{
  
     public function index()
    {
        
        // $cus=daybook::orderBy('id','DESC')->get();
        // // $count=daybook::all()->where('date', '2023-02-01')->sum('amount');
        // $dataval=now()->format('Y-m-d');
        
        // $count=daybook::all()->where('date',  $dataval)->where('modeofpay',  "jamma")->sum('amount');

        // return view('daybook.list',['custo'=>$cus],['totalsum'=>$count]);
        return view('daybook.list');




    }



    public function create()
    {

        return view('daybook.create');
    }


    public function store(Request $req)
   {
    $validator=Validator::make($req->all(),[

        'name'=>'required',
        'address'=>'required',
        'contactno'=>'required',
        'amount'=>'required|numeric', 
      
        'date'=>'required', 
        'modeofpay'=>'required',      
    ]);

    if($validator->passes()){

        $daybook=new daybook();
        $daybook->name=$req->name;
        $daybook->address=$req->address;
        $daybook->contact=$req->contactno;
        $daybook->amount=$req->amount;
        $daybook->remarks=$req->remarks;
        $daybook->date=$req->date;
        $daybook->modeofpay=$req->modeofpay;
        $daybook->save();

      

        return redirect()->route('daybooks.create')->with('success','Items Added Sucessfully !!');  
    }
    else{
        return redirect()->route('daybooks.create')->withErrors($validator)->withInput();

    }
   
   }

}
