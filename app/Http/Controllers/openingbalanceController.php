<?php

namespace App\Http\Controllers;

use App\Models\customerledgerdetails;
use App\Models\opeaningBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class openingbalanceController extends Controller
{
    public function index()
    {
        if(Auth::check()){
            $breadcrumb= [
                'subtitle'=>'View',
                'title'=>'View All opeingbalance',
                'link'=>'View All opeingbalance'
            ];
       
    
         return view('openingbalance.list',['breadcrumb'=>$breadcrumb]);   
    }

 }
  public function create()
    {
        if(Auth::check()){
        $breadcrumb = [
            'subtitle' => 'Add',
            'title' => 'Add Opening Balance',
            'link' => 'Add Opening Balance'
        ];

        return view('openingbalance.create', ['breadcrumb' => $breadcrumb]);
    }
    return redirect('/login');
 }
    public function store(Request $req)
    {
        if(Auth::check()){
        $validator = Validator::make($req->all(), [
            'date' => 'required',
            'customerid' => 'required',
           
           
            'amount' => 'required',
           
        ]);

        if ($validator->passes()) {
        
            $openingBalance = new customerledgerdetails();
            $openingBalance->date = $req->date;
            $openingBalance->customerid = $req->customerid;
            $openingBalance->particulars = "opening_balance";
            $openingBalance->invoicetype = "credit";
            $openingBalance->voucher_type = "old amount";
            $openingBalance->debit = $req->amount;
            $openingBalance->notes = $req->notes;
            $openingBalance->added_by = session('user_email');

            $openingBalance->save();
            session()->put('lastInsertedId', $openingBalance->id);


            return redirect()->route('openingbalances.index')->with('success', ' Added Successfully!');
        } else {
            return redirect()->route('openingbalances.create')->withErrors($validator)->withInput();
        }
    }
    return redirect('/login');
}
public function edit($id)
{
    if(Auth::check()){

        $breadcrumb = [
            'subtitle' => 'Edit',
            'title' => 'Edit Opening Balance',
            'link' => 'Edit Opening Balance'
        ];

        $openingBalance = customerledgerdetails::findOrFail($id);
        // You may add additional logic here if needed
        return view('openingbalance.edit', compact('openingBalance', 'breadcrumb'));
    }
    return redirect('/login');
}


public function update(Request $req, $id)
{
    if(Auth::check()){
        $validator = Validator::make($req->all(), [
            'date' => 'required',
            'customerid' => 'required',
            'amount' => 'required',
        ]);

        if ($validator->passes()) {
            $openingBalance = customerledgerdetails::findOrFail($id);
            $openingBalance->date = $req->date;
            $openingBalance->customerid = $req->customerid;
            $openingBalance->debit = $req->amount;
            $openingBalance->notes = $req->notes;
            $openingBalance->added_by = session('user_email');

            $openingBalance->save();

            return redirect()->route('openingbalances.index')->with('success', 'Opening balance updated successfully!');
        } else {
            return redirect()->route('openingbalances.edit', $id)->withErrors($validator)->withInput();
        }
    }
    return redirect('/login');
}

public function destroy($id)
{
    if(Auth::check()){
        $openingBalance = customerledgerdetails::findOrFail($id);
        $openingBalance->delete();
        return redirect()->route('openingbalances.index')->with('success', 'Opening balance deleted successfully!');
    }
    return redirect('/login');
}
}