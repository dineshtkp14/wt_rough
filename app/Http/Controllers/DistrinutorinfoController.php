<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\distributorinfo;


class DistrinutorinfoController extends Controller
{
    
    public function index()
    {
        
         $cus=distributorinfo::orderBy('id','DESC')->get();
        // // $count=daybook::all()->where('date', '2023-02-01')->sum('amount');
        // $dataval=now()->format('Y-m-d');
        // $count=daybook::all()->where('date',  $dataval)->sum('amount');

         return view('distributor.list',['all'=>$cus]);

       
    }
    public function create()
    {

        return view('distributor.create');
    }


    public function store(Request $req)
   {
    $validator=Validator::make($req->all(),[

        'name'=>'required',
        'address'=>'required',
        'email'=>'required',
        'remarks'=>'required',
      
        'phoneno'=>'required|numeric', 
        'bank_accountno'=>'required', 
           
    ]);

    if($validator->passes()){

        $disinfoobj=new distributorinfo();
        $disinfoobj->name=$req->name;
        $disinfoobj->address=$req->address;
        $disinfoobj->email=$req->email;
        $disinfoobj->phoneno=$req->phoneno;
       
        $disinfoobj->bank_accountno=$req->bank_accountno;
        $disinfoobj->remarks=$req->remarks;
       
        $disinfoobj->save();

      

        return redirect()->route('disinfos.create')->with('success','Items Added Sucessfully !!');  
    }
    else{
        return redirect()->route('disinfos.create')->withErrors($validator)->withInput();

    }

}
}
