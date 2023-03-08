<?php

namespace App\Http\Controllers;

use App\Models\customerinfo;
use App\Models\invoice;
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

        $cus=customerinfo::orderBy('id','DESC')->get();
        return view('itemssales.create',['all'=>$cus]);   

       
    }


    public function store(Request $req)
    {
        $sales_arr = json_decode($req->sales_arr);//rowdetails
        //dd($sales_arr[0]);

        $final_arr = json_decode($req->final_arr);//finaltotalinvoice
      
        // invoice insert
       

            $invoice_data = new invoice();
            $invoice_data->customerid = $req->cid;
          
            $invoice_data->paidamount =null;
            $invoice_data->dueamount = $final_arr[0]->total;
           
            $invoice_data->subtotal = $final_arr[0]->subtotal;
            $invoice_data->discount = $final_arr[0]->discount;
            $invoice_data->total = $final_arr[0]->total;
            $invoice_data->notes = $final_arr[0]->note;
            $invoice_data->save();
           


        // sales insert
        foreach ($sales_arr as $value) {
            $data = new salesitem();
            $data->invoiceid = $invoice_data->id;
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

