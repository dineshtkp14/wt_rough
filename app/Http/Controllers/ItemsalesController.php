<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\salesitem;

class ItemsalesController extends Controller
{



    public function index()
    {

        $cus = salesitem::orderBy('id', 'DESC')->get();


        return view('itemssales.list', ['all' => $cus]);
    }
    public function create()
    {

        return view('itemssales.create');
    }


    public function store(Request $req)
<<<<<<< HEAD
    {
        $validator = Validator::make($req->all(), [
=======
   {
    


    //createinvoice  retirns invoiceid
    //calculate validateallitems from frontend and backed data
    //insert all items in itemsalestable with the returned invoice id
    //update invoice with validated calutaion 
    //create new invoice histry ,billno,date,paidamount,dueamount,totalamount
    
        
        $disinfoobj=new salesitem();
        $disinfoobj->customerid=$req->customerid;
        $disinfoobj->itemid=$req->itemid;
        $disinfoobj->unstockedname=$req->unstockedname;
        $disinfoobj->quantity=$req->quantity;
        $disinfoobj->price=$req->price;
        $disinfoobj->discount=$req->discount;
        $disinfoobj->subtotal=$req->subtotal;

        $disinfoobj->subtotalf=$req->subtotalf;

        $disinfoobj->discountf=$req->discountf;
        $disinfoobj->total=$req->total;
       
        $disinfoobj->save();

    

        return redirect()->route('itemsales.create')->with('success','Items Added Sucessfully !!');  
    
    else{
       
        return redirect()->route('itemsales.create')->withErrors($validator)->withInput();
>>>>>>> c64d39b2a96e611658440b3134c5aed8614fa1d6





            'customerid' => 'required',
            'itemid' => 'required',
            'unstockedname' => 'required',
            'quantity' => 'required',

            'price' => 'required|numeric',
            'discount' => 'required',
            'subtotal' => 'required',
            'subtotalf' => 'required',
            'discountf' => 'required',
            'total' => 'required',

        ]);

        if ($validator->passes()) {


            $disinfoobj = new salesitem();
            $disinfoobj->customerid = $req->customerid;
            $disinfoobj->itemid = $req->itemid;
            $disinfoobj->unstockedname = $req->unstockedname;
            $disinfoobj->quantity = $req->quantity;
            $disinfoobj->price = $req->price;
            $disinfoobj->discount = $req->discount;
            $disinfoobj->subtotal = $req->subtotal;

            $disinfoobj->subtotalf = $req->subtotalf;

            $disinfoobj->discountf = $req->discountf;
            $disinfoobj->total = $req->total;

            $disinfoobj->save();



            return redirect()->route('itemsales.create')->with('success', 'Items Added Sucessfully !!');
        } else {

            return redirect()->route('itemsales.create')->withErrors($validator)->withInput();
        }
    }
}
