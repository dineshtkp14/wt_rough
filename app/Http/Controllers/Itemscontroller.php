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
        
         $cus=item::orderBy('id','DESC')->get();
       

         return view('items.list',['all'=>$cus]);

       
    }
    public function create()
    {

        return view('items.create');
    }


    public function store(Request $req)
   {
    $validator=Validator::make($req->all(),[

        'disname'=>'required',
       
           
    ]);

    if($validator->passes()){

        $disinfoobj=new item();
        $disinfoobj->billno=$req->billno;
        $disinfoobj->distributorname=$req->disname;
        $disinfoobj->date=$req->date;
        $disinfoobj->itemsname=$req->itemsname;
       
        $disinfoobj->quantity=$req->quantity;
        $disinfoobj->dlp=$req->dlp;
        $disinfoobj->mrp=$req->mrp;
        $disinfoobj->total=$req->total;
        $disinfoobj->finaltotal=$req->finaltotal;
       
        $disinfoobj->save();

      

        return redirect()->route('items.create')->with('success','Items Added Sucessfully !!');  
    }
    else{
        return redirect()->route('items.create')->withErrors($validator)->withInput();

    }
}
}
