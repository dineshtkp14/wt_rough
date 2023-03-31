<?php

namespace App\Http\Controllers;

use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class CompanyController extends Controller
{
    public function index()
    {
       
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View Suppliers/Company',
            'link'=>'View Suppliers/Company'
        ];

         return view('company.list',['breadcrumb'=>$breadcrumb]);   

       
    }
    public function create()
    {
        $breadcrumb= [
            'subtitle'=>'Add',
            'title'=>'Add New Suppliers/Company',
            'link'=>'Add New Suppliers/Company'
        ];

        return view('company.create',['breadcrumb'=>$breadcrumb]);
    }


    public function store(Request $req)
   {
    $validator=Validator::make($req->all(),[

        'name'=>'required',
        'address'=>'required',
        'phoneno'=>'required', 
       
       
       
           
    ]);

    if($validator->passes()){

        $companyinfo=new company();
        $companyinfo->name=$req->name;
        $companyinfo->address=$req->address;
        $companyinfo->email=$req->email;
        $companyinfo->phoneno=$req->phoneno;
        $companyinfo->notes=$req->notes;
        $companyinfo->save();

      

        return redirect()->route('companys.index')->with('success','Company Added Sucessfully !!');  
    }
    else{
        return redirect()->route('companys.create')->withErrors($validator)->withInput();

    }

}

public function edit($id)

{
    $breadcrumb= [
        'subtitle'=>'Edit',
        'title'=>'Edit Customers Details',
        'link'=>'Edit Customers Details'
    ];

    $distrinutors=company::findOrfail($id);

    return view('company.edit',['company'=>$distrinutors,'breadcrumb'=>$breadcrumb]);   
    
}

public function update($id, Request $req)
{
    $validator=Validator::make($req->all(),[

        'name'=>'required',
        'address'=>'required',
        'phoneno'=>'required', 
       
           
    ]);

    if($validator->passes()){

      
        $companyinfo= company::find($id);
        $companyinfo->name=$req->name;
        $companyinfo->address=$req->address;
        $companyinfo->email=$req->email;
        $companyinfo->phoneno=$req->phoneno;
        $companyinfo->notes=$req->notes;
        $companyinfo->save();

      

        return redirect()->route('companys.index')->with('success','Company Details Updated Sucessfully !!');  
    }
    else{
        return redirect()->route('companys.create')->withErrors($validator)->withInput();

    }

    
}

public function destroy($id,Request $req){

    $companydelete=company::findOrFail($id);
    $companydelete->delete();

    return redirect()->route('companys.index')->with('success','Company Deleted sucessfully'); 
    

}
}
