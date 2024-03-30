<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Models\CompanyLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; //

class CompanyLedgerBillEntryController extends Controller
{
    use WithPagination;
    public $search = '';

    public function index()
    {
        
    if(Auth::check()){
        
        $breadcrumb= [
            'subtitle'=>' View',
            'title'=>'View COMPANY BILL ENTRY',
            'link'=>'View COMPANY BILL ENTRY  '
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
            'title'=>'Add Bill ENTRY For Company Ledger',
            'link'=>'Add Bill ENTRY For Company Ledger'
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
        'voucherno'=>'required',
        'amount'=>'required'
     
       
           
     ]);

    if($validator->passes()){
    
  
        $companypanyment=new CompanyLedger();
        $companypanyment->companyid=$req->companyid;
        $companypanyment->date=$req->date;
        $companypanyment->particulars=$req->particulars;
        $companypanyment->voucher_no=$req->voucherno;
        $companypanyment->credit=$req->amount;
        $companypanyment->notes=$req->notes;
        $companypanyment->added_by = session('user_email');
        $companypanyment->save();




          // Construct the additional_info string with old and new values
          $additional_info = 
          'companyid: ' . $req->companyid . ', ' .
          'date: ' . $req->date . ', ' .
          'particulars: ' . $req->particulars . ', ' .
          'voucher_no: ' . $req->voucherno . ', ' .
          'credit: ' . $req->amount . ', ' .
          'notes: ' . $req->notes . ', ' .
          'added_by: ' . session('user_email') . '';


        // Insert into track table
    DB::table('trackcompanybillentry')->insert([
    'title' => "BILLENTRY_data_Inserted",
    'updated_by' => session('user_email'),
    'notes' => $additional_info,
    
    
    ]);

        return redirect()->route('companybillentry.create')->with('success',' Added Sucessfully !!');  
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
            'title'=>'Edit COMPANY BILL ENTRY',
            'link'=>'Edit BILL ENTRY'
        ];
   
        $company=CompanyLedger::findOrfail($id);

        return view('companyLedgerBillEntry.edit',['com'=>$company,'breadcrumb'=>$breadcrumb]);   
        
    }
    return redirect('/login');

}

public function update($id, Request $req)
{
    if (Auth::check()) {
        $validator = Validator::make($req->all(), [
            'particulars' => 'required',
            'vt' => 'required',
            'amount' => 'required',
            'companyid' => 'required',
        ]);

        if ($validator->passes()) {
            // Fetch the company based on the selected company ID
            $company = CompanyLedger::find($req->companyid);

            // Check if the company exists
            if ($company) {
                // Fetch the existing item details before updating
                $oldItemDetails = CompanyLedger::find($id);

                // Update the record with new values
                $company = CompanyLedger::find($id);
                $company->companyid = $req->companyid;
                $company->date = $req->date;
                $company->particulars = $req->particulars;
                $company->voucher_no = $req->vt;
                $company->credit = $req->amount;
                $company->notes = $req->notes;
                $company->added_by = session('user_email');
                $company->save();

                // Construct the additional_info string with old and new values
                $additional_info =
                    'companyid: ' . $oldItemDetails->companyid . ', ' .
                    'date: ' . $oldItemDetails->date . ', ' .
                    'particulars: ' . $oldItemDetails->particulars . ', ' .
                    'voucher_no: ' . $oldItemDetails->voucher_no . ', ' .
                    'credit: ' . $oldItemDetails->credit . ', ' .
                    'notes: ' . $oldItemDetails->notes . ', ' .
                    'added_by: ' . $oldItemDetails->added_by . '' .
                    '<br><br>Updated to: ' .
                    'companyid: ' . $req->companyid . ', ' .
                    'date: ' . $req->date . ', ' .
                    'particulars: ' . $req->particulars . ', ' .
                    'voucher_no: ' . $req->vt . ', ' .
                    'credit: ' . $req->amount . ', ' .
                    'notes: ' . $req->notes . ', ' .
                    'added_by: ' . session('user_email') . '';

                // Insert into track table
                DB::table('trackcompanybillentry')->insert([
                    'title' => "BILLENTRY_data_UPDATE",
                    'updated_by' => session('user_email'),
                    'notes' => $additional_info,
                ]);

                return redirect()->route('companybillentry.index')->with('success', ' Updated Sucessfully !!');
            } else {
                return redirect()->route('companyLedgerspay.create')->with('error', 'Company not found.');
            }
        } else {
            return redirect()->route('companyLedgerspay.create')->withErrors($validator)->withInput();
        }
    }

    return redirect('/login');
}


    public function destroy($id){


        $cusiddelete=CompanyLedger::findOrFail($id);


         // Log the operation before deleting
         DB::table('trackcompanybillentry')->insert([
            'title' => "data deleted",
            'updated_by' => session('user_email'),
            'notes' => 'Deleted companyid: ' . $cusiddelete->companyid . ', date: ' . $cusiddelete->date . ', particulars: ' . $cusiddelete->particulars . ', voucher_no: ' . $cusiddelete->voucher_no . ', credit: ' . $cusiddelete->credit . ', notes: ' . $cusiddelete->notes . ', added_by: ' . $cusiddelete->added_by,
        ]);
        $cusiddelete->delete();
  
        return redirect()->route('companybillentry.index')->with('success',' Deleted sucessfully'); 
        
  }


  public function returnchoosendatehistroy(Request $req)
  {
   
    
    if(Auth::check()){
      $breadcrumb= [
          'subtitle'=>'View',
          'title'=>' Company Ledger Details',
          'link'=>' company Ledger Details'
      ];

      $from=date($req->date1);
      $to=date($req->date2);

     
  
      $companyid=$req->companyid;

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

     
    
      
         return view('companyLedgerBillEntry.companyledgersshow',[
        'all'=>$cusledgertails,
         'allcus'=>$allcusinfo,
         'dts'=>$debittotalsumwithdate,
         'cts'=>$credittotalsumwithdate,
         'breadcrumb'=>$breadcrumb,
         'companyid'=>$companyid,
         'from' => $from,
         'to' => $to,



        ]);      
        //  return view('companyLedgerBillentry.payshow', compact('cusledgertails', 'allcusinfo', 'debittotalsumwithdate', 'credittotalsumwithdate', 'breadcrumb'))->withErrors($validator)->withInput();
     
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

    





      // Load the Blade view for the PDF
      $pdfView = view('companyLedgerBillEntry.companylhpdf', [
        'all'=>$cusledgertails,
        'allcus'=>$allcusinfo,
        'dts'=>$debittotalsumwithdate,
        'cts'=>$credittotalsumwithdate,
        'xx'=>$afn,
        'from'=>$from,
        'to'=>$to
    ]);

    // Generate PDF using FacadePdf
    $pdf = FacadePdf::setOptions(['dpi' => 150, 'defaultFont' => 'dejavu serif'])->loadHtml($pdfView);

    // Save the PDF to a temporary file
    $pdfFile = tempnam(sys_get_temp_dir(), 'invoice');
    $pdf->save($pdfFile);

    // Send headers to instruct the browser to open the PDF in a new tab
    return response()->file($pdfFile, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="invoiceu.pdf"',
    ]);

  }
  return redirect('/login');

}

}
