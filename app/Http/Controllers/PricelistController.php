<?php

namespace App\Http\Controllers;

use App\Models\pricelist;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class PricelistController extends Controller
{
    public function index()
    {
        
         $cus=pricelist::orderBy('id','DESC')->get();
       
         return view('pricelist.list',['all'=>$cus]);

       
    }
    public function create()
    {

        return view('pricelist.create');
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

        $disinfoobj=new pricelist();
        $disinfoobj->name=$req->name;
        $disinfoobj->address=$req->address;
        $disinfoobj->email=$req->email;
        $disinfoobj->phoneno=$req->phoneno;
       
        
        $disinfoobj->remarks=$req->remarks;
       
        $disinfoobj->save();

      

        return redirect()->route('pricelists.create')->with('success','Items Added Sucessfully !!');  
    }
    else{
        return redirect()->route('pricelists.create')->withErrors($validator)->withInput();

    }

   }
   
   public function edit($id)
    {
        $customers=pricelist::findOrfail($id);

        return view('pricelists.edit',['cus'=>$customers]);
        
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
    
          
            $disinfoobj= pricelist::find($id);
            $disinfoobj->name=$req->name;
            $disinfoobj->address=$req->address;
            $disinfoobj->email=$req->email;
            $disinfoobj->phoneno=$req->phoneno;
           
            
            $disinfoobj->remarks=$req->remarks;
           
            $disinfoobj->save();
    
          
    
            return redirect()->route('pricelistss.create')->with('success','Items updated Sucessfully !!');  
        }
        else{
            return redirect()->route('pricelistss.create')->withErrors($validator)->withInput();
    
        }
    
        
    }

    public function destroy($id,Request $req){

        $cusiddelete=pricelist::findOrFail($id);
       
  
        $cusiddelete->delete();
  
        //$req->session()->flash('success','Deleted Sucessfully'); 
        return redirect()->route('pricelistss.index')->with('success','Deleted sucessfully'); 
        
  
  }

}
