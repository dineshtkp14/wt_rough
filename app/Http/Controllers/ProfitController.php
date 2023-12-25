<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\salesitem; // Add this line
use Illuminate\Support\Facades\DB; // Add this line

class ProfitController extends Controller
{
    public function index(Request $request)
    {
        $breadcrumb= [
            'subtitle'=>'Profit',
            'title'=>'Calculate Profit',
            'link'=>'View Profit'
        ];


       // Retrieve start and end dates from the request
$startDate = $request->input('start_date');
$endDate = $request->input('end_date');

// Your logic to fetch profits based on chosen dates
$profits = salesitem::join('items', 'salesitems.itemid', '=', 'items.id')
    ->whereBetween('salesitems.created_at', [$startDate, $endDate])
    ->sum(DB::raw('(salesitems.price - items.dlp) * salesitems.quantity'));

return view('profits.index', compact('profits', 'startDate', 'endDate','breadcrumb'));

    }
}
