<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\invoice;
use App\Models\CreditnotesInvoice;
use App\Models\customerledgerdetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;



class showperday_controller extends Controller
{
    public function showonlysalesperday()
    {

        if (Auth::check()) {
            $breadcrumb = [
                'subtitle' => 'Per Day one table',
                'title' => 'Sales Per Day one table',
                'link' => 'Sales Per day one table'
            ];

            $salesPerDaycr = CreditnotesInvoice::select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->get();

            $salesPerDayCash = invoice::select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
                ->where('inv_type', 'cash')
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->get();

            $payment = customerledgerdetails::select(DB::raw('DATE(date) as date'), DB::raw('SUM(credit) as total'))
                ->where('invoicetype', 'payment')
                ->where(function ($query) {
                    $query->where('particulars', '!=', 'salesreturn');
                })
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->get();

            $forsalesreturn = customerledgerdetails::select(DB::raw('DATE(date) as date'), DB::raw('SUM(credit) as total'))
                ->where('invoicetype', 'payment')
                ->where(function ($query) {
                    $query->where('particulars', 'salesreturn');
                })
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100);

            $today = Carbon::today()->toDateString();
            $totalCashToday = $salesPerDayCash->where('date', $today)->sum('total');
            $totalPaymentToday = $payment->where('date', $today)->sum('total');
            $totalCashAndPaymentToday = $totalCashToday + $totalPaymentToday;
            $totalCreditNotesTodaySUM = $salesPerDaycr->where('date', $today)->sum('total');

            $dates = $salesPerDayCash->pluck('date')->merge($payment->pluck('date'))->unique();
            $totalSalesAndPayments = [];

            foreach ($dates as $date) {
                $salesTotal = $salesPerDayCash->where('date', $date)->sum('total');
                $paymentTotal = $payment->where('date', $date)->sum('total');
                $creditNotesTotal = $salesPerDaycr->where('date', $date)->sum('total');
                $bankDeposit = CustomerLedgerDetails::whereDate('date', $date)->value('bank_deposit');
                $CounterDeposit = CustomerLedgerDetails::whereDate('date', $date)->value('counter_deposit');

                $totalSalesAndPayments[] = [
                    'date' => $date,
                    'total' => $salesTotal + $paymentTotal,
                    'bank_deposit' => $bankDeposit,
                    'counter_deposit' => $CounterDeposit,
                    'credit_notes_total' => $creditNotesTotal,
                ];
            }

            usort($totalSalesAndPayments, function ($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });

            $perPage = 100;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = array_slice($totalSalesAndPayments, ($currentPage - 1) * $perPage, $perPage);
            $totalCount = count($totalSalesAndPayments);
            $totalSalesAndPaymentsPaginated = new LengthAwarePaginator($currentItems, $totalCount, $perPage, $currentPage);

            return view('showperday.showperdayinonetable', [
                'totalSalesAndPayments' => $totalSalesAndPaymentsPaginated,
                'totalCashAndPaymentToday' => $totalCashAndPaymentToday,
                'salesPerDayCash' => $salesPerDayCash,
                'payment' => $payment,
                'totalCreditNotesTodaySUM' => $totalCreditNotesTodaySUM,
                'forsalesreturn' => $forsalesreturn,
                'breadcrumb' => $breadcrumb
            ]);
        }
    
        return redirect('/login');
    }


    //onetable

    public function showonlysalesperdayinone_table()
        {
            

                $breadcrumb = [
                    'subtitle' => 'Per Day one table',
                    'title' => 'Sales Per Day one table',
                    'link' => 'Sales Per day one table'
                ];
        

                    
                    $salesPerDaycr = CreditnotesInvoice::select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
                    ->groupBy('date')
                    ->orderBy('date', 'DESC')
                    ->get();


                    $salesPerDayCash = invoice::select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
                    ->where('inv_type', 'cash')
                    ->groupBy('date')
                    ->orderBy('date', 'DESC')
                    ->get();


                    $payment = customerledgerdetails::select(DB::raw('DATE(date) as date'), DB::raw('SUM(credit) as total'))
                    ->where('invoicetype', 'payment')
                    ->where(function ($query) {
                        $query->where('particulars', '!=', 'salesreturn'); // Include rows where particulars is not 'salesreturn'
                    })
                    ->groupBy('date')
                    ->orderBy('date', 'DESC')
                    ->get();


                    //thisisneeded
                    $forsalesreturn = customerledgerdetails::select(DB::raw('DATE(date) as date'), DB::raw('SUM(credit) as total'))
                    ->where('invoicetype', 'payment')
                    ->where(function ($query) {
                        $query->where('particulars', 'salesreturn'); // Include rows where particulars is 'salesreturn'
                    })
                    ->groupBy('date')
                    ->orderBy('date', 'DESC')
                    ->paginate(100);

                    // Calculate the total of cash and payment for today's date
                    $today = Carbon::today()->toDateString(); // Get today's date
                    $totalCashToday = $salesPerDayCash->where('date', $today)->sum('total');
                    $totalPaymentToday = $payment->where('date', $today)->sum('total');
                    $totalCashAndPaymentToday = $totalCashToday + $totalPaymentToday;

                    $totalCreditNotesTodaySUM = $salesPerDaycr->where('date', $today)->sum('total');
                                            
                        //forcashand payemntable
                    $dates = $salesPerDayCash->pluck('date')->merge($payment->pluck('date'))->unique();

                        
                // Initialize an array to store total sales and payments for each date
                $totalSalesAndPayments = [];

                // Loop through each date and calculate the sum for each date
                foreach ($dates as $date) {

                    $salesTotal = $salesPerDayCash->where('date', $date)->sum('total');
                    $paymentTotal = $payment->where('date', $date)->sum('total');
                    $creditNotesTotal = $salesPerDaycr->where('date', $date)->sum('total'); // Calculate total credit notes for the date
                    $bankDeposit = CustomerLedgerDetails::whereDate('date', $date)->value('bank_deposit');
                    $CounterDeposit = CustomerLedgerDetails::whereDate('date', $date)->value('counter_deposit');

                    $totalSalesAndPayments[] = [
                        'date' => $date,
                        'total' => $salesTotal + $paymentTotal,
                        'bank_deposit' => $bankDeposit,
                        'counter_deposit' => $CounterDeposit,
                        'credit_notes_total' => $creditNotesTotal, // Include total credit notes for the date

                    ];
                }

                // Sort the array by date in descending order
                usort($totalSalesAndPayments, function ($a, $b) {
                    return strtotime($b['date']) - strtotime($a['date']);
                });

                // Paginate the sorted array
                $perPage = 100; // Adjust the number as per your requirement
                $currentPage = LengthAwarePaginator::resolveCurrentPage();

                $currentItems = array_slice($totalSalesAndPayments, ($currentPage - 1) * $perPage, $perPage);
                $totalCount = count($totalSalesAndPayments);
                $totalSalesAndPaymentsPaginated = new LengthAwarePaginator($currentItems, $totalCount, $perPage, $currentPage);

                return view('showperday.showperdayinonetable', [
                    'totalSalesAndPayments' => $totalSalesAndPaymentsPaginated,
                    'totalCashAndPaymentToday' => $totalCashAndPaymentToday,
                    'salesPerDayCash' => $salesPerDayCash,
                    'payment' => $payment,
                    'totalCreditNotesTodaySUM' => $totalCreditNotesTodaySUM,
                    'forsalesreturn' => $forsalesreturn,
                    'breadcrumb' => $breadcrumb
                ]);
            }

    public function salesDetailsPerDay(Request $req)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $breadcrumb = [
            'subtitle' => 'Per Day',
            'title' => 'Sales Details Per Day',
            'link' => 'Sales Details Per Day'
        ];

        $from = $req->date1;
        $to = $req->date2;

        $invoiceQuery = invoice::query();
        $creditNoteQuery = CreditnotesInvoice::query();

        if (!empty($from) && !empty($to)) {
            $invoiceQuery->whereBetween('inv_date', [$from, $to]);
            $creditNoteQuery->whereBetween('inv_date', [$from, $to]);
        }

        $cashSales = (clone $invoiceQuery)
            ->select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
            ->where('inv_type', 'cash')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $creditSales = (clone $invoiceQuery)
            ->select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
            ->where('inv_type', 'credit')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $creditNotes = (clone $creditNoteQuery)
            ->select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $dates = $cashSales->keys()
            ->merge($creditSales->keys())
            ->merge($creditNotes->keys())
            ->unique()
            ->sortDesc()
            ->values();

        $rows = $dates->map(function ($date) use ($cashSales, $creditSales, $creditNotes) {
            $cash = (float) optional($cashSales->get($date))->total;
            $credit = (float) optional($creditSales->get($date))->total;
            $notes = (float) optional($creditNotes->get($date))->total;

            return [
                'date' => $date,
                'cash_sales' => $cash,
                'credit_sales' => $credit,
                'credit_notes' => $notes,
                'total_sales' => $cash + $credit,
                'net_after_credit_notes' => ($cash + $credit) - $notes,
            ];
        });

        $perPage = 100;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $rows->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $salesRows = new LengthAwarePaginator($currentItems, $rows->count(), $perPage, $currentPage, [
            'path' => url('/sales-details-per-day'),
            'query' => $req->query(),
        ]);

        return view('showperday.sales-details-per-day', [
            'breadcrumb' => $breadcrumb,
            'salesRows' => $salesRows,
            'from' => $from,
            'to' => $to,
            'totalCashSales' => $rows->sum('cash_sales'),
            'totalCreditSales' => $rows->sum('credit_sales'),
            'totalCreditNotes' => $rows->sum('credit_notes'),
            'grandTotalSales' => $rows->sum('total_sales'),
            'grandNetSales' => $rows->sum('net_after_credit_notes'),
        ]);
    }

    
}

