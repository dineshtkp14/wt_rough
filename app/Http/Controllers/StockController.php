<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Models\item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class StockController extends Controller
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
   
    public $searchTerm = "";

    public function index()
{
    if (Auth::check()) {
        $breadcrumb = [
            'subtitle' => 'Stock',
            'title' => 'View Stocks',
            'link' => 'View Stocks'
        ];

        $iteamdata = Item::orderBy('id', 'DESC')->get();

        // // Use the map function to modify the distributorname
        // $iteamdata = $iteamdata->map(function ($data) {
        //    // if ($data->distributorname) {
        //         //$dis_name = company::where('id', $data->distributorname)->select('name')->first();
        //        // $data->distributorname = $dis_name->name;
        //         // echo $data->distributorname;
        //     }
        //     return $data; // Return the modified data
        // });

        return view('stock.stock', ['all' => $iteamdata, 'breadcrumb' => $breadcrumb]);
    }

    return redirect('/login');
}

}
