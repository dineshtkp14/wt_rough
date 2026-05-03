<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\invoice;
use App\Models\customerledgerdetails;
use App\Models\customerinfo;
use App\Support\NepaliDate;

class UserdashboardController extends Controller
{
   
    public function index()
    {
        $today = now()->toDateString();

        // Get today's invoices count and total
        $todayInvoices = invoice::whereDate('inv_date', $today)->count();
        $todayInvoicesTotal = invoice::whereDate('inv_date', $today)->sum('total') ?? 0;

        // Get today's payments count and total
        $todayPayments = customerledgerdetails::where('invoicetype', 'payment')
            ->whereDate('date', $today)
            ->count();
        $todayPaymentsTotal = customerledgerdetails::where('invoicetype', 'payment')
            ->whereDate('date', $today)
            ->sum('credit') ?? 0;

        // Get recent invoices for display
        $recentInvoicesRaw = invoice::join('customerinfos', 'invoices.customerid', '=', 'customerinfos.id')
            ->select('invoices.id', 'invoices.total as amount', 'invoices.inv_type as type', 'invoices.inv_date as date', 'customerinfos.name as customer')
            ->whereDate('invoices.inv_date', $today)
            ->orderByDesc('invoices.inv_date')
            ->orderByDesc('invoices.id')
            ->limit(10)
            ->get();

        $recentInvoices = [];
        foreach ($recentInvoicesRaw as $inv) {
            $isPaid = ($inv->type === 'cash') || customerledgerdetails::where('invoiceid', $inv->id)->where('credit', '>', 0)->exists();
            $recentInvoices[] = [
                'invoice_id' => $inv->id,
                'id'       => 'INV-' . $inv->id,
                'customer' => $inv->customer,
                'amount'   => (float) $inv->amount,
                'type'     => ucfirst($inv->type),
                'date'     => NepaliDate::adToBsString($inv->date, 'en'),
                'status'   => $isPaid ? 'paid' : 'pending',
            ];
        }

        // Get recent payments for display
        $recentPaymentsRaw = customerledgerdetails::join('customerinfos', 'customerledgerdetails.customerid', '=', 'customerinfos.id')
            ->select('customerinfos.name as customer', 'customerledgerdetails.credit as amount', 'customerledgerdetails.date', 'customerledgerdetails.id', 'customerledgerdetails.bank_deposit', 'customerledgerdetails.counter_deposit', 'customerledgerdetails.particulars', 'customerledgerdetails.voucher_type')
            ->where('customerledgerdetails.invoicetype', 'payment')
            ->whereDate('customerledgerdetails.date', $today)
            ->orderByDesc('customerledgerdetails.date')
            ->orderByDesc('customerledgerdetails.id')
            ->limit(10)
            ->get();

        $recentPayments = [];
        foreach ($recentPaymentsRaw as $pay) {
            $mode = trim($pay->voucher_type ?? '');
            if (empty($mode)) {
                $mode = trim($pay->particulars ?? '');
            }
            if (empty($mode)) {
                $mode = 'Cash';
            }

            $recentPayments[] = [
                'payment_id' => $pay->id,
                'customer' => $pay->customer,
                'amount'   => (float) $pay->amount,
                'mode'     => $mode,
                'date'     => NepaliDate::adToBsString($pay->date, 'en'),
                'receipt'  => 'RCP-' . $pay->id,
            ];
        }

        return view('dashboard.userdashboard', compact(
            'todayInvoices', 
            'todayInvoicesTotal', 
            'todayPayments', 
            'todayPaymentsTotal',
            'recentInvoices',
            'recentPayments'
        ));    
    }
    public function invoicedash()
    {
        

     return view('userpages.invoicepages');    
    }

    public function bankdash()
    {
        

     return view('userpages.bankpages');    
    }

    public function itemdash()
    {
        

     return view('userpages.itempages');    
    }

    public function daybookdash()
    {
        

     return view('userpages.daybookpages');    
    }

   

    public function purchaseorderdash()
    {
        

     return view('userpages.purchasepages');    
    }

    public function customerdash()
    {
        

     return view('userpages.customerpages');    
    }

    public function companydash()
    {
        

     return view('userpages.companypages');    
    }

    public function cndash()
    {
        

     return view('userpages.creditnotespages');    
    }
}