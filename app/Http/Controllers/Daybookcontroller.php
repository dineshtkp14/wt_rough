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
        
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View All Daybooks',
            'link'=>'View All Customers'
        ];
   
        
        return view('daybook.list',['breadcrumb'=>$breadcrumb]);

    }



    public function create()
    {
        $breadcrumb= [
            'subtitle'=>'Add',
            'title'=>'Add Daybook',
            'link'=>'Add Daybook'
        ];
   
        return view('daybook.create',['breadcrumb'=>$breadcrumb]);
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
   
public function edit($id)

{
    $breadcrumb= [
        'subtitle'=>'Edit',
        'title'=>'Edit Daybook Details',
        'link'=>'Edit Daybook Details'
    ];

    $daybook=daybook::findOrfail($id);

    return view('daybook.edit',['daybook'=>$daybook,'breadcrumb'=>$breadcrumb]);   
    
}

public function update($id, Request $req)
{
    $validator=Validator::make($req->all(),[

        'name'=>'required',
        'address'=>'required',
        'phoneno'=>'required', 
       
           
    ]);

    if($validator->passes()){

      
        $daybook= daybook::find($id);
        $daybook->name=$req->name;
        $daybook->address=$req->address;
        $daybook->contact=$req->contactno;
        $daybook->amount=$req->amount;
        $daybook->remarks=$req->remarks;
        $daybook->date=$req->date;
        $daybook->modeofpay=$req->modeofpay;
        $daybook->save();
      

        return redirect()->route('daybooks.index')->with('success','Daybook Details Updated Sucessfully !!');  
    }
    else{
        return redirect()->route('daybooks.edit')->withErrors($validator)->withInput();

    }

    
}

public function destroy($id,Request $req){

    $daybookdelete=daybook::findOrFail($id);
    $daybookdelete->delete();

    return redirect()->route('daybooks.index')->with('success','Daybook Deleted sucessfully'); 
    

}



}
