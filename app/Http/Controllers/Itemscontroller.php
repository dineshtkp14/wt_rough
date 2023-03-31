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
            'subtitle'=>'View',
            'title'=>'View Invoice Sales Details',
            'link'=>'View Invoice Sales Details'
        ];

         $cus=item::orderBy('id','DESC')->get();
         return view('items.list',['all'=>$cus,'breadcrumb'=>$breadcrumb]);

       
    }
    public function create()
    {
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View Invoice Sales Details',
            'link'=>'View Invoice Sales Details'
        ];

        return view('items.create',['breadcrumb'=>$breadcrumb]);
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
