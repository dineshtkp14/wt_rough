<?php

namespace App\Http\Controllers;

use App\Models\item;
use Illuminate\Http\Request;

class ItemsSearchAPI extends Controller
{
    public function index(Request $req)
    {

        //old data
        // $items = item::where('itemsname', 'LIKE', '%'.$req->name.'%')->get();
        // return json_encode($items);

        $items = item::where('itemsname', 'LIKE', '%'.$req->name.'%')->where('quantity', '>', 0) ->get();
       
                return json_encode($items);

      



    }

}
