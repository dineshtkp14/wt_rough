<?php

namespace App\Http\Controllers;

use App\Models\item;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $breadcrumb= [
            'subtitle'=>'Stock',
            'title'=>'View Stocks',
            'link'=>'View Stocks'
        ];
        
        
         $data=item::orderBy('id','DESC')->get();
         return view('stock.stock',['all'=>$data,'breadcrumb'=>$breadcrumb]);

       
    }
}
