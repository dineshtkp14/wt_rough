<?php

namespace App\Http\Controllers;

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
    }
}
