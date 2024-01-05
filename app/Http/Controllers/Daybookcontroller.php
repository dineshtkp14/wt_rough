<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\daybook;
use Illuminate\Support\Facades\Auth;

class Daybookcontroller extends Controller
{
  
     public function index()
    {
  
    if(Auth::check()){

        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View All Daybooks',
            'link'=>'View All Daybooks'
        ];
   
        
        return view('daybook.list',['breadcrumb'=>$breadcrumb]);

    }
    return redirect('/login');
}



    public function create()
    {
        
    if(Auth::check()){

        $breadcrumb= [
            'subtitle'=>'Add',
            'title'=>'Add Daybook',
            'link'=>'Add Daybook'
        ];
   
        return view('daybook.create',['breadcrumb'=>$breadcrumb]);
    }

    return redirect('/login');
 }
    public function store(Request $req)
   {
    
    if(Auth::check()){

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
   return redirect('/login');
}
public function edit($id)

{
    
    if(Auth::check()){

    $breadcrumb= [
        'subtitle'=>'Edit',
        'title'=>'Edit Daybook Details',
        'link'=>'Edit Daybook Details'
    ];

    $daybook=daybook::findOrfail($id);

    return view('daybook.edit',['daybook'=>$daybook,'breadcrumb'=>$breadcrumb]);   
    
}
return redirect('/login');
}
public function update($id, Request $req)
{
    if(Auth::check()) {
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'address' => 'required',
            'phoneno' => 'required',
        ]);

        if($validator->passes()) {
            $daybook = daybook::find($id);
            $daybook->name = $req->name;
            $daybook->address = $req->address;
            $daybook->contact = $req->contactno;
            $daybook->amount = $req->amount;
            $daybook->remarks = $req->remarks;
            $daybook->date = $req->date;
            $daybook->modeofpay = $req->modeofpay;
            $daybook->save();

            return redirect()->route('daybooks.index')->with('success','Daybook Details Updated Successfully !!');
        } else {
            dd("this is errror");
            return redirect()->route('daybooks.edit', ['daybooks' => $daybook])->withErrors($validator)->withInput();
        }
    }

    return redirect('/login');
}


public function destroy($id,Request $req){

    if(Auth::check()){

    $daybookdelete=daybook::findOrFail($id);
    $daybookdelete->delete();

    return redirect()->route('daybooks.index')->with('success','Daybook Deleted sucessfully'); 
    

}

return redirect('/login');
}

}
