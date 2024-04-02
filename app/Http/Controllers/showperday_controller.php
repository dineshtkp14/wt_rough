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
                ->where(function ($query) {
                    $query->where('particulars', '!=', 'salesreturn'); // Include rows where particulars is not 'salesreturn'
                })
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
                    
      
            return view('showperday.perday', [
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
                ->where(function ($query) {
                    $query->where('particulars', '!=', 'salesreturn'); // Include rows where particulars is not 'salesreturn'
                })
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100);


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


                         // Calculate the total of CREDITNOTES for today's date
                         
                         $today = Carbon::today()->toDateString(); // Get today's date
                         $totalCashToday = $salesPerDayCash->where('date', $today)->sum('total');
                         $totalPaymentToday = $payment->where('date', $today)->sum('total');
                         $totalCreditNotesTodaySUM = $salesPerDaycr->where('date', $today)->sum('total');
 

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

            // Sort the array by date in descending order
            usort($totalSalesAndPayments, function ($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });

            // Paginate the sorted array
            $perPage = 10; // Adjust the number as per your requirement
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = array_slice($totalSalesAndPayments, ($currentPage - 1) * $perPage, $perPage);
            $totalCount = count($totalSalesAndPayments);
            $totalSalesAndPaymentsPaginated = new LengthAwarePaginator($currentItems, $totalCount, $perPage, $currentPage);

            return view('showperday.showperdayinonetable', [
                'totalSalesAndPayments' => $totalSalesAndPaymentsPaginated,
                'totalCashAndPaymentToday' => $totalCashAndPaymentToday,
                'salesPerDayCredit' => $salesPerDayCredit,
                'salesPerDayCash' => $salesPerDayCash,
                'salesPerDay' => $salesPerDay,
                'salesPerDaycrnotes' => $salesPerDaycr,
                'payment' => $payment,
                'totalCreditNotesTodaySUM' => $totalCreditNotesTodaySUM,
                'forsalesreturn' => $forsalesreturn,
                'breadcrumb' => $breadcrumb
            ]);
        }

        return redirect('/login');
    }
}





