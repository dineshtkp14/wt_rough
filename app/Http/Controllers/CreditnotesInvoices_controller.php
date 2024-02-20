<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\CreditnotesInvoice;
use App\Models\BackupCreditnotesInvoice;


class CreditnotesInvoices_controller extends Controller
{
    public function index()
    {
        if(Auth::check()){

            $breadcrumb= [
                'subtitle'=>'View',
                'title'=>'View Credit Notes Invoices',
                'link'=>'View Credit Notes Invoices'
            ];
       
         $alldata=CreditnotesInvoice::orderBy('id','DESC')->get();
       

         return view('creditnotesinvoice.list',['all'=>$alldata,'breadcrumb'=>$breadcrumb]);

       
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

        return view('creditnote.create',['breadcrumb'=>$breadcrumb]);
    }
    return redirect('/login');
}

    public function store()
    {
        if(Auth::check()){

        return view('creditnote.create');
    }

    return redirect('/login');
 }


 public function showonlysalesperday()
    {
        if (Auth::check()) {
            $breadcrumb = [
                'subtitle' => 'Per Day',
                'title' => 'Sales Per Day',
                'link' => 'Sales Per Day'
            ];
    
            $salesPerDay = CreditnotesInvoice::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(20); 
    
            return view('creditnotesinvoice.perday', ['salesPerDay' => $salesPerDay, 'breadcrumb' => $breadcrumb]);
        }
    
        return redirect('/login');
    }






    public function edit($id)

{
    if(Auth::check()){
    $breadcrumb= [
        'subtitle'=>'Edit',
        'title'=>'Edit Invoice Details',
        'link'=>'Edit Invoice Details'
    ];

    $invoices=CreditnotesInvoice::findOrfail($id);

    return view('creditnotesinvoice.edit',['invoice'=>$invoices,'breadcrumb'=>$breadcrumb]);   
    
    return redirect('/login');
}
}


public function returndeletedcninvoice()
{
    if(Auth::check()){

        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'View Deleted Invoices',
            'link'=>'View Deleted Invoices'
        ];
   
     $alldata=BackupCreditnotesInvoice::orderBy('id','DESC')->get();
   

     return view('creditnote.viewdeletedcreditnotesinvoice',['all'=>$alldata,'breadcrumb'=>$breadcrumb]);

   
}

return redirect('/login');
}



public function update($id, Request $req)
{
    if (Auth::check()) {
        $validator = Validator::make($req->all(), [
            'subtotal' => 'required',
            'discount' => 'required',
            'total' => 'required',
        ]);

        if ($validator->passes()) {
            $invoice = CreditnotesInvoice::find($id);
            $invoice->subtotal = $req->subtotal;
            $invoice->discount = $req->discount;
            $invoice->total = $req->total;
            $invoice->notes = $req->notes;
            $invoice->added_by = session('user_email');

            $invoice->save();

            return redirect()->route('cninvoice.index')->with('success', 'Updated Successfully!');
        } else {
            return redirect()->route('cninvoice.edit', $id)->withErrors($validator)->withInput();
        }
    }

    return redirect('/login');
}

}
