<?php

namespace App\Http\Controllers;
use App\Models\invoice; // Add this line
use Illuminate\Support\Facades\DB; // Add this line
use Illuminate\Http\Request;

class TotalSalesController extends Controller
{
    public function index(Request $request)
    {
        $breadcrumb= [
            'subtitle'=>'TotalSales',
            'title'=>'Calculate TotalSales',
            'link'=>'View TotalSales'
        ];


      

// Retrieve start and end dates from the request
$startDate = $request->input('start_date');
$endDate = $request->input('end_date');

// Fetch results once
$results = Invoice::whereBetween('created_at', [$startDate, $endDate])->get();

// Calculate the total sales
$totalSales = $results->sum('total');

// Calculate the total discount
$totalDiscount = $results->sum('discount');

// Calculate the final result (total sales - total discount)
$finalResult = $totalSales - $totalDiscount;

// Pass the data to the view
return view('totalsales.index', compact('finalResult', 'startDate', 'endDate', 'breadcrumb'));

}
}