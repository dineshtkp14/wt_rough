<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\TransferGoods;
use App\Models\item;



class TransferGoodsController extends Controller
{
    public function index()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View All Transfer Goods',
            'link'=>'View All Transfer Goods'
        ];
   

     return view('TransferGoods.list',['breadcrumb'=>$breadcrumb]);   

    }
    return redirect('/login');
}

    

    public function create()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Transfer',
            'title'=>'Transfer Goods ',
            'link'=>'Transfer Goods '
        ]; 
     
        return view('TransferGoods.create',['breadcrumb'=>$breadcrumb]);   
    }

    return redirect('/login');
 }





    public function store(Request $req)
   {
    if(Auth::check()){
    $validator=Validator::make($req->all(),[

        'itemid' => 'required',
        'itemid' => [
            'required',
            function ($attribute, $value, $fail) {
                // Check if the itemid exists in the items table
                $item = item::find($value);
                if (!$item) {
                    $fail('The selected itemid is invalid.');
                }
            }
        ],
            'date' => 'required|date',
            'shiftArea' => 'required',
            'shiftBy' => 'required',
            'notes' => 'nullable',
           
    ]);

    if($validator->passes()){

            $transfergoodsinfo=new TransferGoods();
            $transfergoodsinfo->itemid=$req->itemid;
            $transfergoodsinfo->date=$req->date;
            $transfergoodsinfo->shiftArea=$req->shiftArea;
            $transfergoodsinfo->shiftBy=$req->shiftBy;
            $transfergoodsinfo->quantity=$req->quantity;

            $transfergoodsinfo->notes=$req->notes;
            $transfergoodsinfo->added_by = session('user_email');
            $transfergoodsinfo->save();

        return redirect()->route('transfergoods.index')->with('success','Customer Added Sucessfully !!');  
    }

    else{
        return redirect()->route('transfergoods.create')->withErrors($validator)->withInput();

    }

   }
   return redirect('/login');
}

   public function edit($id)

    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Edit',
            'title'=>'Edit Transfer Goods',
            'link'=>'Edit Transfer Goods'
        ];
   
        $allmyfirm=TransferGoods::findOrfail($id);

        return view('TransferGoods.edit',['all'=>$allmyfirm,'breadcrumb'=>$breadcrumb]);   
        
    }
    return redirect('/login');
 }
    public function update($id, Request $req)
    {
        if(Auth::check()){
        $validator=Validator::make($req->all(),[
            'itemid' => 'required',
            'date' => 'required|date',
            'shiftArea' => 'required',
            'shiftBy' => 'required',
            'notes' => 'nullable',
               
        ]);
    
        if($validator->passes()){
    
            $transfergoodsinfo= TransferGoods::find($id);
            $transfergoodsinfo->itemid=$req->itemid;
            $transfergoodsinfo->quantity=$req->quantity;

            $transfergoodsinfo->date=$req->date;
            $transfergoodsinfo->shiftArea=$req->shiftArea;
            $transfergoodsinfo->shiftBy=$req->shiftBy;
            $transfergoodsinfo->notes=$req->notes;
            $transfergoodsinfo->added_by = session('user_email');
            $transfergoodsinfo->save();
    
            return redirect()->route('transfergoods.index')->with('success','Customer updated Sucessfully !!');  
        }
        else{
            return redirect()->route('transfergoods.create')->withErrors($validator)->withInput();
    
        }
    
        
    }
    return redirect('/login');
 }
    public function destroy($id,Request $req){


        $cusiddelete=TransferGoods::findOrFail($id);
        $cusiddelete->delete();
  
        return redirect()->route('transfergoods.index')->with('success','Customer Deleted sucessfully'); 
        
  }
}
