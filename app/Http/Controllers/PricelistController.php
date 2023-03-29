<?php

namespace App\Http\Controllers;

use App\Models\pricelist;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class PricelistController extends Controller
{
    public function index()
    {
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View Items Price List',
            'link'=>'View Items Price List'
        ];
        
       
         return view('pricelist.list',['breadcrumb'=>$breadcrumb]);

       
    }
    public function create()
    {
        $breadcrumb= [
            'subtitle'=>'Add',
            'title'=>'Add Items Price List',
            'link'=>'Add Items Price List'
        ];
        
       
        return view('pricelist.create',['breadcrumb'=>$breadcrumb]);
    }


    public function store(Request $req)
   {
    $validator=Validator::make($req->all(),[

        'itemname'=>'required',
        'costprice'=>'required|numeric', 
        'saleprice'=>'required|numeric', 
        'wholesaleprice'=>'numeric', 
       
       
           
    ]);

    if($validator->passes()){

        $pricelistobj=new pricelist();
        $pricelistobj->itemname=$req->itemname;
        $pricelistobj->costprice=$req->costprice;
        $pricelistobj->saleprice=$req->saleprice;
        $pricelistobj->wholesaleprice=$req->wholesaleprice;
        $pricelistobj->note=$req->note;       
        $pricelistobj->save();

      

        return redirect()->route('pricelists.create')->with('success','Items Added Sucessfully !!');  
    }
    else{
        return redirect()->route('pricelists.create')->withErrors($validator)->withInput();

    }

   }
   
   public function edit($id)
    {
        $priceistdata=pricelist::findOrfail($id);

        return view('pricelist.edit',['pricelistdata'=>$priceistdata]);
        
    }

    public function update($id, Request $req)
    {
        $validator=Validator::make($req->all(),[

            'itemname'=>'required',
        'costprice'=>'required|numeric', 
        'saleprice'=>'required|numeric', 
        'wholesaleprice'=>'numeric', 
           
               
        ]);
    
        if($validator->passes()){
    
          
        $pricelistobj= pricelist::find($id);
        $pricelistobj->itemname=$req->itemname;
        $pricelistobj->costprice=$req->costprice;
        $pricelistobj->saleprice=$req->saleprice;
        $pricelistobj->wholesaleprice=$req->wholesaleprice;
        $pricelistobj->note=$req->note;       
        $pricelistobj->save();

    
            return redirect()->route('pricelists.create')->with('success','Items updated Sucessfully !!');  
        }
        else{
            return redirect()->route('pricelists.create')->withErrors($validator)->withInput();
    
        }
    
        
    }

    public function destroy($id){

        $pricelistid=pricelist::findOrFail($id);
        $pricelistid->delete();


          return redirect()->route('pricelists.index')->with('success','Deleted sucessfully'); 
        
  
  }

}
