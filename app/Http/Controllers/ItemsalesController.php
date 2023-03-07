<?php

namespace App\Http\Controllers;

use App\Models\itemsale;
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
    {
        $sales_arr = json_decode($req->sales_arr);//rowdetails
        //dd($sales_arr[0]);

        $final_arr = json_decode($req->final_arr);//finaltotal
      
        // invoice insert

        // sales insert
        foreach ($sales_arr as $value) {
            $data = new salesitem();
            $data->invoiceid = 1;
            $data->itemid = $value->product;
            $data->unstockedname = "kkk";

            $data->quantity = $value->quantity;
            $data->price = $value->price;
            $data->discount = $value->discount;  
            $data->subtotal = $value->subtotal;   

            

            $data->save();
        }
    }
}

