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
            'title' => 'View Stockss',
            'link' => 'View Stocks'
        ];

        $iteamdata = Item::orderBy('id', 'DESC')->get();

        // $iteamdata = item::where('check_remove_ofs', '=', 0)
               
        // ->orderBy('id', 'DESC')
        // ->select('*');

       

        return view('stock.stock', ['all' => $iteamdata, 'breadcrumb' => $breadcrumb]);
    }

    return redirect('/login');
}

public function updateofs(Request $request)
{
    $itemId = $request->input('item_id');

    // Update your database here
    Item::where('id', $itemId)->update(['check_remove_ofs' => 1]);

    // Redirect back or return a response as needed
    return redirect()->back()->with('success', 'Items Remove  successfully');
}



}
