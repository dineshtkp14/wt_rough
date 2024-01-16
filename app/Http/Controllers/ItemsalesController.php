<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Models\customerinfo;
use App\Models\customerledgerdetails;
use App\Models\invoice;
use App\Models\item;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\salesitem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemsalesController extends Controller
{
    
    public function index()
    {
        if(Auth::check()){
        $breadcrumb = [
            'subtitle' => 'View',
            'title' => 'View Invoice Sales Details',
            'link' => 'View Invoice Sales Details'
        ];
        $cus = salesitem::orderBy('id', 'DESC')->paginate(20); 
        return view('itemssales.list', compact('cus', 'breadcrumb'));
    }
    return redirect('/login');
}  



    public function create()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Create',
            'title'=>'Invoice',
            'link'=>'Invoice'
        ];

     
        $cus = customerinfo::all();
        $statement  = DB::select("SHOW TABLE STATUS LIKE 'invoices'");
        $nextUserId = $statement[0]->Auto_increment;

        $itemsdata = item::all();
        return view('itemssales.create', ['page' => 'isc', 'all' => $cus, 'data' => $itemsdata,'nextgenid' => $nextUserId,'breadcrumb'=>$breadcrumb]);
    }

    return redirect('/login');
 }
    public function store(Request $req)
    {
        if(Auth::check()){

        $sales_arr = json_decode($req->sales_arr); //rowdetails
        $final_arr = json_decode($req->final_arr); //finaltotalinvoice
        // invoice insert
        $invoice_data = new invoice();
        $invoice_data->customerid = $final_arr[0]->customer;
        // $invoice_data->paidamount = null;
        // $invoice_data->dueamount = $final_arr[0]->total;
        $invoice_data->subtotal = $final_arr[0]->subtotal;
        $invoice_data->discount = $final_arr[0]->discount == "" ? 0.00 : $final_arr[0]->discount;
        $invoice_data->total = $final_arr[0]->total;
        $invoice_data->notes = $final_arr[0]->note;
        $invoice_data->inv_type = $req->invoice_type;
        $invoice_data->date = $req->date;


        $invoice_data->added_by = session('user_email');

        //dd( $invoice_data->notes);
        $invoice_data->save();

        // sales insert
        foreach ($sales_arr as $value) {
            $data = new salesitem();
            $data->invoiceid = $invoice_data->id;
            $data->itemid = $value->product == "" ? null : $value->product;

            $data->unstockedname = $value->unstocked;
            $data->quantity = $value->quantity;

            if ($data->itemid) {
                // $item = item::where('id', $data->itemid)->select('quantity')->first();
                // $item->quantity =  $item->quantity - $value->quantity;

                // // Check for validation errors
                // $item->update(['quantity' => $item->quantity]);

                DB::table('items')->where('id', $data->itemid)->decrement('quantity', $value->quantity);


            }

            $data->price = $value->price;
            $data->discount = $value->discount == "" ? 0.00 : $value->discount;
            $data->subtotal = $value->subtotal;
            $data->added_by = session('user_email');

            $data->save();
        }

        $cus_data = new customerledgerdetails();
        $cus_data->customerid = $final_arr[0]->customer;
        $cus_data->invoiceid = $invoice_data->id;
        $cus_data->date = $req->date;
        $cus_data->particulars  = "Goods Sales";
        $cus_data->voucher_type = "sales";
        $cus_data->invoicetype = $req->invoice_type;
        $cus_data->debit =  $final_arr[0]->total;
        $cus_data->added_by = session('user_email');

        $cus_data->save();

        return redirect()->route('itemsales.index')->with('success','Invoice Created Sucessfully !!');
                                
       

    }

    return redirect('/login');
 }



 public function edit($id)
 {
     if (Auth::check()) {
         $breadcrumb = [
             'subtitle' => 'Edit',
             'title' => 'Edit SalesItem Details',
             'link' => 'Edit SalesItem Details'
         ];
 
         $all = salesitem::findOrFail($id);
 
         return view('itemssales.edit', ['all' => $all, 'breadcrumb' => $breadcrumb]);
 
         return redirect('/login');
     }
 }
 
 public function update($id, Request $req)
 {
     if (Auth::check()) {
         $validator = Validator::make($req->all(), [
             'itemid' => 'required',
             'unstockedname' => 'required',
             'quantity' => 'required',
             'price' => 'required',
             'discount' => 'required',
             'subtotal' => 'required',
             // Add more validation rules as needed
         ]);
 
         if ($validator->passes()) {
             $item = salesitem::find($id);
             $item->itemid = $req->itemid;
             $item->unstockedname = $req->unstockedname;
             $item->quantity = $req->quantity;
             $item->price = $req->price;
             $item->discount = $req->discount;
             $item->subtotal = $req->subtotal;
             // Update other fields as needed
             $item->added_by = session('user_email');

 
             $item->save();
 
             return redirect()->route('itemsales.index')->with('success', 'ItemSales Updated Successfully!');
         } else {
             return redirect()->route('itemsales.edit', $id)->withErrors($validator)->withInput();
         }
     }
 
     return redirect('/login');
 }
 







    
}