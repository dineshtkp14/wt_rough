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
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View Suppliers/Company',
            'link'=>'View Suppliers/Company'
        ];

         return view('distributor.list',['all'=>$cus,'breadcrumb'=>$breadcrumb]);

       
    }
    public function create()
    {
        $breadcrumb= [
            'subtitle'=>'Add',
            'title'=>'Add New Suppliers/Company',
            'link'=>'Add New Suppliers/Company'
        ];

        return view('distributor.create',['breadcrumb'=>$breadcrumb]);
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

public function edit($id)

{
    $breadcrumb= [
        'subtitle'=>'Edit',
        'title'=>'Edit Customers Details',
        'link'=>'Edit Customers Details'
    ];

    $distrinutors=distributorinfo::findOrfail($id);

    return view('customerinfo.edit',['disinfo'=>$distrinutors,'breadcrumb'=>$breadcrumb]);   
    
}

public function update($id, Request $req)
{
    $validator=Validator::make($req->all(),[

        'name'=>'required',
        'address'=>'required',
        'email'=>'required',
      
      
        'phoneno'=>'required|numeric', 
       
           
    ]);

    if($validator->passes()){

      
        $disinfoobj= distributorinfo::find($id);
        $disinfoobj->name=$req->name;
        $disinfoobj->address=$req->address;
        $disinfoobj->email=$req->email;
        $disinfoobj->phoneno=$req->phoneno;
       
        
        $disinfoobj->remarks=$req->remarks;
       
        $disinfoobj->save();

      

        return redirect()->route('customerinfos.create')->with('success','Items updated Sucessfully !!');  
    }
    else{
        return redirect()->route('customerinfos.create')->withErrors($validator)->withInput();

    }

    
}

public function destroy($id,Request $req){

    $cusiddelete=distributorinfo::findOrFail($id);
   

    $cusiddelete->delete();

    return redirect()->route('customerinfos.index')->with('success','Deleted sucessfully'); 
    

}
}
