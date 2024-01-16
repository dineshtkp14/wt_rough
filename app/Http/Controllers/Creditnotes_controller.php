<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BackupCreditnotesInvoice;
use App\Models\BackupCreditnotesSalesItem;
use App\Models\BackupCreditnotesCustomerLedgerDetail;

use App\Models\customerledgerdetails;
use App\Models\invoice;


use Illuminate\Support\Facades\Validator;

use App\Models\CreditnotesInvoice;
use App\Models\CreditnotesSalesitem;
use App\Models\CreditnotesCustomerledgerdetail;

use App\Models\customerinfo;
use Illuminate\Support\Facades\DB;
use App\Models\item;




class Creditnotes_controller extends Controller
{
   
    public function index()
    {
        if(Auth::check()){
        $breadcrumb = [
            'subtitle' => 'Credit Notes / Sales Return',
            'title' => 'View Invoice Sales Details / Sales Return',
            'link' => 'View Invoice Sales Details / Sales Return'
        ];
        $cus = CreditnotesSalesitem::orderBy('id', 'DESC')->paginate(20); 
        return view('creditnote.list', compact('cus', 'breadcrumb'));
    }
    return redirect('/login');
}  



    public function create()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Create Credit Notes / Sales Return',
            'title'=>'Credit Notes Invoice / Sales Return',
            'link'=>' Credit Notes Invoice / Sales Return'
        ];

     
        $cus = customerinfo::all();
        $statement  = DB::select("SHOW TABLE STATUS LIKE 'creditnotes_invoices'");
        $nextUserId = $statement[0]->Auto_increment;

        $itemsdata = item::all();
        return view('creditnote.create', ['page' => 'isc', 'all' => $cus, 'data' => $itemsdata,'nextgenid' => $nextUserId,'breadcrumb'=>$breadcrumb]);
    }

    return redirect('/login');
 }
    public function store(Request $req)
    {
        if(Auth::check()){

        $sales_arr = json_decode($req->sales_arr); //rowdetails
        $final_arr = json_decode($req->final_arr); //finaltotalinvoice
        // invoice insert
        $invoice_data = new CreditnotesInvoice();
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
            $data = new CreditnotesSalesitem();
            $data->invoiceid = $invoice_data->id;
            $data->itemid = $value->product == "" ? null : $value->product;

            $data->unstockedname = $value->unstocked;
            $data->quantity = $value->quantity;

            if ($data->itemid) {
              

                DB::table('items')->where('id', $data->itemid)->increment('quantity', $value->quantity);


            }

            $data->price = $value->price;
            $data->discount = $value->discount == "" ? 0.00 : $value->discount;
            $data->subtotal = $value->subtotal;
            $data->added_by = session('user_email');
            $data->save();
        }

        $cus_data = new CreditnotesCustomerledgerdetail();
        $cus_data->customerid = $final_arr[0]->customer;
        $cus_data->invoiceid = $invoice_data->id;
        $cus_data->date = $req->date;
        $cus_data->particulars  = "Goods Sales";
        $cus_data->voucher_type = "sales";
        $cus_data->invoicetype = $req->invoice_type;
        $cus_data->debit =  $final_arr[0]->total;
        $cus_data->added_by = session('user_email');
        $cus_data->save();


       
        // $reutntype = customerledgerdetails::find($req->bilinvoiceid);
        // $reutntype->salesreturn = "yes";
        // $reutntype->save();

        // $reutntype = invoice::find($req->bilinvoiceid);
        // $reutntype->salesreturn = "yes";
        // $reutntype->save();


        if ($req->bilinvoiceid !== null) {
            $reutntype = customerledgerdetails::find($req->bilinvoiceid);
        
            if ($reutntype !== null) {
                $reutntype->salesreturn = "yes";
                $reutntype->returnidforcreditnotes = $req->creditnoteinvoiceid;


                $reutntype->save();
            }
        
            $reutntype = invoice::find($req->bilinvoiceid);
        
            if ($reutntype !== null) {
                $reutntype->salesreturn = "yes";
                $reutntype->returnidforcreditnotes = $req->creditnoteinvoiceid;
                $reutntype->save();
            }
        } 
        

        return redirect()->route('creditnotes.create')->with('success','Invoice Created Sucessfully !!');
                                
       

    }

    return redirect('/login');
 }



 public function edit($id)
 {
     if (Auth::check()) {
         $breadcrumb = [
             'subtitle' => 'Edit credit notes/ Sales Return',
             'title' => 'Edit SalesItem Details / Sales Return',
             'link' => 'Edit SalesItem Details / Sales Return'
         ];
 
         $all = CreditnotesSalesitem::findOrFail($id);
 
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
 

//viewing create note
 public function returnBillsDEtailsByInvoiceidforviewingcreditnotebill(Request $req)
            {
                if(Auth::check()){

                $breadcrumb = [
                    'subtitle' => 'Credit Notes',
                    'title' => 'Search Bill No / Sales Return',
                    'link' => 'Search Bill No / Sales Return'
                ];
            
                $itemsname = item::where('id', $req->customerid)->get();
                $invoiceid = $req->invoiceid;
            
                $allInvoices = CreditnotesInvoice::where('id', $req->invoiceid)->get();
            
                $allcusbyid = CreditnotesSalesitem::where('invoiceid', $req->invoiceid)->get();
                $customerinfodetails = null;

                $cusleddetaiforinvoicetype = CreditnotesCustomerledgerdetail::where('invoiceid', $req->invoiceid)->get();
                $forinvoicetype = $cusleddetaiforinvoicetype->first();           
            
                foreach ($allcusbyid as $data) {
                    $item = item::where('id', $data->itemid)->select('itemsname', 'mrp')->first();
                    if ($item) {
                        $data->itemid = $item->itemsname;
                        $data->mrp = $item->mrp;
                    } else {
                        $data->itemid = $data->unstockedname;
                    }
                }
            
                foreach ($allInvoices as $data) {
                    if ($data->customerid) {
                        $customerinfodetails = customerinfo::where('id', $data->customerid)->get();
                    }
                }
            
                return view('creditnotesinvoice.searchcreditnotebillno', [
                    'allinvoices' => $allInvoices,
                    'allcusbyid' => $allcusbyid,
                    'itemsname' => $itemsname,
                    'invoiceid' => $invoiceid,
                    'cinfodetails' => $customerinfodetails,
                    'forinvoicetype'=>$forinvoicetype,
                    'breadcrumb' => $breadcrumb
                ]);
            }
}



public function returndeletedcnBillsDEtailsByInvoiceid(Request $req)
{
    if(Auth::check()){

    $breadcrumb = [
        'subtitle' => 'DELETED CREDIT NOTES BILL ',
        'title' => 'SEARCH CREDIT NOTES DELETED  BILL NO',
        'link' => 'SEARCH CREDIT NOTES DELETED  BILL NO'
    ];

    $itemsname = item::where('id', $req->customerid)->get();
    $invoiceid = $req->invoiceid;

    $allInvoices = BackupCreditnotesInvoice::where('id', $req->invoiceid)->get();

    $allcusbyid = BackupCreditnotesSalesItem::where('invoiceid', $req->invoiceid)->get();
    $customerinfodetails = null;

    $cusleddetaiforinvoicetype = BackupCreditnotesCustomerLedgerDetail::where('invoiceid', $req->invoiceid)->get();
    $forinvoicetype = $cusleddetaiforinvoicetype->first();           

    foreach ($allcusbyid as $data) {
        $item = item::where('id', $data->itemid)->select('itemsname', 'mrp')->first();
        if ($item) {
            $data->itemid = $item->itemsname;
            $data->mrp = $item->mrp;
        } else {
            $data->itemid = $data->unstockedname;
        }
    }

    foreach ($allInvoices as $data) {
        if ($data->customerid) {
            $customerinfodetails = customerinfo::where('id', $data->customerid)->get();
        }
    }

    return view('deletedbill.deletedbillcreditnotes', [
        'allinvoices' => $allInvoices,
        'allcusbyid' => $allcusbyid,
        'itemsname' => $itemsname,
        'invoiceid' => $invoiceid,
        'cinfodetails' => $customerinfodetails,
        'forinvoicetype'=>$forinvoicetype,
        'breadcrumb' => $breadcrumb
    ]);
}



return redirect('/login');
}



//deletebill from database and keep backup for creditnotes
public function deletebillfromdatabaseforcreditnotes(Request $req)
{
    $validator = Validator::make($req->all(), [
        'invoiceid' => 'required',
    ]);

    if ($validator->passes()) {
        // Retrieve the items from the bill before deleting
        $items = DB::table('creditnotes_salesitems')->where('invoiceid', $req->invoiceid)->get();

        // Backup data before deleting
        foreach ($items as $item) {
            $backupSalesItem = new BackupCreditnotesSalesItem();
            $backupSalesItem->invoiceid = $item->invoiceid;
            $backupSalesItem->itemid = $item->itemid;
            $backupSalesItem->unstockedname = $item->unstockedname;
            $backupSalesItem->quantity = $item->quantity;
            $backupSalesItem->price = $item->price;
            $backupSalesItem->discount = $item->discount;
            $backupSalesItem->subtotal = $item->subtotal;
            $backupSalesItem->added_by = session('user_email');

            $backupSalesItem->save();
        }

        // Backup invoice data
        $invoice = DB::table('creditnotes_invoices')->where('id', $req->invoiceid)->first();

        if ($invoice) {
            $backupInvoice = new BackupCreditnotesInvoice();
            $backupInvoice->customerid = $invoice->customerid;
            $backupInvoice->subtotal = $invoice->subtotal;
            $backupInvoice->discount = $invoice->discount;
            $backupInvoice->total = $invoice->total;
            $backupInvoice->inv_type = $req->invoice_type;

            $backupInvoice->notes = $invoice->notes;
            $backupInvoice->added_by = session('user_email');


            $backupInvoice->save();
        } else {
            // Handle the case when the invoice does not exist
            return redirect()->route('creditnotescustomer.billno')->with('error', 'Invalid invoiceid provided');
        }

        // Backup customer ledger details
        $ledgerDetails = DB::table('creditnotes_customerledgerdetails')->where('invoiceid', $req->invoiceid)->get();
        foreach ($ledgerDetails as $ledger) {
            $backupLedger = new BackupCreditnotesCustomerLedgerDetail();
            $backupLedger->customerid = $ledger->customerid;
            $backupLedger->invoiceid = $ledger->invoiceid;
            $backupLedger->particulars = $ledger->particulars;
            $backupLedger->voucher_type = $ledger->voucher_type;
            $backupLedger->date = $ledger->date;
            $backupLedger->debit = $ledger->debit;
            $backupLedger->credit = $ledger->credit;
            $backupLedger->invoicetype = $ledger->invoicetype;
            $backupLedger->notes = $ledger->notes;
            $backupLedger->added_by = session('user_email');

            $backupLedger->save();
        }

        // Adjust stock quantities before deleting
        foreach ($items as $item) {
            $product = Item::find($item->itemid);
            if ($product) {
                $product->quantity += $item->quantity;
                $product->save();
            }
        }

        // Delete records from salesitem_tbl
        $salesItemsDeleted = DB::table('creditnotes_salesitems')->where('invoiceid', $req->invoiceid)->delete();

        // Delete records from invoices_tbl
        $invoicesDeleted = DB::table('creditnotes_invoices')->where('id', $req->invoiceid)->delete();

        // Delete records from customerledgerdetails_tbl
        $ledgerDetailsDeleted = DB::table('creditnotes_customerledgerdetails')->where('invoiceid', $req->invoiceid)->delete();

        // Check if any records were deleted
        if ($salesItemsDeleted || $invoicesDeleted || $ledgerDetailsDeleted) {
            return redirect()->route('creditnotescustomer.billno')->with('deletesuccess', 'Deleted Successfully !!');
        } else {
            return redirect()->route('creditnotescustomer.billno')->with('error', 'No records found for the provided invoiceid');
        }
    } else {
        // Redirect with an error message if invoiceid is not provided
        return redirect()->route('creditnotescustomer.billno')->withErrors($validator)->withInput();
    }
}








//update creditnotes invicetypebyid
public function updateinvoiicetypeforcreditnotes(Request $req)
{

$validator = Validator::make($req->all(), [
    'updateinvoiceid' => 'required',
    'invoicetype' => 'required|in:credit,cash',
]);

if ($req->invoicetype == 'check') {
   
    return redirect()->route('creditnotescustomer.billno')->with('updateerror', 'Please select a valid invoice type');
}
if ($validator->passes()) {
    // Check if the selected value is not the default "Open this select menu"
   
   
    $invoiceExists = DB::table('creditnotes_customerledgerdetails')->where('invoiceid', $req->updateinvoiceid)->exists();

    if ($invoiceExists) {
        DB::table('creditnotes_customerledgerdetails')
            ->where('invoiceid', $req->updateinvoiceid)
            ->update(['invoicetype' => $req->invoicetype]);

        return redirect()->route('creditnotescustomer.billno')->with('updatesuccess', 'Updated Invoice Type Successfully !!');
    } else {
        return redirect()->route('creditnotescustomer.billno')->with('updateerror', 'No records found for the provided invoiceid');
    }
} else {
    // Redirect with an error message if validation fails
    return redirect()->route('creditnotescustomer.billno')->withErrors($validator)->withInput();
}
}



}