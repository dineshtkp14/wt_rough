<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\customerinfo;



class CustomerinfoController extends Controller
{
    public function index()
    {
        
         $cus=customerinfo::orderBy('id','DESC')->get();
        // // $count=daybook::all()->where('date', '2023-02-01')->sum('amount');
        // $dataval=now()->format('Y-m-d');
        // $count=daybook::all()->where('date',  $dataval)->sum('amount');

         return view('customerinfo.list',['all'=>$cus]);

       
    }
    public function create()
    {

        return view('customerinfo.create');
    }


    public function store(Request $req)
   {
    $validator=Validator::make($req->all(),[

        'name'=>'required',
        'address'=>'required',
        'email'=>'required',
        'remarks'=>'required',
      
        'phoneno'=>'required|numeric', 
       
           
    ]);

    if($validator->passes()){

        $disinfoobj=new customerinfo();
        $disinfoobj->name=$req->name;
        $disinfoobj->address=$req->address;
        $disinfoobj->email=$req->email;
        $disinfoobj->phoneno=$req->phoneno;
       
        
        $disinfoobj->remarks=$req->remarks;
       
        $disinfoobj->save();

      

        return redirect()->route('customerinfos.create')->with('success','Items Added Sucessfully !!');  
    }
    else{
        return redirect()->route('customerinfos.create')->withErrors($validator)->withInput();

    }

   }
   
   public function edit($id)
    {
        $customers=customerinfo::findOrfail($id);

        return view('customerinfo.edit',['cus'=>$customers]);
        
    }

    public function update($id, Request $req)
    {
        $validator=Validator::make($req->all(),[

            'name'=>'required',
            'address'=>'required',
            'email'=>'required',
            'remarks'=>'required',
          
            'phoneno'=>'required|numeric', 
           
               
        ]);
    
        if($validator->passes()){
    
          
            $disinfoobj= customerinfo::find($id);
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

        $cusiddelete=customerinfo::findOrFail($id);
       
  
        $cusiddelete->delete();
  
        //$req->session()->flash('success','Deleted Sucessfully'); 
        return redirect()->route('customerinfos.index')->with('success','Deleted sucessfully'); 
        
  
  }

}
