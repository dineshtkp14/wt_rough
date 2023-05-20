<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\item;
class Itemscontroller extends Controller
{
    public function index()
    {
        $breadcrumb= [
            'subtitle'=>'View ',
            'title'=>'View Items Details',
            'link'=>'View Items Details'
        ];

         $cus=item::orderBy('id','DESC')->get();
         return view('items.list',['all'=>$cus,'breadcrumb'=>$breadcrumb]);

       
    }
    public function create()
    {
        $breadcrumb= [
            'subtitle'=>'Add',
            'title'=>'Add Items',
            'link'=>'Add Items'
        ];

        return view('items.create',['breadcrumb'=>$breadcrumb]);
    }


    public function store(Request $req)
   {
    $validator=Validator::make($req->all(),[

        'date'=>'required',
        'companyid'=>'required',
        'itemsname'=>'required',
        'dlp'=>'required',
        'quantity'=>'required',
        'mrp'=>'required',

        
       
           
    ]);

    if($validator->passes()){

        $itemsdetails=new item();
        $itemsdetails->billno=$req->billno;
        $itemsdetails->distributorname=$req->companyid;
        $itemsdetails->date=$req->date;
        $itemsdetails->itemsname=$req->itemsname;
        $itemsdetails->quantity=$req->quantity;
        $itemsdetails->dlp=$req->dlp;
        $itemsdetails->mrp=$req->mrp;
        $itemsdetails->total=$req->quantity*$req->dlp;
        $itemsdetails->save();

      

        return redirect()->route('items.create')->with('success','Items Added Sucessfully !!');  
    }
    else{
        return redirect()->route('items.create')->withErrors($validator)->withInput();

    }
}
public function edit($id)

{
    $breadcrumb= [
        'subtitle'=>'Edit',
        'title'=>'Edit Items Details',
        'link'=>'Edit Items Details'
    ];

    $items=item::findOrfail($id);

    return view('items.edit',['item'=>$items,'breadcrumb'=>$breadcrumb]);   
    
}
public function update($id, Request $req)
{
    $validator=Validator::make($req->all(),[

        'date'=>'required',
        'companyid'=>'required',
        'itemsname'=>'required',
        'dlp'=>'required',
        'quantity'=>'required',
        'mrp'=>'required',

       
           
    ]);

    if($validator->passes()){


    $itemsdetails= item::find($id);
    $itemsdetails->billno=$req->billno;
    $itemsdetails->distributorname=$req->companyid;
    $itemsdetails->date=$req->date;
    $itemsdetails->itemsname=$req->itemsname;
    $itemsdetails->quantity=$req->quantity;
    $itemsdetails->dlp=$req->dlp;
    $itemsdetails->mrp=$req->mrp;
    $itemsdetails->total=$req->quantity*$req->dlp;
    $itemsdetails->save();


        return redirect()->route('items.index')->with('success','Items Price Updated Sucessfully !!');  
    }
    else{
        return redirect()->route('items.edit',$id)->withErrors($validator)->withInput();

    }

    
}

public function destroy($id){

    $pricelistid=item::findOrFail($id);
    $pricelistid->delete();


      return redirect()->route('items.index')->with('success','Items deleted Sucesfully !!'); 
    

}
}
