<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Models\CompanyLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\Auth;

class CompanyLedgerBillEntryController extends Controller
{
    use WithPagination;
    public $search = '';

    public function index()
    {
        
    if(Auth::check()){
        
        $breadcrumb= [
            'subtitle'=>'Add RR',
            'title'=>'Add Bills',
            'link'=>'View All '
        ];
   

     return view('companyLedgerBillEntry.list',['breadcrumb'=>$breadcrumb]);   

    }
    return redirect('/login');

}


    

    public function create()
    {

        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Add',
            'title'=>'Add Bill For Company Ledger',
            'link'=>'Add Bill For Company Ledger'
        ]; 
    
        return view('companyLedgerBillEntry.create',['breadcrumb'=>$breadcrumb]);   
    }
    return redirect('/login');

}




    public function store(Request $req)
   {
    
    
    if(Auth::check()){
    $validator=Validator::make($req->all(),[
      'companyid'=>'required',
        'date'=>'required',
        'particulars'=>'required',
        'vt'=>'required',
        'amount'=>'required'
     
       
           
     ]);

    if($validator->passes()){
       dd("oer");
  
        $companypanyment=new CompanyLedger();
        $companypanyment->companyid=$req->companyid;
        // $companypanyment->companyid=$req->customerid;

        $companypanyment->date=$req->date;
        $companypanyment->particulars=$req->particulars;
        $companypanyment->voucher_no=$req->vt;
        $companypanyment->credit=$req->amount;
        $companypanyment->notes=$req->notes;
        $companypanyment->save();

        return redirect()->route('companybillentry.create')->with('success','Customer Added Sucessfully !!');  
    }

    else{
        return redirect()->route('companybillentry.create')->withErrors($validator)->withInput();

    }

   }
   return redirect('/login');

}

   
   public function edit($id)

    {
        
    if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Edit',
            'title'=>'Edit Customers Details',
            'link'=>'Edit Customers Details'
        ];
   
        $company=CompanyLedger::findOrfail($id);

        return view('companyLedgerBillEntry.edit',['com'=>$company,'breadcrumb'=>$breadcrumb]);   
        
    }
    return redirect('/login');

}


    public function update($id, Request $req)
    {
        
    if(Auth::check()){
        $validator=Validator::make($req->all(),[
           
            'particulars'=>'required',
            'vt'=>'required',
            'amount'=>'required', 
           
               
        ]);
      
        if($validator->passes()){
   
            $company= CompanyLedger::find($id);
            $company->date=$req->date;
            $company->particulars=$req->particulars;
            $company->voucher_type=$req->vt;
            $company->debit=$req->amount;
            $company->notes=$req->notes;
            $company->save();
    
            return redirect()->route('companybillentry.index')->with('success','Customer updated Sucessfully !!');  
        }
        else{
            return redirect()->route('companyLedgers.create')->withErrors($validator)->withInput();
    
        }
    
        
    }
    return redirect('/login');

}


    public function destroy($id){


        $cusiddelete=CompanyLedger::findOrFail($id);
        $cusiddelete->delete();
  
        return redirect()->route('companybillentry.index')->with('success',' Deleted sucessfully'); 
        
  }


  public function returnchoosendatehistroy(Request $req)
  {
    // $validator = Validator::make($req->all(), [
    //     'companyid' => 'required',
    // ]);

    // if ($validator->fails()) {
    //     return redirect()->route('items.index')
    //         ->withErrors($validator)
    //         ->withInput();
    // }
    
    if(Auth::check()){
      $breadcrumb= [
          'subtitle'=>'View',
          'title'=>' Company Ledger Details',
          'link'=>' company Ledger Details'
      ];

      $from=date($req->date1);
      $to=date($req->date2);
  

      $cusledgertails=null;
      $debittotalsumwithdate=null;
      $credittotalsumwithdate=null;
      
      $allcusinfo=company::orderBy('id','DESC')->get();  
     
      if($from == "" || $to==""){
       

          $cusledgertails=CompanyLedger::where('companyid', $req->companyid)->get();
          $betweendate=CompanyLedger::where('companyid',$req->companyid)->get();

          $debittotalsumwithdate = $betweendate->sum('debit');
          $credittotalsumwithdate = $betweendate->sum('credit');
       
         
      }else{
        
      

          $betweendate=CompanyLedger::whereBetween('date',[$from,$to])->where('companyid',$req->companyid)->get();
          $debittotalsumwithdate = $betweendate->sum('debit');
          $credittotalsumwithdate = $betweendate->sum('credit');

          
          $cusledgertails=CompanyLedger::whereBetween('date',  [$from,$to])->where('companyid', $req->companyid)->get();         
         
      }

     
    
      
         return view('companyLedgerBillEntry.companyledgersshow',['all'=>$cusledgertails,'allcus'=>$allcusinfo,'dts'=>$debittotalsumwithdate,'cts'=>$credittotalsumwithdate,'breadcrumb'=>$breadcrumb]);      
        //  return view('companyLedgerBillentry.companyledgersshow', compact('cusledgertails', 'allcusinfo', 'debittotalsumwithdate', 'credittotalsumwithdate', 'breadcrumb'))->withErrors($validator)->withInput();
     
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

          
          
          $allcusinfo=company::orderBy('id','DESC')->get();
        
          $afn=null;
          
         
         
          if($from == "" || $to==""){

              $cusledgertails=CompanyLedger::where('companyid', $req->companyid)->get();
              $betweendate=CompanyLedger::where('companyid',$req->companyid)->get();

              $debittotalsumwithdate = $betweendate->sum('debit');
              $credittotalsumwithdate = $betweendate->sum('credit');

              $xd= company::where('id',$req->companyid)->get();
              $afn=$xd;



             
          }else{
              


              $betweendate=CompanyLedger::whereBetween('date',[$from,$to])->where('companyid',$req->companyid)->get();
              $debittotalsumwithdate = $betweendate->sum('debit');
              $credittotalsumwithdate = $betweendate->sum('credit');

              

              $cusledgertails=CompanyLedger::whereBetween('date',  [$from,$to])->where('companyid', $req->companyid)->get();
              
              $xd= company::where('id',$req->companyid)->get();
              $afn=$xd;

              $from=date($req->date1);
              $to=date($req->date2);

             
          }

      
      $pdfview=FacadePdf::setOptions(['dpi' => 150,'defaultFont' => 'dejavu serif'])->loadView('companyLedgerBillEntry.companylhpdf',['all'=>$cusledgertails,'allcus'=>$allcusinfo,'dts'=>$debittotalsumwithdate,'cts'=>$credittotalsumwithdate,'xx'=>$afn,'fromdate'=>$from,'todate'=>$to]);   

    

     return $pdfview->download('invoicet.pdf');

  }
  return redirect('/login');

}

}
