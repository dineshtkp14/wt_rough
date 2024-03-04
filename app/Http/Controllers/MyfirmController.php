<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Myfirm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;




class MyfirmController extends Controller
{
    //
    public function index()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View All My FIRM LIST',
            'link'=>'View All My FIRM LIST'
        ];
   

     return view('myfirm.list',['breadcrumb'=>$breadcrumb]);   

    }
    return redirect('/login');
}

    

    public function create()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Add',
            'title'=>'Add New MY FIRM ',
            'link'=>'Add New MY FIRM '
        ]; 
     
        return view('myfirm.create',['breadcrumb'=>$breadcrumb]);   
    }

    return redirect('/login');
 }





    public function store(Request $req)
   {
    if(Auth::check()){
    $validator=Validator::make($req->all(),[

       
        'firmname' => 'required',
        'nickname' => 'required', 
           
    ]);

    if($validator->passes()){

        $myfirminfo=new Myfirm();
        $myfirminfo->firm_name=$req->firmname;
        $myfirminfo->nick_name=$req->nickname;
        $myfirminfo->notes=$req->notes;
        $myfirminfo->added_by = session('user_email');
        $myfirminfo->save();

        return redirect()->route('myfirm.index')->with('success','Customer Added Sucessfully !!');  
    }

    else{
        return redirect()->route('myfirm.create')->withErrors($validator)->withInput();

    }

   }
   return redirect('/login');
}

   public function edit($id)

    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Edit',
            'title'=>'Edit My Firm Details',
            'link'=>'Edit My Firm Details'
        ];
   
        $allmyfirm=Myfirm::findOrfail($id);

        return view('myfirm.edit',['all'=>$allmyfirm,'breadcrumb'=>$breadcrumb]);   
        
    }
    return redirect('/login');
 }
    public function update($id, Request $req)
    {
        if(Auth::check()){
        $validator=Validator::make($req->all(),[

            'firmname' => 'required',
        'nickname' => 'required', 
               
        ]);
    
        if($validator->passes()){
    
            $myfirminfo= Myfirm::find($id);
            $myfirminfo->firm_name=$req->firmname;
        $myfirminfo->nick_name=$req->nickname;
        $myfirminfo->notes=$req->notes;
        $myfirminfo->added_by = session('user_email');

            $myfirminfo->save();
    
            return redirect()->route('myfirm.index')->with('success','Customer updated Sucessfully !!');  
        }
        else{
            return redirect()->route('myfirm.create')->withErrors($validator)->withInput();
    
        }
    
        
    }
    return redirect('/login');
 }
    public function destroy($id,Request $req){


        $cusiddelete=Myfirm::findOrFail($id);
        $cusiddelete->delete();
  
        return redirect()->route('myfirm.index')->with('success','Customer Deleted sucessfully'); 
        
  }
}
