<?php

namespace App\Http\Controllers;
use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\item;
use App\Models\Myfirm;
use App\Models\trackitemstable;
use Illuminate\Support\Facades\DB; //


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

        $all=Myfirm::orderBy('id','DESC')->get();


        return view('items.create',['breadcrumb'=>$breadcrumb,'all'=>$all]);
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
             'itemstorearea' => 'required',


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
                $itemsdetails->opening_stock = $req->quantity;
                $itemsdetails->item_store_area = $req->itemstorearea;

                $itemsdetails->added_by = session('user_email');
                $itemsdetails->save();

//insertintotracktable
// Concatenate all the attributes of $itemsdetails
$additional_info = 'billno: ' . $itemsdetails->billno . ', ' .
                   'companyid: ' . $itemsdetails->companyid . ', ' .
                   'date: ' . $itemsdetails->date . ', ' .
                   'itemsname: ' . $itemsdetails->itemsname . ', ' .
                   'quantity: ' . $itemsdetails->quantity . ', ' .
                   'unit: ' . $itemsdetails->unit . ', ' .
                   'costprice: ' . $itemsdetails->costprice . ', ' .
                   'mrp: ' . $itemsdetails->mrp . ', ' .
                   'notes: ' . $itemsdetails->notes . ', ' .
                   'firm_name: ' . $itemsdetails->firm_name . ', ' .
                   'com_Retail_price: ' . $itemsdetails->com_Retail_price . ', ' .
                   'com_wholesale_price: ' . $itemsdetails->com_wholesale_price . ', ' .
                   'wholesale_price: ' . $itemsdetails->wholesale_price . ', ' .
                   'showwarning: ' . $itemsdetails->showwarning . ', ' .
                   'total: ' . $itemsdetails->total . ', ' .
                   'opening_stock: ' . $itemsdetails->opening_stock . ', ' .
                   'opening_stock: ' . $itemsdetails->item_store_area . ', ' .
                   'added_by: ' . $itemsdetails->added_by;


                // Insert into track table
DB::table('trackitemstable')->insert([

    'title' => "data inserted",
    'updated_by' => session('user_email'),
    'notes' => $additional_info,
   

]);
                
 
                 return redirect()->route('items.index')->with('success', 'Items Added Successfully !!');
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
    $all=Myfirm::orderBy('id','DESC')->get();

    return view('items.edit',['item'=>$items,'breadcrumb'=>$breadcrumb,'all'=>$all]);   
    
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
            'unit' => 'required',
            'itemstorearea' => 'required',
        ]);

        if ($validator->passes()) {
            // Fetch the company based on the selected company ID
            $company = Company::find($req->companyid);

            // Check if the company is found
            if ($company) {
  // Fetch the existing item details before updating
  $oldItemDetails = Item::find($id);

   // Construct the additional_info string with old and new values
   $additional_info = 'Initial: ' . 
   'billno: <strong>' . $oldItemDetails->billno . '</strong>, ' .
   'Updated to: ' . 'billno: ' . $req->billno . ', ||' .
   ' companyid: <strong>' . $oldItemDetails->companyid . '</strong>, ' .
   'Updated to: ' . 'companyid: ' . $req->companyid . ', ||' .
   ' date: <strong>' . $oldItemDetails->date . '</strong>, ' .
   'Updated to: ' . 'date: ' . $req->date . ',  ||' .
   ' itemsname: <strong>' . $oldItemDetails->itemsname . '</strong>, ' .
   'Updated to: ' . 'itemsname: ' . $req->itemsname . ',  ||' .
   ' quantity: <strong>' . $oldItemDetails->quantity . '</strong>, ' .
   'Updated to: ' . 'quantity: ' . $req->quantity . ',  ||' .
   ' costprice: <strong>' . $oldItemDetails->costprice . '</strong>, ' .
   'Updated to: ' . 'costprice: ' . $req->costprice . ',  ||' .
   ' mrp: <strong>' . $oldItemDetails->mrp . '</strong>, ' .
   'Updated to: ' . 'mrp: ' . $req->mrp . ',  ||' .
   ' showwarning: <strong>' . $oldItemDetails->showwarning . '</strong>, ' .
   'Updated to: ' . 'showwarning: ' . $req->showwarning . ', ||'.
   ' notes: <strong>' . $oldItemDetails->notes . '</strong>, ' .
   'Updated to: ' . 'notes: ' . $req->notes . ', || ' .
   ' firm_name: <strong>' . $oldItemDetails->firm_name . '</strong>, ' .
   'Updated to: ' . 'firm_name: ' . $req->firm_name . ', || ' .
   ' com_Retail_price: <strong>' . $oldItemDetails->com_Retail_price . '</strong>, ' .
   'Updated to: ' . 'com_Retail_price: ' . $req->competetiveretail . ', || ' .
   ' com_wholesale_price: <strong>' . $oldItemDetails->com_wholesale_price . '</strong>, ' .
   'Updated to: ' . 'com_wholesale_price: ' . $req->competetivewholesale . ', || ' .
   ' wholesale_price: <strong>' . $oldItemDetails->wholesale_price . '</strong>, ' .
   'Updated to: ' . 'wholesale_price: ' . $req->wp . ', ||' .
   ' total: <strong>' . $oldItemDetails->total . '</strong>, ' .
   'Updated to: ' . 'total: ' . $req->quantity * $req->costprice . ', ' .
   ' opening_stock: <strong>' . $oldItemDetails->opening_stock . '</strong>, ' .
   'Updated to: ' . 'opening_stock: ' . $req->quantity . ', ||' .
   ' added_by: <strong>' . $oldItemDetails->added_by . '</strong>, ' .
   'Updated to: ' . 'opening_stock: ' . $req->quantity . ', ||' .
   ' added_by: <strong>' . $oldItemDetails->added_by . '</strong>, ' .
   
   'Item Store Area: <strong>' . $oldItemDetails->item_store_area . '</strong>, ' .
   'Updated to: ' . 'Item Store Area: ' . $req->item_store_area . ', ||' .

   'Updated to: ' . 'added_by: ' . session('user_email');

  // Update the item details
  $itemsdetails = Item::find($id);
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
  $itemsdetails->item_store_area = $req->itemstorearea;

  $itemsdetails->added_by = session('user_email');
  $itemsdetails->opening_stock = $req->quantity;

  // Save the updated item details
  $itemsdetails->save();

  // Insert into track table
  DB::table('trackitemstable')->insert([
      'title' => "data updated",
      'updated_by' => session('user_email'),
      'notes' => $additional_info,
  ]);


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

    $itemsdetails=item::findOrFail($id);
    $itemsdetails->delete();


                    //insertintotracktable
// Concatenate all the attributes of $itemsdetails
$additional_info = 'billno: ' . $itemsdetails->billno . ', ' .
'itemid: ' . $itemsdetails->id . ', ' .
'companyid: ' . $itemsdetails->companyid . ', ' .
'date: ' . $itemsdetails->date . ', ' .
'itemsname: ' . $itemsdetails->itemsname . ', ' .
'quantity: ' . $itemsdetails->quantity . ', ' .
'unit: ' . $itemsdetails->unit . ', ' .
'costprice: ' . $itemsdetails->costprice . ', ' .
'mrp: ' . $itemsdetails->mrp . ', ' .
'notes: ' . $itemsdetails->notes . ', ' .
'firm_name: ' . $itemsdetails->firm_name . ', ' .
'com_Retail_price: ' . $itemsdetails->com_Retail_price . ', ' .
'com_wholesale_price: ' . $itemsdetails->com_wholesale_price . ', ' .
'wholesale_price: ' . $itemsdetails->wholesale_price . ', ' .
'showwarning: ' . $itemsdetails->showwarning . ', ' .
'total: ' . $itemsdetails->total . ', ' .
'opening_stock: ' . $itemsdetails->opening_stock . ', ' .
'added_by: ' . $itemsdetails->added_by;


// Insert into track table
DB::table('trackitemstable')->insert([

'title' => "data Deleted",
'updated_by' => session('user_email'),
'notes' => $additional_info,


]);


      return redirect()->route('items.index')->with('success','Deleted Sucesfully !!'); 
    

}

}
