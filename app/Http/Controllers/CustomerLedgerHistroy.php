<?php

namespace App\Http\Controllers;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\customerinfo;
use App\Models\customerledgerdetails;
use App\Models\invoice;
use App\Models\item;
use App\Models\Trackinvoice;


use App\Models\salesitem;

use App\Models\BackupSalesItem;
use App\Models\BackupInvoice;
use App\Models\CreditnotesCustomerledgerdetail;

use App\Models\BackupCustomerLedgerDetails;


use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; //
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Carbon;



class CustomerLedgerHistroy extends Controller
{
    

    public function returncusbills(Request $req){
        
                if(Auth::check()){

                $allcusinfo=customerinfo::orderBy('id','DESC')->get();  
                $query=invoice::where('customerid',$req->customerid)->get();

                return view('customerledgerhistory.customerbilllist',['all'=>$query],['allcus'=>$allcusinfo]);   
            }


    return redirect('/login');
 }

  
 public function returnchoosendatehistroy(Request $req)
 {
     if(Auth::check()) {
         $breadcrumb = [
             'subtitle' => 'View',
             'title' => 'Customers Ledger Details (ONLY CREDIT)',
             'link' => 'Customers Ledger Details (ONLY CREDIT)'
         ];
 
         $from = date($req->date1);
         $to = date($req->date2);
 
         $cusledgertails = null;
         $debittotalsumwithdate = null;
         $credittotalsumwithdate = null;
 
         $allcusinfo = customerinfo::orderBy('id', 'DESC')->get();  
        $customeridonly=$req->customerid;

         if($from == "" || $to == "") {
             $cusledgertails = Customerledgerdetails::where('customerid', $req->customerid)
                 ->where(function($query) {
                     $query->where('invoicetype', 'credit')
                           ->orWhere('invoicetype', 'payment');
                 })
                 ->get();
 
             $querycheck = customerledgerdetails::where('customerid', $req->customerid)
                 ->where(function($query) {
                     $query->where('invoicetype', 'credit')
                           ->orWhere('invoicetype', 'payment');
                 })
                 ->get();
 
             $debittotalsumwithdate = $querycheck->sum('debit');
             $credittotalsumwithdate = $querycheck->sum('credit');

             $xd = customerinfo::where('id', $req->customerid)->get();
             $afn = $xd;
         } 
         
         else {
            $betweendate = customerledgerdetails::where('customerid', $req->customerid)
            ->where(function ($query) use ($from, $to) {
                $query->where('invoicetype', 'credit')
                      ->orWhere('invoicetype', 'payment');
            })
            ->whereBetween('date', [$from, $to])
            ->get();
        
             
             $debittotalsumwithdate = $betweendate->sum('debit');
             $credittotalsumwithdate = $betweendate->sum('credit');
 
             $cusledgertails = customerledgerdetails::whereBetween('date',  [$from,$to])
    ->where('customerid', $req->customerid)
    ->where(function ($query) {
        $query->where('invoicetype', 'credit')
            ->orWhere('invoicetype', 'payment');
    })
    ->get();
    $xd = customerinfo::where('id', $req->customerid)->get();
    $afn = $xd;
        
         }
 
         return view('customerledgerhistory.list', [
             'all' => $cusledgertails,
             'allcus' => $allcusinfo,
             'dts' => $debittotalsumwithdate,
             'cts' => $credittotalsumwithdate,
             'breadcrumb' => $breadcrumb,
             'fromdate' => $from,
             'todate' => $to,
             'customeridonly' => $customeridonly,
             'cusinfobyid' => $afn,

         ]);      
     }
 
     return redirect('/login');
 }
 

 public function PdfGenerateCustomerDetails(Request $req)
 {
     if (Auth::check()) {
        $from = date($req->date1);
        $to = date($req->date2);

        $cusledgertails = null;
        $debittotalsumwithdate = null;
        $credittotalsumwithdate = null;

        $customeridonly=$req->customerid;


        $allcusinfo = customerinfo::orderBy('id', 'DESC')->get();  

        if($from == "" || $to == "") {
            $cusledgertails = customerledgerdetails::where('customerid', $req->customerid)
                ->where(function($query) {
                    $query->where('invoicetype', 'credit')
                          ->orWhere('invoicetype', 'payment');
                })
                ->get();

            $querycheck = customerledgerdetails::where('customerid', $req->customerid)
                ->where(function($query) {
                    $query->where('invoicetype', 'credit')
                          ->orWhere('invoicetype', 'payment');
                })
                ->get();

            $debittotalsumwithdate = $querycheck->sum('debit');
            $credittotalsumwithdate = $querycheck->sum('credit');

            $xd = customerinfo::where('id', $req->customerid)->get();
            $afn = $xd;
        } 
        
        else {
           $betweendate = customerledgerdetails::where('customerid', $req->customerid)
           ->where(function ($query) use ($from, $to) {
               $query->where('invoicetype', 'credit')
                     ->orWhere('invoicetype', 'payment');
           })
           ->whereBetween('date', [$from, $to])
           ->get();
       
            
            $debittotalsumwithdate = $betweendate->sum('debit');
            $credittotalsumwithdate = $betweendate->sum('credit');

            $cusledgertails = customerledgerdetails::whereBetween('date',  [$from,$to])
   ->where('customerid', $req->customerid)
   ->where(function ($query) {
       $query->where('invoicetype', 'credit')
           ->orWhere('invoicetype', 'payment');
   })
   ->get();

   $xd = customerinfo::where('id', $req->customerid)->get();
   $afn = $xd;
       
        }
 
         $pdfview = view('customerledgerhistory.customerLedgerDetailsConvertPdf', [
             'all' => $cusledgertails,
             'allcus' => $allcusinfo,
             'dts' => $debittotalsumwithdate,
             'cts' => $credittotalsumwithdate,
             'cusinfobyid' => $afn,
             'fromdate' => $from,
             'todate' => $to,
             'customeridonly' => $customeridonly,
         ]);
 
         // Generate PDF using FacadePdf
         $pdf = FacadePdf::setOptions(['dpi' => 150, 'defaultFont' => 'dejavu serif'])->loadHtml($pdfview);
 
         // Save the PDF to a temporary file
         $pdfFile = tempnam(sys_get_temp_dir(), 'invoice');
         $pdf->save($pdfFile);
 
         // Send headers to instruct the browser to open the PDF in a new tab
         return response()->file($pdfFile, [
             'Content-Type' => 'application/pdf',
             'Content-Disposition' => 'inline; filename="onlycreditinvoice.pdf"',
         ]);
     }
 
     return redirect('/login');
 }
 


    


    public function deletebillfromdatabase(Request $req)
    {
        // Check if the user's email is the admin's email
        $user_email = $req->session()->get('user_email');
    
        if ($user_email === 'dineshtkp14@gmail.com') {
            // Admin can delete without any date restrictions
            $validator = Validator::make($req->all(), [
                'invoiceid' => 'required',
            ]);
        } else {
            // Regular users can delete only if the current date matches the date in the database
            $validator = Validator::make($req->all(), [
                'invoiceid' => 'required',
            ]);
    
            // Retrieve the date associated with the invoice from the database
            $invoiceDate = DB::table('customerledgerdetails')
                ->where('invoiceid', $req->invoiceid)
                ->value('date');
    
            if (!empty($invoiceDate) && !Carbon::parse($invoiceDate)->isToday()) {
                // If the date doesn't match today's date, return an error message
                return redirect()->route('customer.billno')->with('error', 'Regular users can only delete on the current date.');
            }
        }
    
        // If validation fails, redirect with an error message
        if ($validator->fails()) {
            return redirect()->route('customer.billno')->withErrors($validator)->withInput();
        }
    
        // Retrieve the items from the bill before deleting
        $items = DB::table('salesitems')->where('invoiceid', $req->invoiceid)->get();
    
        // Backup data before deleting
        foreach ($items as $item) {
            $backupSalesItem = new BackupSalesItem();
            $backupSalesItem->invoiceid = $item->invoiceid;
            $backupSalesItem->itemid = $item->itemid;
            $backupSalesItem->unstockedname = $item->unstockedname;
            $backupSalesItem->quantity = $item->quantity;
            $backupSalesItem->price = $item->price;
            $backupSalesItem->subtotal = $item->subtotal;
            $backupSalesItem->added_by = $user_email;
            $backupSalesItem->save();
        }
    
        // Backup invoice data
        $invoice = DB::table('invoices')->where('id', $req->invoiceid)->first();
        if ($invoice) {
            $backupInvoice = new BackupInvoice();
            $backupInvoice->customerid = $invoice->customerid;
            $backupInvoice->subtotal = $invoice->subtotal;
            $backupInvoice->discount = $invoice->discount;
            $backupInvoice->total = $invoice->total;
            $backupInvoice->notes = $invoice->notes;
            $backupInvoice->invoice_id = $req->invoiceid;
            $backupInvoice->inv_type = $invoice->inv_type;
            $backupInvoice->inv_date = $invoice->inv_date;
            $backupInvoice->added_by = $user_email;
            $backupInvoice->save();
        } else {
            // Handle the case when the invoice does not exist
            return redirect()->route('customer.billno')->with('error', 'Invalid invoiceid provided');
        }
    
        // Backup customer ledger details
        $ledgerDetails = DB::table('customerledgerdetails')->where('invoiceid', $req->invoiceid)->get();
        foreach ($ledgerDetails as $ledger) {
            $backupLedger = new BackupCustomerLedgerDetails();
            $backupLedger->customerid = $ledger->customerid;
            $backupLedger->invoiceid = $ledger->invoiceid;
            $backupLedger->particulars = $ledger->particulars;
            $backupLedger->voucher_type = $ledger->voucher_type;
            $backupLedger->date = $ledger->date;
            $backupLedger->debit = $ledger->debit;
            $backupLedger->credit = $ledger->credit;
            $backupLedger->invoicetype = $ledger->invoicetype;
            $backupLedger->notes = $ledger->notes;
            $backupLedger->added_by = $user_email;
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
        $salesItemsDeleted = DB::table('salesitems')->where('invoiceid', $req->invoiceid)->delete();
    
        // Delete records from invoices_tbl
        $invoicesDeleted = DB::table('invoices')->where('id', $req->invoiceid)->delete();
    
        // Delete records from customerledgerdetails_tbl
        $ledgerDetailsDeleted = DB::table('customerledgerdetails')->where('invoiceid', $req->invoiceid)->delete();
    
        // Check if any records were deleted
        if ($salesItemsDeleted || $invoicesDeleted || $ledgerDetailsDeleted) {
            // Insert into track table

            $billno=$req->invoiceid;
            Trackinvoice::create([
                'bill_no' => $billno, // Assuming $billno contains the invoice ID
                'title' => "invoice_deleted",
                'updated_by' => $user_email,
                'notes' => 'Invoice Id: ' . $billno . ' is deleted by ' . $user_email
            ]);
            return redirect()->route('customer.billno')->with('deletesuccess', 'Deleted Successfully !!');
        } else {
            return redirect()->route('customer.billno')->with('error', 'No records found for the provided invoiceid');
        }
    }
    



    public function deletebillfromdatabasefor_user(Request $req)
    {
       
        
        // Check if the user's email is the admin's email
        $user_email = $req->session()->get('user_email');
    
        if ($user_email === 'dineshtkp14@gmail.com') {
            // Admin can delete without any date restrictions
            $validator = Validator::make($req->all(), [
                // 'invoiceid' => 'required',
            ]);
        } else {
            // Regular users can delete only if the current date matches the date in the database
            $validator = Validator::make($req->all(), [
                // 'invoiceid' => 'required',
            ]);
    
            // Retrieve the date associated with the invoice from the database
            $invoiceDate = DB::table('customerledgerdetails')
                ->where('invoiceid', $req->invoiceid)
                ->value('date');
    
            if (!empty($invoiceDate) && !Carbon::parse($invoiceDate)->isToday()) {
                // If the date doesn't match today's date, return an error message
                return redirect()->route('onlyviewbillafterbill')->with('error', 'Regular users can only delete on the current date.');
            }
        }
    
        // If validation fails, redirect with an error message
        if ($validator->fails()) {
            return redirect()->route('onlyviewbillafterbill')->withErrors($validator)->withInput();
        }
    
        // Retrieve the items from the bill before deleting
        $items = DB::table('salesitems')->where('invoiceid', $req->invoiceid)->get();
    
        // Backup data before deleting
        foreach ($items as $item) {
            $backupSalesItem = new BackupSalesItem();
            $backupSalesItem->invoiceid = $item->invoiceid;
            $backupSalesItem->itemid = $item->itemid;
            $backupSalesItem->unstockedname = $item->unstockedname;
            $backupSalesItem->quantity = $item->quantity;
            $backupSalesItem->price = $item->price;
            $backupSalesItem->subtotal = $item->subtotal;
            $backupSalesItem->added_by = $user_email;
            $backupSalesItem->save();
        }
    
        // Backup invoice data
        $invoice = DB::table('invoices')->where('id', $req->invoiceid)->first();
        if ($invoice) {
            $backupInvoice = new BackupInvoice();
            $backupInvoice->customerid = $invoice->customerid;
            $backupInvoice->subtotal = $invoice->subtotal;
            $backupInvoice->discount = $invoice->discount;
            $backupInvoice->total = $invoice->total;
            $backupInvoice->notes = $invoice->notes;
            $backupInvoice->invoice_id = $req->invoiceid;
            $backupInvoice->inv_type = $invoice->inv_type;
            $backupInvoice->inv_date = $invoice->inv_date;
            $backupInvoice->added_by = $user_email;
            $backupInvoice->save();
        } else {
            // Handle the case when the invoice does not exist
            return redirect()->route('onlyviewbillafterbill')->with('error', 'Invalid invoiceid provided');
        }
    
        // Backup customer ledger details
        $ledgerDetails = DB::table('customerledgerdetails')->where('invoiceid', $req->invoiceid)->get();
        foreach ($ledgerDetails as $ledger) {
            $backupLedger = new BackupCustomerLedgerDetails();
            $backupLedger->customerid = $ledger->customerid;
            $backupLedger->invoiceid = $ledger->invoiceid;
            $backupLedger->particulars = $ledger->particulars;
            $backupLedger->voucher_type = $ledger->voucher_type;
            $backupLedger->date = $ledger->date;
            $backupLedger->debit = $ledger->debit;
            $backupLedger->credit = $ledger->credit;
            $backupLedger->invoicetype = $ledger->invoicetype;
            $backupLedger->notes = $ledger->notes;
            $backupLedger->added_by = $user_email;
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
        $salesItemsDeleted = DB::table('salesitems')->where('invoiceid', $req->invoiceid)->delete();
    
        // Delete records from invoices_tbl
        $invoicesDeleted = DB::table('invoices')->where('id', $req->invoiceid)->delete();
    
        // Delete records from customerledgerdetails_tbl
        $ledgerDetailsDeleted = DB::table('customerledgerdetails')->where('invoiceid', $req->invoiceid)->delete();
    
        // Check if any records were deleted
        if ($salesItemsDeleted || $invoicesDeleted || $ledgerDetailsDeleted) {
            // Insert into track table
            $query = Trackinvoice::create([
                'title' => "invoice_deleted",
                'updated_by' => $user_email,
                'notes' => 'Invoice Id: ' . $req->invoiceid . ' is deleted by ' . $user_email
            ]);
            

            return redirect()->route('onlyviewbillafterbill')->with('deletesuccess', 'Deleted Successfully !!');
        } else {
            return redirect()->route('customer.deletebillnoforuser')->with('error', 'No records found for the provided invoiceid');
        }
    }
    








    public function updatecustomername(Request $req)
    {
        // Check if the user's email is the admin's email
        $user_email = $req->session()->get('user_email');
    
        if ($user_email === 'dineshtkp14@gmail.com') {
            // Admin can update on any date
            $validator = Validator::make($req->all(), [
                'Bill_No' => 'required',
                'customerid' => 'required',
            ]);
    
            if ($validator->passes()) {


                 //retrivecustomerid
                 $initial_customer_id = DB::table('customerledgerdetails')
                 ->where('invoiceid', $req->Bill_No)
                 ->value('customerid');


                // Update customerledgerdetails table
                DB::table('customerledgerdetails')
                    ->where('invoiceid', $req->Bill_No)
                    ->update(['customerid' => $req->customerid]);
    
                // Update invoices table
                DB::table('invoices')
                    ->where('id', $req->Bill_No)
                    ->update(['customerid' => $req->customerid]);
    
                // Insert into track table
                Trackinvoice::create([
                    'bill_no' => $req->Bill_No,
                    'title' => "customer_name_updated",
                    'updated_by' => $user_email,
                    'notes' => 'Initial Customer ID ' . $initial_customer_id . ' updated to Customer ID ' . $req->customerid . ' for invoice/bill No (' . $req->Bill_No .  ') by ' . $user_email
                ]);
    
                return redirect()->route('customer.billno')->with('updatesuccesscusname', 'Updated customer name Successfully !!');
            } else {
                // Redirect with an error message if validation fails
                return redirect()->route('customer.billno')->withErrors($validator)->withInput();
            }
        } else {
            // Check if the date of the request is today's date for regular users
            $ledgerDate = DB::table('customerledgerdetails')
                ->where('invoiceid', $req->Bill_No)
                ->value('date');
    
            if (!empty($ledgerDate) && Carbon::parse($ledgerDate)->isToday()) {
                $validator = Validator::make($req->all(), [
                    'Bill_No' => 'required',
                    'customerid' => 'required',
                ]);
    
                if ($validator->passes()) {

                   
                    //retrivecustomerid
                    $initial_customer_id = DB::table('customerledgerdetails')
                    ->where('invoiceid', $req->Bill_No)
                    ->value('customerid');


                    // Update customerledgerdetails table
                    DB::table('customerledgerdetails')
                        ->where('invoiceid', $req->Bill_No)
                        ->update(['customerid' => $req->customerid]);
    
                    // Update invoices table
                    DB::table('invoices')
                        ->where('id', $req->Bill_No)
                        ->update(['customerid' => $req->customerid]);
    
                    // Insert into track table
                    Trackinvoice::create([

                    // DB::table('Trackinvoice')->insert([thistablemakechange
                        'bill_no' => $req->Bill_No,
                        'title' => "customer_name_updated",
                        'updated_by' => $user_email,

                        'notes' => 'Initial Customer ID ' . $initial_customer_id . ' updated to  Customer ID ' . $req->customerid . ' for invoice/bill No (' . $req->Bill_No .  ') by ' . $user_email

                    ]);
    
                    return redirect()->route('customer.billno')->with('updatesuccesscusname', 'Updated customer name Successfully !!');
                } else {
                    // Redirect with an error message if validation fails
                    return redirect()->route('customer.billno')->withErrors($validator)->withInput();
                }
            } else {
                return redirect()->route('customer.billno')->with('updateerrorcusname', 'Regular users can only update on the current date.');
            }
        }
    }
    







   
    public function updateinvoiicetype(Request $req)
    {
        // Check if the user's email is the admin's email
        $user_email = $req->session()->get('user_email');
    
        if ($user_email === 'dineshtkp14@gmail.com') {
            // Admin can update without any date restrictions
            $validator = Validator::make($req->all(), [
                'updateinvoiceid' => 'required',
                'invoicetype' => 'required|in:credit,cash',
            ]);
        } else {
            // Regular users can update only if the current date matches the date in the database
            $validator = Validator::make($req->all(), [
                'updateinvoiceid' => 'required',
                'invoicetype' => 'required|in:credit,cash',
            ]);
    
            // Retrieve the date associated with the invoice type from the database
            $invoiceDate = DB::table('customerledgerdetails')
                ->where('invoiceid', $req->updateinvoiceid)
                ->value('date');
    
            // Check if the date is today's date
            if (!empty($invoiceDate) && Carbon::parse($invoiceDate)->isToday()) {
                // Validation passes
            } else {
                // Date doesn't match today's date, return with an error message
                return redirect()->route('customer.billno')->with('updateerror', 'Regular users can only update on the current date.');
            }
        }
    
        if ($validator->fails()) {
            // Redirect with an error message if validation fails
            return redirect()->route('customer.billno')->withErrors($validator)->withInput();
        }
    
        // Check if the selected value is not the default "Open this select menu"
        $invoiceExists = DB::table('customerledgerdetails')->where('invoiceid', $req->updateinvoiceid)->exists();
    
        if (!$invoiceExists) {
            return redirect()->route('customer.billno')->with('updateerror', 'No records found for the provided invoiceid');
        }
    
        // Retrieve the initial invoice type from customerledgerdetails
        $initialinvoicetype = DB::table('customerledgerdetails')
            ->where('invoiceid', $req->updateinvoiceid)
            ->value('invoicetype');
    
        // Update customerledgerdetails table
        DB::table('customerledgerdetails')
            ->where('invoiceid', $req->updateinvoiceid)
            ->update(['invoicetype' => $req->invoicetype]);
    
        // Update invoices table
        DB::table('invoices')
            ->where('id', $req->updateinvoiceid)
            ->update(['inv_type' => $req->invoicetype]);
    
        // Insert into track table
        Trackinvoice::create([
            'bill_no' => $req->updateinvoiceid,
            'title' => "invoice_type_updated",
            'updated_by' => $user_email,
            'notes' => 'Initial invoice type : ' . $initialinvoicetype . ' is updated to invoicetype: ' . $req->invoicetype . ' of invoice/bill No (' . $req->updateinvoiceid . ')  by ' . $user_email
        ]);
    
        return redirect()->route('customer.billno')->with('updatesuccess', 'Updated Invoice Type Successfully !!');
    }
    








            public function returnBillsDEtailsByInvoiceid(Request $req)
            {
                if(Auth::check()){

                $breadcrumb = [
                    'subtitle' => '',
                    'title' => 'Search Bill No',
                    'link' => 'Search Bill No'
                ];
                $forinvoicetype=NULL;
            
                $itemsname = item::where('id', $req->customerid)->get();
                $invoiceid = $req->invoiceid;
            
                $allInvoices = invoice::where('id', $req->invoiceid)->get();
            
                $allcusbyid = salesitem::where('invoiceid', $req->invoiceid)->get();
                $customerinfodetails = null;

                $cusleddetaiforinvoicetype = customerledgerdetails::where('invoiceid', $req->invoiceid)->get();
                $forinvoicetype = $cusleddetaiforinvoicetype->first(); // G  
                
              // Check if any record is found


       
            
                foreach ($allcusbyid as $data) {
                    $item = item::where('id', $data->itemid)->select('id','itemsname', 'mrp','unit')->first();
                    if ($item) {
                        $data->itemid = $item->itemsname;
                        $data->itemidorg = $item->id;

                        $data->mrp = $item->mrp;
                        $data->unit = $item->unit;
                    } else {
                        $data->itemid = $data->unstockedname;
                    }
                }
            
                foreach ($allInvoices as $data) {
                    if ($data->customerid) {
                        $customerinfodetails = customerinfo::where('id', $data->customerid)->get();
                    }
                }
            
                return view('customerledgerhistory.customerBillsDetailsByInvoideId', [
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

//deletedbill
            public function returndeletedBillsDEtailsByInvoiceid(Request $req)
            {
                if(Auth::check()){

                $breadcrumb = [
                    'subtitle' => '',
                    'title' => 'Search Deleted Bill No00o',
                    'link' => 'Search Deleted Bill No'
                ];
            
                $itemsname = item::where('id', $req->customerid)->get();
                $invoiceid = $req->invoiceid;
            
                $allInvoices = BackupInvoice::where('invoice_id', $req->invoiceid)->get();
            
                $allcusbyid = BackupSalesItem::where('invoiceid', $req->invoiceid)->get();
                $displayaddedby = BackupSalesItem::where('invoiceid', $req->invoiceid)->pluck('added_by')->first();
                $displayaddedbydate = BackupSalesItem::where('invoiceid', $req->invoiceid)->pluck('created_at')->first();

                $customerinfodetails = null;

                $cusleddetaiforinvoicetype = BackupCustomerLedgerDetails::where('invoiceid', $req->invoiceid)->get();
                $forinvoicetype = $cusleddetaiforinvoicetype->first();           
            
                foreach ($allcusbyid as $data) {
                    $item = item::where('id', $data->itemid)->select('itemsname', 'mrp','unit')->first();
                    if ($item) {
                        $data->itemid = $item->itemsname;
                        $data->mrp = $item->mrp;
                        $data->unit = $item->unit;
                    } else {
                        $data->itemid = $data->unstockedname;
                    }
                }
            
                foreach ($allInvoices as $data) {
                    if ($data->customerid) {
                        $customerinfodetails = customerinfo::where('id', $data->customerid)->get();
                    }
                }
               
                return view('deletedbill.deletedbillview', [
                    'allinvoices' => $allInvoices,
                    'allcusbyid' => $allcusbyid,
                    'itemsname' => $itemsname,
                    'invoiceid' => $invoiceid,
                    'cinfodetails' => $customerinfodetails,
                    'forinvoicetype'=>$forinvoicetype,
                    'displayaddedby' => $displayaddedby,
                    'displayaddedbydate' => $displayaddedbydate,
                    'breadcrumb' => $breadcrumb
                ]);
            }
            


        return redirect('/login');
    }
    


    public function showPDF_InvoiveBillByBillno(Request $req)
    {
        if (Auth::check()) {
            $invoiceid = $req->invoiceid;
            $allInvoices = invoice::where('id', $req->invoiceid)->get();
            $allcusbyid = salesitem::where('invoiceid', $req->invoiceid)->get();
    
            $cusleddetaiforinvoicetype = customerledgerdetails::where('invoiceid', $req->invoiceid)->get();
            $forinvoicetype = $cusleddetaiforinvoicetype->first();
    
            foreach ($allcusbyid as  $data) {
                $item = item::where('id', $data->itemid)->select('id','itemsname', 'mrp','unit')->first();
                if ($item) {
                    $data->itemid = $item->itemsname;
                    $data->itemidorg = $item->id;

                    $data->mrp = $item->mrp;
                    $data->unit = $item->unit;
                } else {
                    $data->itemid = $data->unstockedname;
                }
            }
            foreach ($allInvoices as  $data) {
                if ($data->customerid) {
                    $customerinfodetails = customerinfo::where('id', $data->customerid)->get();
                }
            }
    
            // Load the Blade view for the PDF
            $pdfView = view('customerledgerhistory.customerbillnoinvoiceconvertpdf', [
                'allinvoices' => $allInvoices,
                'allcusbyid' => $allcusbyid,
                'invoiceid' => $invoiceid,
                'cinfodetails' => $customerinfodetails,
                'forinvoicetype' => $forinvoicetype,
            ]);
    
            // Generate PDF using FacadePdf
            $pdf = FacadePdf::setOptions(['dpi' => 150, 'defaultFont' => 'dejavu serif'])->loadHtml($pdfView);
    
            // Save the PDF to a temporary file
            $pdfFile = tempnam(sys_get_temp_dir(), 'invoice');
            $pdf->save($pdfFile);
    
            // Send headers to instruct the browser to open the PDF in a new tab
            return response()->file($pdfFile, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="invoice.pdf"',
            ]);
        }

        
    }



    public function returnchoosendatehistroycashandcredit(Request $req)
    {
        if (Auth::check()) {
            $breadcrumb = [
                'subtitle' => 'View  (CASH / CREDIT)',
                'title' => ' Customers Ledger/ Statement (CASH / CREDIT)',
                'link' => ' Customers Ledger Statement (CASH / CREDIT)'
            ];
    
            $customeridfor = $req->customerid;
    
            $forcashreceiptno = customerledgerdetails::where('invoicetype', 'payment')
                ->where('customerid', $customeridfor)
                ->pluck('id')
                ->first();
    
            $creditnoteledger = CreditnotesCustomerledgerdetail::where('customerid', $req->customerid)->get();
            $debittotalcrnotes = $creditnoteledger->sum('debit');
    
            $from = $req->date1;
            $to = $req->date2;
    
            $cusinfoforpdf = customerinfo::where('id', $req->customerid)->get();
    
            $allcusinfo = customerinfo::orderBy('id', 'DESC')->get();
    
            $querycheck = customerledgerdetails::where('customerid', $req->customerid)->orderBy('date', 'DESC');;
            if ($from && $to) {
                $querycheck->whereBetween('date', [$from, $to]);
            }
    
            // Pagination settings
            $perPage = 200; // Adjust according to your needs
            $cusledgertails = $querycheck->paginate($perPage)->appends($req->all());
    
            // Calculate sum values for debit, credit, and debit not cash
            $debittotalsumwithdate = $querycheck->sum('debit');
            $credittotalsumwithdate = $querycheck->sum('credit');
            $debitnotcash = $querycheck->where('invoicetype', '!=', 'cash')->sum('debit');
    
            // Calculate allnotcash and cts before storing them in the session
            $allnotcash = $debitnotcash;
            $cts = $credittotalsumwithdate;
    
          
    
            return view('customerledgerhistory.view_customerallledger_cashandcredit', [
                'cusinfoforpdfok' => $cusinfoforpdf,
                'debittotalcrnotes' => $debittotalcrnotes,
                'creditnoteledger' => $creditnoteledger,
                'allnotcash' => $allnotcash,
                'all' => $cusledgertails,
                'allcus' => $allcusinfo,
                'dts' => $debittotalsumwithdate,
                'cts' => $cts,
                'breadcrumb' => $breadcrumb,
                'cid' => $customeridfor,
                'from' => $from,
                'to' => $to,
                'forcashreceiptno' => $forcashreceiptno,
            ]);
        }
    }
    
    


   
    



public function pdfreturnchoosendatehistroycashandcredit(Request $req)
{
    // Check if user is authenticated
    if (Auth::check()) {
        
        // Breadcrumb information
        $breadcrumb = [
            'subtitle' => 'View',
            'title' => 'Customers Ledger Details Cash Credit',
            'link' => 'Customers Ledger Details'
        ];

        $customeridfor=$req->customerid;
        // Fetch credit note ledger details
        $creditnoteledger = CreditnotesCustomerledgerdetail::where('customerid', $req->customerid)->get();
        $debittotalcrnotes = $creditnoteledger->sum('debit');

        // Get date range from request
        $from = date($req->date1);
        $to = date($req->date2);

        $cusinfoforpdf= customerinfo::where('id',$req->customerid)->get();
        // Initialize variables
        $cusledgertails = null;
        $debittotalsumwithdate = null;
        $credittotalsumwithdate = null;
        $debitnotcash = null;

        // Get all customer information
        $allcusinfo = customerinfo::orderBy('id', 'DESC')->get();

        if ($from == "" || $to == "") {
            // No date range specified, fetch data without filtering by date

            $cusledgertails = customerledgerdetails::where('customerid', $req->customerid)->get();

            $querycheck = customerledgerdetails::where('customerid', $req->customerid)->get();

            // Calculate sums for debit and credit without date filtering
            $debittotalsumwithdate = $querycheck->sum('debit');
            $credittotalsumwithdate = $querycheck->sum('credit');
            $debitnotcash = $querycheck->where('invoicetype', '!=', 'cash')->sum('debit');
        } else {
            // Date range specified, fetch data within the date range

            $betweendate = customerledgerdetails::where('customerid', $req->customerid)->get();

            // Calculate sums for debit and credit within the specified date range
            $debittotalsumwithdate = $betweendate->sum('debit');
            $credittotalsumwithdate = $betweendate->sum('credit');

            $debitnotcash = $betweendate->where('invoicetype', '!=', 'cash')->sum('debit');

            // Fetch customer ledger details within the specified date range
            $cusledgertails = customerledgerdetails::whereBetween('date', [$from, $to])->where('customerid', $req->customerid)->get();
        }

        // Generate PDF view
        $pdfview = view('customerledgerhistory.view_customerallledger_cashandcredit_PDF', [
            'debittotalcrnotes' => $debittotalcrnotes,
            'creditnoteledger' => $creditnoteledger,
            'allnotcash' => $debitnotcash,
            'all' => $cusledgertails,
            'allcus' => $allcusinfo,
            'dts' => $debittotalsumwithdate,
            'cts' => $credittotalsumwithdate,
            'cusinfoforpdfok' => $cusinfoforpdf,
            'breadcrumb' => $breadcrumb,
            'cid' => $customeridfor
        ]);

        // Generate PDF using FacadePdf
        $pdf = FacadePdf::setOptions(['dpi' => 150, 'defaultFont' => 'dejavu serif'])->loadHtml($pdfview);
    
        // Save the PDF to a temporary file
        $pdfFile = tempnam(sys_get_temp_dir(), 'invoice');
        $pdf->save($pdfFile);

        // Send headers to instruct the browser to open the PDF in a new tab
        return response()->file($pdfFile, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }
}

public function oldpricecheck(Request $req)
{
    if (!Auth::check()) return redirect()->route('login');

    $breadcrumb = [
        'subtitle' => '',
        'title'    => 'Check Old Price',
        'link'     => 'Check Old Price',
    ];

    // inputs
    $customerid = $req->input('customerid');
    $from       = $req->input('date1');
    $to         = $req->input('date2');
    $searchxx   = trim($req->input('searchxx', ''));
    $like       = "%{$searchxx}%";

    // only load data after any filter/search
    $searched = $req->filled('customerid') || $req->filled('searchxx') || $req->filled('date1') || $req->filled('date2');

    // resolve table names from your models (you said you already import them)
    $tblSales = (new salesitem)->getTable();
    $tblItem  = (new item)->getTable();
    $tblInv   = (new invoice)->getTable();
    $tblCust  = (new customerinfo)->getTable();

    // defaults so Blade never errors
    $cus = new \Illuminate\Pagination\LengthAwarePaginator(
        collect(), 0, 50, $req->input('page', 1), ['path' => $req->url(), 'query' => $req->query()]
    );
    $cid = null;
    // $allnotcash = 0; $cts = 0; $dts = 0;
    $betweendate = customerledgerdetails::where('customerid', $req->customerid)->get();

    $debittotalsumwithdate = $betweendate->sum('debit');
    $credittotalsumwithdate = $betweendate->sum('credit');

    $debitnotcash = $betweendate->where('invoicetype', '!=', 'cash')->sum('debit');
    $all = new \Illuminate\Pagination\LengthAwarePaginator(
        collect(), 0, 50, $req->input('page', 1), ['path' => $req->url(), 'query' => $req->query()]
    );
    $cusinfoforpdfok = collect();

    if ($searched) {
        $q = salesitem::from($tblSales.' as s')
            ->leftJoin($tblItem.' as it', 'it.id', '=', 's.itemid')
            ->leftJoin($tblInv.' as inv', 'inv.id', '=', 's.invoiceid')
            ->leftJoin($tblCust.' as c', 'c.id', '=', 'inv.customerid');

        if (!empty($customerid)) {
            $q->where('inv.customerid', $customerid);
            $cid = $customerid;

            $cinfo = customerinfo::select('id','name','address','email','phoneno','alternate_phoneno','remarks')
                    ->find($customerid);
            if ($cinfo) $cusinfoforpdfok = collect([$cinfo]);
        }

        if (!empty($from) && !empty($to)) {
            $q->whereBetween('s.date', [$from, $to]);
        }

        if ($searchxx !== '') {
            $q->where(function ($qq) use ($like) {
                $qq->orWhere('it.itemsname', 'like', $like)
                   ->orWhere('s.unstockedname', 'like', $like)
                   ->orWhere('c.name', 'like', $like)
                   ->orWhere('s.invoiceid', 'like', $like)
                   ->orWhere('s.date', 'like', $like)
                   ->orWhereRaw('CAST(s.quantity AS CHAR) LIKE ?', [$like])
                   ->orWhereRaw('CAST(s.price AS CHAR) LIKE ?',    [$like])
                   ->orWhereRaw('CAST(s.subtotal AS CHAR) LIKE ?', [$like]);
            });
        }

        $cus = $q->orderByDesc('s.id')
            ->select([
                's.id','s.date','s.created_at','s.invoiceid','s.unstockedname','s.quantity','s.price','s.subtotal','s.unit',
                'inv.inv_type as inv_type',
                'c.id as customeridx','c.name as customername',
                'it.itemsname as itemname','it.mrp as itemprice','it.costprice as itemdlp',
            ])
            ->paginate(20)
            ->appends($req->only(['customerid','date1','date2','searchxx']));
    }

    // === AJAX branch: return only the HTML block for table+pagination ===
    if ($req->ajax() || $req->boolean('ajax')) {
        $html = view('customerledgerhistory._items_block', [
            'cus'       => $cus,
            'searchxx'  => $searchxx,
        ])->render();

        return response()->json(['html' => $html]);
    }

    // Full page (first load or non-AJAX navigation)
    return view('customerledgerhistory.customersoldpricecheck', [
        'breadcrumb'      => $breadcrumb,
        'cus'             => $cus,
        'cid'             => $cid,
        'from'            => $from,
        'to'              => $to,
        'allnotcash' => $debitnotcash,
        'dts' => $debittotalsumwithdate,
        'cts' => $credittotalsumwithdate,
        
        'all'             => $all,
        'cusinfoforpdfok' => $cusinfoforpdfok,
        'searchxx'        => $searchxx,
        'searched'        => $searched,
    ]);
}







    public function returndeletedinvoice()
    {
        if(Auth::check()){

            $breadcrumb= [
                'subtitle'=>'View',
                'title'=>'View Deleted Invoices',
                'link'=>'View Deleted Invoices'
            ];
       
         $alldata=BackupInvoice::orderBy('id','DESC')->get();
       

         return view('deletedbill.deletedinvoice',['all'=>$alldata,'breadcrumb'=>$breadcrumb]);

       
    }

    return redirect('/login');
    }



    public function onlyviewbillafterbill( Request $req)
    {
        if(Auth::check()){

            $breadcrumb = [
                'subtitle' => '',
                'title' => 'Search Bill No',
                'link' => 'Search Bill No'
            ];
        
            $itemsname = item::where('id', $req->customerid)->get();
            $invoiceid = $req->invoiceid;
        
            $allInvoices = invoice::where('id', $req->invoiceid)->get();
        
            $allcusbyid = salesitem::where('invoiceid', $req->invoiceid)->get();
            $customerinfodetails = null;

            $cusleddetaiforinvoicetype = customerledgerdetails::where('invoiceid', $req->invoiceid)->get();
            $forinvoicetype = $cusleddetaiforinvoicetype->first();           
        
            foreach ($allcusbyid as $data) {
                $item = item::where('id', $data->itemid)->select('id','itemsname', 'mrp','unit')->first();
                if ($item) {
                    $data->itemid = $item->itemsname;
                    $data->itemidorg = $item->id;

                    $data->mrp = $item->mrp;
                    $data->unit = $item->unit;
                } else {
                    $data->itemid = $data->unstockedname;
                }
            }
        
            foreach ($allInvoices as $data) {
                if ($data->customerid) {
                    $customerinfodetails = customerinfo::where('id', $data->customerid)->get();
                }
            }
        
            return view('customerledgerhistory.customerbillonlyview', [
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
    }


