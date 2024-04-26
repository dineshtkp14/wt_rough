<?php

namespace App\Http\Controllers;



use App\Models\customerledgerdetails;
use Illuminate\Support\Facades\Validator;


use Illuminate\Http\Request;
use App\Models\CustomerLedgerDetail; // Fixed the model name

class BankDeposit_CounterCheckController extends Controller
{
    public function showBankdeposit_UpdateForm()
    {
        $breadcrumb = [
            'subtitle' => 'Check Amount Bank Deposit',
            'title' => 'check Amout Bank Desposit',
            'link' => 'Check Amount Bank Deposit'
        ];
        return view('BankDeposit_counterCheck.bankdeposit', ['breadcrumb' => $breadcrumb]);
    }

    public function BankDeposit_UpdateForm(Request $request)
{
    // Validate the request data
    $request->validate([
        'date' => 'required|date',
    ]);

    // Check if records exist for the provided date
    $count = customerledgerdetails::where(function ($query) {
        $query->where('invoicetype', 'cash')
              ->orWhere('invoicetype', 'payment');
    })
    ->where('date', $request->input('date'))
    ->count();

    

    if ($count === 0 || $count === null) {
        // No records found for the provided date and invoicetype conditions
        return redirect()->back()->with('error', 'No records found for the provided date and invoice type.');
    }
    

    // Update records
    customerledgerdetails::where(function ($query) {
        $query->where('invoicetype', 'cash')
              ->orWhere('invoicetype', 'payment');
    })
    ->where('date', $request->input('date'))
    ->update(['bank_deposit' => 'yes']);

    // Redirect back with success message
    return redirect()->back()->with('bank_success', 'Bank deposit updated successfully.');
}


public function showCounterDeposit_UpdateForm()
    {
        $breadcrumb = [
            'subtitle' => 'Check Amount Counter Deposit',
            'title' => 'check Amout Counter Desposit',
            'link' => 'Check Amount Counter Deposit'
        ];
        return view('BankDeposit_counterCheck.Countercheck', ['breadcrumb' => $breadcrumb]);
    }

    public function CounterDeposit_UpdateForm(Request $request)
    {


        // Validate the request data
      // Validate the request data
    $request->validate([
        'date' => 'required|date',
    ]);

    // Check if records exist for the provided date
    $count = customerledgerdetails::where(function ($query) {
        $query->where('invoicetype', 'cash')
              ->orWhere('invoicetype', 'payment');
    })
    ->where('date', $request->input('date'))
    ->count();

    if ($count === 0 || $count === null) {
        // No records found for the provided date and invoicetype conditions
        return redirect()->back()->with('error', 'No records found for the provided date and invoice type.');
    }
    

    // Update records
    customerledgerdetails::where(function ($query) {
        $query->where('invoicetype', 'cash')
              ->orWhere('invoicetype', 'payment');
    })
    ->where('date', $request->input('date'))
    ->update(['counter_deposit' => 'yes']);
        
        
        // Redirect back with success message
        return redirect()->back()->with('counter_success', 'Counter Checked successfully.');
    }
    
}    

