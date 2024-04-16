<?php

namespace App\Http\Controllers;

use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class CompanyController extends Controller
{
    public function index()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View Suppliers/Company',
            'link'=>'View Suppliers/Company'
        ];

         return view('company.list',['breadcrumb'=>$breadcrumb]);  
    } 
         return redirect('/login');
        
       
    }
    public function create()
    {

        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Add',
            'title'=>'Add New Suppliers/Company',
            'link'=>'Add New Suppliers/Company'
        ];

        return view('company.create',['breadcrumb'=>$breadcrumb]);
        
    }
    return redirect('/login');

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
        $companyinfo->added_by = session('user_email');

        $companyinfo->save();

      

        return redirect()->route('companys.index')->with('success','Company Added Sucessfully !!');  
    }
    else{
        return redirect()->route('companys.create')->withErrors($validator)->withInput();

    }

}

public function edit($id)

{
    if(Auth::check()){
    $breadcrumb= [
        'subtitle'=>'Edit',
        'title'=>'Edit Customers Details',
        'link'=>'Edit Customers Details'
    ];

    $distrinutors=company::findOrfail($id);

    return view('company.edit',['company'=>$distrinutors,'breadcrumb'=>$breadcrumb]);   
    
}

return redirect('/login');

}

public function update($id, Request $req)
{
    if(Auth::check()){
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
        $companyinfo->added_by = session('user_email');
        $companyinfo->save();

      

        return redirect()->route('companys.index')->with('success','Company Details Updated Sucessfully !!');  
    }
    else{
        return redirect()->route('companys.edit', ['companys' => $id])->withErrors($validator)->withInput();


    }

    
}
return redirect('/login');

}


public function destroy($id,Request $req){

    $companydelete=company::findOrFail($id);
    $companydelete->delete();

    return redirect()->route('companys.index')->with('success','Company Deleted sucessfully'); 
    

}
}
