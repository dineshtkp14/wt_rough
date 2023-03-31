<?php

namespace App\Http\Controllers;

use App\Models\item;
use Illuminate\Http\Request;

class ItemsSearchAPI extends Controller
{
    public function index(Request $req)
    {

        $items = item::where('itemsname', 'LIKE', '%'.$req->name.'%')->get();
        return json_encode($items);
      



    }

}
