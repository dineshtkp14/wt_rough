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
    
            $salesPerDay = invoice::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100); 
    


                $salesPerDaycr = CreditnotesInvoice::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100); 


                $salesPerDayCash = invoice::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
                ->where('inv_type', 'cash')
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100);



                $salesPerDayCredit = invoice::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
                ->where('inv_type', 'credit')
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(100);
    
      
            return view('invoice.perday', ['salesPerDayCredit' => $salesPerDayCredit,'salesPerDayCash' => $salesPerDayCash,'salesPerDay' => $salesPerDay,'salesPerDaycrnotes' => $salesPerDaycr, 'breadcrumb' => $breadcrumb]);
        }
    
        return redirect('/login');
    }

}
