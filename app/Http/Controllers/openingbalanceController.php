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
        // Add your logic for displaying the opening balances index page
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
            'particulars' => 'required',
           
            'amount' => 'required',
           
        ]);

        if ($validator->passes()) {
        
            $openingBalance = new customerledgerdetails();
            $openingBalance->date = $req->date;
            $openingBalance->customerid = $req->customerid;
            $openingBalance->particulars = $req->particulars;
            $openingBalance->invoicetype = "credit";
            $openingBalance->voucher_type = "old amount";
            $openingBalance->debit = $req->amount;
            $openingBalance->notes = $req->notes;
            $openingBalance->added_by = session('user_email');

            $openingBalance->save();

            return redirect()->route('openingbalances.create')->with('success', ' Added Successfully!');
        } else {
            return redirect()->route('openingbalances.create')->withErrors($validator)->withInput();
        }
    }
    return redirect('/login');
}

    public function edit()
    {
        // Add your logic for editing an opening balance
    }

    public function destroy()
    {
        // Add your logic for deleting an opening balance
    }

}