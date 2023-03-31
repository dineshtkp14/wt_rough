<?php

namespace App\Http\Controllers;

use App\Models\customerinfo;
use App\Models\customerledgerdetails;
use App\Models\invoice;
use App\Models\item;
use App\Models\salesitem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;


class CustomerLedgerHistroy extends Controller
{
    
    

    public function returncusbills(Request $req){
 
      
        $allcusinfo=customerinfo::orderBy('id','DESC')->get();  
        $query=invoice::where('customerid',$req->customerid)->get();

        return view('customerledgerhistory.customerbilllist',['all'=>$query],['allcus'=>$allcusinfo]);   
    }




  

        public function returnchoosendatehistroy(Request $req)
        {
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

                $cusledgertails=customerledgerdetails::where('customerid', $req->customerid)->get();
                $betweendate=customerledgerdetails::where('customerid',$req->customerid)->get();

                $debittotalsumwithdate = $betweendate->sum('debit');
                $credittotalsumwithdate = $betweendate->sum('credit');

               
            }else{

                $betweendate=customerledgerdetails::whereBetween('date',[$from,$to])->where('customerid',$req->customerid)->get();
                $debittotalsumwithdate = $betweendate->sum('debit');
                $credittotalsumwithdate = $betweendate->sum('credit');

                
                $cusledgertails=customerledgerdetails::whereBetween('date',  [$from,$to])->where('customerid', $req->customerid)->get();         
               
            }

           
            return view('customerledgerhistory.list',['all'=>$cusledgertails,'allcus'=>$allcusinfo,'dts'=>$debittotalsumwithdate,'cts'=>$credittotalsumwithdate,'breadcrumb'=>$breadcrumb]);      
        }

     
    
      
    
            public function PdfGenerateCustomerDetails(Request $req)
            {
    
                $from=date($req->date1);
                $to=date($req->date2);
                 
             
                $cusledgertails=null;
                
                $debittotalsumwithdate=null;
                $credittotalsumwithdate=null;

                
                
                $allcusinfo=customerinfo::orderBy('id','DESC')->get();
              
                $afn=null;
                
               
               
                if($from == "" || $to==""){
    
                    $cusledgertails=customerledgerdetails::where('customerid', $req->customerid)->get();
                    $betweendate=customerledgerdetails::where('customerid',$req->customerid)->get();
    
                    $debittotalsumwithdate = $betweendate->sum('debit');
                    $credittotalsumwithdate = $betweendate->sum('credit');

                    $xd= customerinfo::where('id',$req->customerid)->get();
                    $afn=$xd;



                   
                }else{
                    
    
    
                    $betweendate=customerledgerdetails::whereBetween('date',[$from,$to])->where('customerid',$req->customerid)->get();
                    $debittotalsumwithdate = $betweendate->sum('debit');
                    $credittotalsumwithdate = $betweendate->sum('credit');
    
                    
    
                    $cusledgertails=customerledgerdetails::whereBetween('date',  [$from,$to])->where('customerid', $req->customerid)->get();
                    
                    $xd= customerinfo::where('id',$req->customerid)->get();
                    $afn=$xd;

                    $from=date($req->date1);
                    $to=date($req->date2);
    
                   
                }
    
            
            $pdfview=FacadePdf::setOptions(['dpi' => 150,'defaultFont' => 'dejavu serif'])->loadView('customerledgerhistory.customerLedgerDetailsConvertPdf',['all'=>$cusledgertails,'allcus'=>$allcusinfo,'dts'=>$debittotalsumwithdate,'cts'=>$credittotalsumwithdate,'xx'=>$afn,'fromdate'=>$from,'todate'=>$to]);   
    
          
    
           return $pdfview->download('invoice.pdf');
    
        }

        public function returnBillsDEtailsByInvoiceid(Request $req){
            $breadcrumb= [
                'subtitle'=>'',
                'title'=>'Search Bill No',
                'link'=>'Search Bill No'
            ];
           
                
                $itemsname= item::where('id',$req->customerid)->get();
                $invoiceid= $req->invoiceid;


                $allInvoices=invoice::where('id',$req->invoiceid)->get();

                $allcusbyid=salesitem::where('invoiceid',$req->invoiceid)->get();
                $customerinfodetails=null;

               foreach($allcusbyid as  $data){
                if($data->co){
                    $item_name=item::where('id',$data->itemid)->select('itemsname')->first();
                    $data->itemid = $item_name->itemsname;
                }else{
                    $data->itemid = $data->unstockedname;
                }
            }
                foreach($allInvoices as  $data){
                    if($data->customerid){
                        $customerinfodetails=customerinfo::where('id',$data->customerid)->get();
                      
                    }

                
               }    

        return view('customerledgerhistory.customerBillsDetailsByInvoideId',['allinvoices'=>$allInvoices,'allcusbyid'=>$allcusbyid,'itemsname'=>$itemsname,'invoiceid'=>$invoiceid,'cinfodetails'=>$customerinfodetails,'breadcrumb'=>$breadcrumb]);   

            }



        public function showPDF_InvoiveBillByBillno(Request $req){

            $invoiceid= $req->invoiceid;
            $allInvoices=invoice::where('id',$req->invoiceid)->get();
            $allcusbyid=salesitem::where('invoiceid',$req->invoiceid)->get();

           foreach($allcusbyid as  $data){

            if($data->itemid){
                $item_name=item::where('id',$data->itemid)->select('itemsname')->first();
                $data->itemid = $item_name->itemsname;
            }else{
                $data->itemid = $data->unstockedname;
            }

         
        }
        foreach($allInvoices as  $data){
            if($data->customerid){
                $customerinfodetails=customerinfo::where('id',$data->customerid)->get();
              
            }
        }
        
        $pdfviewe=FacadePdf::setOptions(['dpi' => 150,'defaultFont' => 'dejavu serif'])->loadView('customerledgerhistory.customerbillnoinvoiceconvertpdf',['allinvoices'=>$allInvoices,'allcusbyid'=>$allcusbyid,'invoiceid'=>$invoiceid,'cinfodetails'=>$customerinfodetails]);     

           return $pdfviewe->download('invoice.pdf');

        }
       

        }

        
    