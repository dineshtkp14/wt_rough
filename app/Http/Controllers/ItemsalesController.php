<?php

namespace App\Http\Controllers;

use App\Models\itemsale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\salesitem;

class ItemsalesController extends Controller
{



    public function index()
    {

        $cus = salesitem::orderBy('id', 'DESC')->get();


        return view('itemssales.list', ['all' => $cus]);
    }
    public function create()
    {

        return view('itemssales.create');
    }


    public function store(Request $req)
    {
        $sales_arr = json_decode($req->sales_arr);
        $final_arr = json_decode($req->final_arr);

        // invoice insert

        // sales insert
        foreach ($req->values as $value) {
            $data = new itemsale();
            $data->quantity = $value->price;
            $data->save();
        }
    }
}
