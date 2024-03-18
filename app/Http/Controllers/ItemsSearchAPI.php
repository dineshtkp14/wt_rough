<?php

namespace App\Http\Controllers;

use App\Models\item;
use Illuminate\Http\Request;

class ItemsSearchAPI extends Controller
{
    public function index(Request $req)
    {
        $items=null;
        //old data

        // $items = Item::where('itemsname', 'LIKE', '%'.$req->name.'%')
        // ->orWhere('id', 'LIKE', '%'.$req->name.'%')
        // ->where('quantity', '>', 0)
        // ->get();


//also you van check all and limited for itemsales and creditnotes
        $quantity_case = $req->input('quantity');

        if ($quantity_case == 'all') {
            $items = Item::where('itemsname', 'LIKE', '%'.$req->name.'%')
                        ->orWhere('id', 'LIKE', '%'.$req->name.'%')
                        ->get();
        } else {
            $items = Item::where(function($query) use ($req) {
                            $query->where('itemsname', 'LIKE', '%'.$req->name.'%')
                                  ->orWhere('id', 'LIKE', '%'.$req->name.'%');
                        })
                        ->where('quantity', '>', 0)
                        ->get();
        }
        
        
        return json_encode($items);
        
        
        

         
       
 


        
        
        

      



    }

}
