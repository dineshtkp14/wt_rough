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



class showperday_controller extends Controller
{
    public function showonlysalesperday()
    {

        if (Auth::check()) {
            $breadcrumb = [
                'subtitle' => 'Per Day',
                'title' => 'Sales Per Day',
                'link' => 'Sales Per Day'
            ];
    
            $salesPerDay = invoice::select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100); 

               
                
                $salesPerDaycr = CreditnotesInvoice::select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100); 


                $salesPerDayCash = invoice::select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
                ->where('inv_type', 'cash')
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100);


                $salesPerDayCredit = invoice::select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
                ->where('inv_type', 'credit')
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100);


                $payment = customerledgerdetails::select(DB::raw('DATE(date) as date'), DB::raw('SUM(credit) as total'))
                ->where('invoicetype', 'payment')
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100);
    
 
      
            return view('showperday.perday', ['salesPerDayCredit' => $salesPerDayCredit,'salesPerDayCash' => $salesPerDayCash,'salesPerDay' => $salesPerDay,'salesPerDaycrnotes' => $salesPerDaycr, 'payment' => $payment,'breadcrumb' => $breadcrumb]);
        }
    
        return redirect('/login');
    }



    public function showonlysalesperdayinone_table()
    {

        if (Auth::check()) {
            $breadcrumb = [
                'subtitle' => 'Per Day one table',
                'title' => 'Sales Per Day one table',
                'link' => 'Sales Per day one table'
            ];
    
            $salesPerDay = invoice::select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100); 

               
                
                $salesPerDaycr = CreditnotesInvoice::select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100); 


                $salesPerDayCash = invoice::select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
                ->where('inv_type', 'cash')
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100);


                $salesPerDayCredit = invoice::select(DB::raw('DATE(inv_date) as date'), DB::raw('SUM(total) as total'))
                ->where('inv_type', 'credit')
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100);


                $payment = customerledgerdetails::select(DB::raw('DATE(date) as date'), DB::raw('SUM(credit) as total'))
                ->where('invoicetype', 'payment')
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100);
  
                

                        // Calculate the total of cash and payment for today's date
                    $today = Carbon::today()->toDateString(); // Get today's date
                    $totalCashToday = $salesPerDayCash->where('date', $today)->sum('total');
                    $totalPaymentToday = $payment->where('date', $today)->sum('total');
                    $totalCashAndPaymentToday = $totalCashToday + $totalPaymentToday;



                    //forcashand payemntable
                    $dates = $salesPerDayCash->pluck('date')->merge($payment->pluck('date'))->unique();

                    // Initialize an array to store total sales and payments for each date
                    $totalSalesAndPayments = [];
                
                    // Loop through each date and calculate the sum for each date
                    foreach ($dates as $date) {
                        $salesTotal = $salesPerDayCash->where('date', $date)->sum('total');
                        $paymentTotal = $payment->where('date', $date)->sum('total');

                        $bankDeposit = CustomerLedgerDetails::whereDate('date', $date)->value('bank_deposit');
                        $CounterDeposit = CustomerLedgerDetails::whereDate('date', $date)->value('counter_deposit');

                        $totalSalesAndPayments[] = [
                            'date' => $date,
                            'total' => $salesTotal + $paymentTotal,
                            'bank_deposit' => $bankDeposit,
                            'counter_deposit' => $CounterDeposit
                        ];
                    }
                

// dd($totalSalesAndPayments);

      
            return view('showperday.showperdayinonetable', [
               
            'totalSalesAndPayments' => $totalSalesAndPayments,
            'totalCashAndPaymentToday' => $totalCashAndPaymentToday
            ,'salesPerDayCredit' => $salesPerDayCredit,
            'salesPerDayCash' => $salesPerDayCash,
            'salesPerDay' => $salesPerDay,
            'salesPerDaycrnotes' => $salesPerDaycr,
             'payment' => $payment,
             'breadcrumb' => $breadcrumb]);
        }
    
        return redirect('/login');
    }




}
