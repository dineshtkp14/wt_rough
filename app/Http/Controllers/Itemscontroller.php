<?php

namespace App\Http\Controllers;

use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\item;
use Illuminate\Support\Facades\Auth;

class Itemscontroller extends Controller
{
    public function index()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'View ',
            'title'=>'View Items Details',
            'link'=>'View Items Details'
        ];

      
         $allitem=item::orderBy('id','DESC')->get();

       
         return view('items.list',['breadcrumb'=>$breadcrumb]);

       
    }
    return redirect('/login');
}
    public function create()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Add',
            'title'=>'Add Items',
            'link'=>'Add Items'
        ];

        return view('items.create',['breadcrumb'=>$breadcrumb]);
    }

    return redirect('/login');
 }
 public function store(Request $req)
 {
     if (Auth::check()) {
         $validator = Validator::make($req->all(), [
             'date' => 'required',
             'companyid' => 'required',
             'itemsname' => 'required',
             'costprice' => 'required',
             'quantity' => 'required',
             'mrp' => 'required',
             'showwarning' => 'required',
             'unit' => 'required',


         ]);
 
         if ($validator->passes()) {
             $company = company::find($req->companyid);
 
             if ($company) {
               
 
                 $itemsdetails = new item();
                 $itemsdetails->billno = $req->billno;

                $itemsdetails->companyid = $req->companyid; // Update with the company name

                $itemsdetails->date = $req->date;
                $itemsdetails->itemsname = $req->itemsname;
                $itemsdetails->quantity = $req->quantity;
                $itemsdetails->unit = $req->unit;

                $itemsdetails->costprice = $req->costprice;
                $itemsdetails->mrp = $req->mrp;
                $itemsdetails->notes = $req->notes;
                $itemsdetails->firm_name = $req->firm_name;


                $itemsdetails->com_Retail_price = $req->competetiveretail;
                $itemsdetails->com_wholesale_price = $req->competetivewholesale;
                $itemsdetails->wholesale_price = $req->wp;
                $itemsdetails->showwarning = $req->showwarning;

                $itemsdetails->total = $req->quantity * $req->costprice;
                $itemsdetails->added_by = session('user_email');

              


                $itemsdetails->save();
 
                 return redirect()->route('items.create')->with('success', 'Items Added Successfully !!');
             } else {
                 // Handle the case where the company is not found
                 return redirect()->route('items.create')->withErrors(['companyid' => 'Invalid Company'])->withInput();
             }
         } else {
             return redirect()->route('items.create')->withErrors($validator)->withInput();
         }
     }
 
     return redirect('/login');
 }
 
public function edit($id)

{
    if(Auth::check()){
    $breadcrumb= [
        'subtitle'=>'Edit',
        'title'=>'Edit Items Details',
        'link'=>'Edit Items Details'
    ];

    $items=item::findOrfail($id);

    return view('items.edit',['item'=>$items,'breadcrumb'=>$breadcrumb]);   
    
    return redirect('/login');
}
}

public function update($id, Request $req)
{
    if (Auth::check()) {
        $validator = Validator::make($req->all(), [
            'date' => 'required',
            'companyid' => 'required',
            'itemsname' => 'required',
            'costprice' => 'required',
            'quantity' => 'required',
            'mrp' => 'required',
        ]);

        if ($validator->passes()) {
            // Fetch the company based on the selected company ID
            $company = Company::find($req->companyid);

            // Check if the company is found
            if ($company) {

                $itemsdetails = item::find($id);
                $itemsdetails->billno = $req->billno;
                $itemsdetails->companyid = $req->companyid; // Update with the company name

                $itemsdetails->date = $req->date;
                $itemsdetails->itemsname = $req->itemsname;
                $itemsdetails->quantity = $req->quantity;
                $itemsdetails->costprice = $req->costprice;
                $itemsdetails->mrp = $req->mrp;
                $itemsdetails->showwarning = $req->showwarning;

                $itemsdetails->notes = $req->notes;
                $itemsdetails->firm_name = $req->firm_name;


                $itemsdetails->com_Retail_price = $req->competetiveretail;
                $itemsdetails->com_wholesale_price = $req->competetivewholesale;
                $itemsdetails->wholesale_price = $req->wp;

                $itemsdetails->total = $req->quantity * $req->costprice;
                $itemsdetails->added_by = session('user_email');

                $itemsdetails->save();

                return redirect()->route('items.index')->with('success', 'Updated Successfully!');
            } else {
                return redirect()->route('items.edit', $id)->with('error', 'Company not found!')->withInput();
            }
        } else {
            return redirect()->route('items.edit', $id)->withErrors($validator)->withInput();
        }
    }

    return redirect('/login');
}
   


public function destroy($id){

    $pricelistid=item::findOrFail($id);
    $pricelistid->delete();


      return redirect()->route('items.index')->with('success','Deleted Sucesfully !!'); 
    

}

}
