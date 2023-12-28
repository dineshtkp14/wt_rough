<?php

namespace App\Http\Controllers;

use App\Models\customerinfo;
use App\Models\customerledgerdetails;
use App\Models\invoice;
use App\Models\item;
use App\Models\salesitem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\Auth;

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
                'title'=>' Customers Ledger Details',
                'link'=>' Customers Ledger Details'
            ];

            $from=date($req->date1);
            $to=date($req->date2);
        

            $cusledgertails=null;
            $debittotalsumwithdate=null;
            $credittotalsumwithdate=null;
            
            $allcusinfo=customerinfo::orderBy('id','DESC')->get();  
           
            if($from == "" || $to==""){

                // $cusledgertails=customerledgerdetails::where('customerid', $req->customerid)->get();
                $cusledgertails = customerledgerdetails::where('customerid', $req->customerid)
                ->where('invoicetype', 'credit')
                ->get();
                $betweendate=customerledgerdetails::where('customerid',$req->customerid)->where('invoicetype', 'credit')->get();

                $debittotalsumwithdate = $betweendate->sum('debit');
                $credittotalsumwithdate = $betweendate->sum('credit');

               
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
        


        return redirect('/login');
    }
        public function showPDF_InvoiveBillByBillno(Request $req){
            if(Auth::check()){

            $invoiceid= $req->invoiceid;
            $allInvoices=invoice::where('id',$req->invoiceid)->get();
            $allcusbyid=salesitem::where('invoiceid',$req->invoiceid)->get();

            $cusleddetaiforinvoicetype = customerledgerdetails::where('invoiceid', $req->invoiceid)->get();
            $forinvoicetype = $cusleddetaiforinvoicetype->first(); 

           foreach($allcusbyid as  $data){

            $item = item::where('id', $data->itemid)->select('itemsname', 'mrp')->first();
                if ($item) {
                    $data->itemid = $item->itemsname;
                    $data->mrp = $item->mrp;
                } else {
                    $data->itemid = $data->unstockedname;
                }

         
        }
        foreach($allInvoices as  $data){
            if($data->customerid){
                $customerinfodetails=customerinfo::where('id',$data->customerid)->get();
              
            }
        }
        
        $pdfviewe=FacadePdf::setOptions(['dpi' => 150,'defaultFont' => 'dejavu serif'])->loadView('customerledgerhistory.customerbillnoinvoiceconvertpdf',['allinvoices'=>$allInvoices,'allcusbyid'=>$allcusbyid,'invoiceid'=>$invoiceid,'cinfodetails'=>$customerinfodetails,  'forinvoicetype'=>$forinvoicetype,
    ]);     

           return $pdfviewe->download('invoice.pdf');

        }
       
        return redirect('/login');
    }
        }

        
    