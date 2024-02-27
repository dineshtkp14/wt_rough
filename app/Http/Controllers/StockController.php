<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Models\item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StockUpdatePriceRequest;


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
//TRUE
        $iteamdata = item::orderBy('id', 'ASC')->get();

        // $iteamdata = Item::where('check_remove_ofs', '=', 0)
               
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






public function update(Request $request, $id)
{
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'wp' => 'required|numeric', // Example validation rules, adjust as needed
        'competetiveretail' => 'required|numeric',
        'competetivewholesale' => 'required|numeric',
    ]);

    // If validation fails, return the validation errors to the view
    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput()->with('error', 'Error !!! Price Not Update !!');
    }

    // If validation passes, continue with your logic to update the prices
    // You can access validated data using $request->input('fieldname')

    // Example:
    $product = item::findOrFail($id);
    $product->wholesale_price = $request->input('wp');
    $product->com_Retail_price = $request->input('competetiveretail');
    $product->com_wholesale_price = $request->input('competetivewholesale');
    $product->save();

    // Redirect the user or do whatever is appropriate for your application
    return redirect()->route('stocks.index')->with('success', 'Price Updated Sucessfully  !!!');
}

}
