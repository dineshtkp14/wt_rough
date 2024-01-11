<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Invoicecontroller extends Controller
{

    public function index()
    {
        if(Auth::check()){

            $breadcrumb= [
                'subtitle'=>'View',
                'title'=>'View Invoices',
                'link'=>'View Invoices'
            ];
       
         $alldata=invoice::orderBy('id','DESC')->get();
       

         return view('invoice.list',['all'=>$alldata,'breadcrumb'=>$breadcrumb]);

       
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

        return view('itemssales.create',['breadcrumb'=>$breadcrumb]);
    }
    return redirect('/login');
}

    public function store()
    {
        if(Auth::check()){

        return view('itemssales.create');
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
    
            $salesPerDay = invoice::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(20); 
    
            return view('invoice.perday', ['salesPerDay' => $salesPerDay, 'breadcrumb' => $breadcrumb]);
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

    $invoices=invoice::findOrfail($id);

    return view('invoice.edit',['invoice'=>$invoices,'breadcrumb'=>$breadcrumb]);   
    
    return redirect('/login');
}
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
            $invoice = Invoice::find($id);
            $invoice->subtotal = $req->subtotal;
            $invoice->discount = $req->discount;
            $invoice->total = $req->total;
            $invoice->notes = $req->notes;
            $invoice->added_by = session('user_email');

            $invoice->save();

            return redirect()->route('invoice.index')->with('success', 'Updated Successfully!');
        } else {
            return redirect()->route('invoice.edit', $id)->withErrors($validator)->withInput();
        }
    }

    return redirect('/login');
}

}
