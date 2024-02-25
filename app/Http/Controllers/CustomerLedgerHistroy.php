<?php

namespace App\Http\Controllers;

use App\Models\customerinfo;
use App\Models\customerledgerdetails;
use App\Models\invoice;
use App\Models\item;

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
                    if(Auth::check()){

                    $breadcrumb= [
                        'subtitle'=>'View',
                        'title'=>' Customers Ledger Details (ONLY CREDIT) ',
                        'link'=>' Customers Ledger Details (ONLY CREDIT)'
                    ];

                    $from=date($req->date1);
                    $to=date($req->date2);
                

                    $cusledgertails=null;
                    $debittotalsumwithdate=null;
                    $credittotalsumwithdate=null;
                    
                    $allcusinfo=customerinfo::orderBy('id','DESC')->get();  
                
                    if($from == "" || $to==""){

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


                    
                    }else{

                        $betweendate=customerledgerdetails::where('customerid',$req->customerid)->where('invoicetype', 'credit')->get();
                        $debittotalsumwithdate = $betweendate->sum('debit');
                        $credittotalsumwithdate = $betweendate->sum('credit');

                        
                        $cusledgertails=customerledgerdetails::whereBetween('date',  [$from,$to])->where('customerid', $req->customerid)->where('invoicetype', 'credit')->get();         
                    
                    }

                
                    return view('customerledgerhistory.list',['all'=>$cusledgertails,'allcus'=>$allcusinfo,'dts'=>$debittotalsumwithdate,'cts'=>$credittotalsumwithdate,'breadcrumb'=>$breadcrumb]);      
                }

    
            return redirect('/login');
        }



            public function PdfGenerateCustomerDetails(Request $req)
            {
                if(Auth::check()){

    
                $from=date($req->date1);
                $to=date($req->date2);
                 
             
                $cusledgertails=null;
                
                $debittotalsumwithdate=null;
                $credittotalsumwithdate=null;

                
                
                $allcusinfo=customerinfo::orderBy('id','DESC')->get();
              
                $afn=null;
                
               
               
                if($from == "" || $to==""){
    
                    $cusledgertails = customerledgerdetails::where('customerid', $req->customerid)
                ->where('invoicetype', 'credit')
                ->get();
                $betweendate=customerledgerdetails::where('customerid',$req->customerid)->where('invoicetype', 'credit')->get();

                $debittotalsumwithdate = $betweendate->sum('debit');
                $credittotalsumwithdate = $betweendate->sum('credit');
                    $xd= customerinfo::where('id',$req->customerid)->get();
                    $afn=$xd;



                   
                }else{
                    
                    $betweendate=customerledgerdetails::where('customerid',$req->customerid)->where('invoicetype', 'credit')->get();
                $debittotalsumwithdate = $betweendate->sum('debit');
                $credittotalsumwithdate = $betweendate->sum('credit');

                
                $cusledgertails=customerledgerdetails::whereBetween('date',  [$from,$to])->where('customerid', $req->customerid)->where('invoicetype', 'credit')->get();         
                    $xd= customerinfo::where('id',$req->customerid)->get();
                    $afn=$xd;

                    $from=date($req->date1);
                    $to=date($req->date2);
    
                   
                }
    
            
            $pdfview=FacadePdf::setOptions(['dpi' => 150,'defaultFont' => 'dejavu serif'])->loadView('customerledgerhistory.customerLedgerDetailsConvertPdf',['all'=>$cusledgertails,'allcus'=>$allcusinfo,'dts'=>$debittotalsumwithdate,'cts'=>$credittotalsumwithdate,'xx'=>$afn,'fromdate'=>$from,'todate'=>$to]);   
           return $pdfview->download('invoice.pdf');
    
        }
        return redirect('/login');
    }




    

    public function deletebillfromdatabase(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'invoiceid' => 'required',
        ]);
    
        if ($validator->passes()) {
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
                // $backupSalesItem->discount = $item->discount;
                $backupSalesItem->subtotal = $item->subtotal;
                $backupSalesItem->added_by = session('user_email');

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


                
                $backupInvoice->added_by = session('user_email');


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
            $salesItemsDeleted = DB::table('salesitems')->where('invoiceid', $req->invoiceid)->delete();
    
            // Delete records from invoices_tbl
            $invoicesDeleted = DB::table('invoices')->where('id', $req->invoiceid)->delete();
    
            // Delete records from customerledgerdetails_tbl
            $ledgerDetailsDeleted = DB::table('customerledgerdetails')->where('invoiceid', $req->invoiceid)->delete();
    


    

            // Check if any records were deleted
            if ($salesItemsDeleted || $invoicesDeleted || $ledgerDetailsDeleted) {
       
            // Insert into track table
            DB::table('trackinvoice')->insert([
                
                'bill_no' => $req->invoiceid,
                'title' => "invoice_deleted",
                'updated_by' => session('user_email'),
                'notes' => ' Invoice Id : ' . $req->invoiceid . ' is deleted  by ' . session('user_email')
            ]);
    

                return redirect()->route('customer.billno')->with('deletesuccess', 'Deleted Successfully !!');
            } else {
                return redirect()->route('customer.billno')->with('error', 'No records found for the provided invoiceid');
            }
        } else {
            // Redirect with an error message if invoiceid is not provided
            return redirect()->route('customer.billno')->withErrors($validator)->withInput();
        }
    }




// //updatecustomername
// public function updatecustomername(Request $req)
// {
   
  
//     $validator = Validator::make($req->all(), [
//         'Bill_No' => 'required',
//         'cid' => 'required',
//     ]);
   
//     if ($validator->passes()) {
//         // Check if the selected value is not the default "Open this select menu"
       
       
//         $invoiceExists = DB::table('customerledgerdetails')->where('invoiceid', $req->Bill_No)->exists();

//         if ($invoiceExists) {
//             DB::table('customerledgerdetails')
//                 ->where('invoiceid', $req->Bill_No)
//                 ->update(['customerid' => $req->customerid]);


//                  // Update invoices table
//             DB::table('invoices')
//             ->where('id', $req->Bill_No)
//             ->update(['customerid' => $req->customerid]);

//              // Insert into track table
//              DB::table('trackinvoice')->insert([
//                 'bill_no' => $req->Bill_No,
//                 'customer_id' => $req->customerid,
//                 'title' => "customer_name_updated",
//                 'updated_by' => session('user_email')
//             ]);

//             return redirect()->route('customer.billno')->with('updatesuccesscusname', 'Updated customer name  Successfully !!');
//         } else {
//             return redirect()->route('customer.billno')->with('updateerrorcusname', 'No records found for the provided invoiceid');
//         }
//     } else {
       
//         // Redirect with an error message if validation fails
//         return redirect()->route('customer.billno')->withErrors($validator)->withInput();
//     }


public function updatecustomername(Request $req)
{
    $validator = Validator::make($req->all(), [
        'Bill_No' => 'required',
        'customerid' => 'required',
    ]);

    if ($validator->passes()) {
        // Retrieve the initial customer ID from customerledgerdetails
        $initialCustomerId = DB::table('customerledgerdetails')
            ->where('invoiceid', $req->Bill_No)
            ->value('customerid');

        if ($initialCustomerId === null) {
            return redirect()->route('customer.billno')->with('updateerrorcusname', 'No records found for the provided invoiceid');
        }

        // Update customerledgerdetails table
        DB::table('customerledgerdetails')
            ->where('invoiceid', $req->Bill_No)
            ->update(['customerid' => $req->customerid]);

        // Update invoices table
        DB::table('invoices')
            ->where('id', $req->Bill_No)
            ->update(['customerid' => $req->customerid]);

        // Insert into track table
        DB::table('trackinvoice')->insert([
            
                            'bill_no' => $req->Bill_No,
                            'title' => "customer_name_updated",
                            'updated_by' => session('user_email'),
                            'notes' => 'Initial customer Id: ' . $initialCustomerId . ' is updated to customerid: ' . $req->customerid . ' of invoice/bill No ('.$req->Bill_No.') of the title customer_name_updated by ' . session('user_email')
                        ]);

        return redirect()->route('customer.billno')->with('updatesuccesscusname', 'Updated customer name Successfully !!');
    } else {
        // Redirect with an error message if validation fails
        return redirect()->route('customer.billno')->withErrors($validator)->withInput();
    }
}






public function updateinvoiicetype(Request $req)
{
    $validator = Validator::make($req->all(), [
        'updateinvoiceid' => 'required',
        'invoicetype' => 'required|in:credit,cash',
    ]);

    if ($req->invoicetype == 'check') {
        return redirect()->route('customer.billno')->with('updateerror', 'Please select a valid invoice type');
    }

    if ($validator->passes()) {
        // Check if the selected value is not the default "Open this select menu"
        $invoiceExists = DB::table('customerledgerdetails')->where('invoiceid', $req->updateinvoiceid)->exists();

        if ($invoiceExists) {

            // Retrieve the initial customer ID from customerledgerdetails
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
        DB::table('trackinvoice')->insert([
          
                            'bill_no' => $req->updateinvoiceid,
                            'title' => "invoice_type_updated",
                            'updated_by' => session('user_email'),
                            'notes' => 'Initial invoice type : ' . $initialinvoicetype . ' is updated to invoicetype: ' .$req->invoicetype . ' of invoice/bill No ('.$req->updateinvoiceid.') of the title invoice_type_updated by ' . session('user_email')
                        ]);

            return redirect()->route('customer.billno')->with('updatesuccess', 'Updated Invoice Type Successfully !!');
        } else {
            return redirect()->route('customer.billno')->with('updateerror', 'No records found for the provided invoiceid');
        }
    } else {
        // Redirect with an error message if validation fails
        return redirect()->route('customer.billno')->withErrors($validator)->withInput();
    }
}










            public function returnBillsDEtailsByInvoiceid(Request $req)
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
                    'title' => 'Search Deleted Bill No',
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
                $item = item::where('id', $data->itemid)->select('itemsname', 'mrp','unit')->first();
                if ($item) {
                    $data->itemid = $item->itemsname;
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

        if(Auth::check()){

            $breadcrumb= [
                'subtitle'=>'View  (CASH / CREDIT)',
                'title'=>' Customers Ledger Details ALL (CASH / CREDIT)',
                'link'=>' Customers Ledger Details (CASH / CREDIT)'
            ];

            $customeridfor=$req->customerid;

            

            $creditnoteledger = CreditnotesCustomerledgerdetail::where('customerid', $req->customerid)->get();
            $debittotalcrnotes = $creditnoteledger->sum('debit');

            $from=date($req->date1);
            $to=date($req->date2);
        
            $cusinfoforpdf= customerinfo::where('id',$req->customerid)->get();

            $cusledgertails=null;
            $debittotalsumwithdate=null;
            $credittotalsumwithdate=null;
            $debitnotcash=null;   

            $allcusinfo=customerinfo::orderBy('id','DESC')->get();  

           
            if($from == "" || $to==""){

                $cusledgertails = customerledgerdetails::where('customerid', $req->customerid)->get();
                $querycheck=customerledgerdetails::where('customerid',$req->customerid)->get();
                $debittotalsumwithdate = $querycheck->sum('debit');
                $credittotalsumwithdate = $querycheck->sum('credit');
                $debitnotcash = $querycheck->where('invoicetype', '!=', 'cash')->sum('debit');

               
            }else{

                $betweendate=customerledgerdetails::where('customerid',$req->customerid)->get();
                $debittotalsumwithdate = $betweendate->sum('debit');
                $credittotalsumwithdate = $betweendate->sum('credit');

                $debitnotcash = $betweendate->where('invoicetype', '!=', 'cash')->sum('debit');

                
                $cusledgertails=customerledgerdetails::whereBetween('date',  [$from,$to])->where('customerid', $req->customerid)->get();         
               
            }

           
            return view('customerledgerhistory.view_customerallledger_cashandcredit',['cusinfoforpdfok' => $cusinfoforpdf,
            'debittotalcrnotes'=>$debittotalcrnotes,'creditnoteledger'=>$creditnoteledger,'allnotcash'=>$debitnotcash,'all'=>$cusledgertails,'allcus'=>$allcusinfo,'dts'=>$debittotalsumwithdate,'cts'=>$credittotalsumwithdate,'breadcrumb'=>$breadcrumb, 'cid' => $customeridfor,'from' => $from,'to' => $to]);      
        }

     

        }
    



// public function pdfreturnchoosendatehistroycashandcredit(Request $req)
// {
//     // Check if user is authenticated
//     if (Auth::check()) {
        
//         // Breadcrumb information
//         $breadcrumb = [
//             'subtitle' => 'View',
//             'title' => 'Customers Ledger Details Cash Credit',
//             'link' => 'Customers Ledger Details'
//         ];

//         // Fetch credit note ledger details
//         $creditnoteledger = CreditnotesCustomerledgerdetail::where('customerid', $req->customerid)->get();
//         $debittotalcrnotes = $creditnoteledger->sum('debit');

//         // Get date range from request
//         $from = date($req->date1);
//         $to = date($req->date2);

//         $cusinfoforpdf= customerinfo::where('id',$req->customerid)->get();
       


//         // Initialize variables
//         $cusledgertails = null;
//         $debittotalsumwithdate = null;
//         $credittotalsumwithdate = null;
//         $debitnotcash = null;

//         // Get all customer information
//         $allcusinfo = customerinfo::orderBy('id', 'DESC')->get();

//         if ($from == "" || $to == "") {
//             // No date range specified, fetch data without filtering by date

//             $cusledgertails = customerledgerdetails::where('customerid', $req->customerid)->get();

//             $querycheck = customerledgerdetails::where('customerid', $req->customerid)->get();

//             // Calculate sums for debit and credit without date filtering
//             $debittotalsumwithdate = $querycheck->sum('debit');
//             $credittotalsumwithdate = $querycheck->sum('credit');
//             $debitnotcash = $querycheck->where('invoicetype', '!=', 'cash')->sum('debit');
//         } else {
//             // Date range specified, fetch data within the date range

//             $betweendate = customerledgerdetails::where('customerid', $req->customerid)->get();

//             // Calculate sums for debit and credit within the specified date range
//             $debittotalsumwithdate = $betweendate->sum('debit');
//             $credittotalsumwithdate = $betweendate->sum('credit');

//             $debitnotcash = $betweendate->where('invoicetype', '!=', 'cash')->sum('debit');

//             // Fetch customer ledger details within the specified date range
//             $cusledgertails = customerledgerdetails::whereBetween('date', [$from, $to])->where('customerid', $req->customerid)->get();
//         }

//         // Generate PDF view
//         $pdfview = FacadePdf::setOptions(['dpi' => 150, 'defaultFont' => 'dejavu serif', 'format' => 'A5'])
//             ->loadView('customerledgerhistory.view_customerallledger_cashandcredit_PDF', [
//                 'debittotalcrnotes' => $debittotalcrnotes,
//                 'creditnoteledger' => $creditnoteledger,
//                 'allnotcash' => $debitnotcash,
//                 'all' => $cusledgertails,
//                 'allcus' => $allcusinfo,
//                 'dts' => $debittotalsumwithdate,
//                 'cts' => $credittotalsumwithdate,
//                 'cusinfoforpdfok' => $cusinfoforpdf,

//                 'breadcrumb' => $breadcrumb
//             ]);

//         // Download the PDF
//         return $pdfview->download('invoice.pdf');
//     }
// }



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





    }


