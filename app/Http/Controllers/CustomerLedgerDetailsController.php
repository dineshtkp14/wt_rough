<?php

namespace App\Http\Controllers;

use App\Models\customerledgerdetails;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\customerinfo;


class CustomerLedgerDetailsController extends Controller

{
    public function index()
    {
        dd("index");
         $cus=customerledgerdetails::orderBy('id','DESC')->get();
       

         return view('customerdetails.list',['all'=>$cus]);   
    }

    public function create()
    {
        dd("test");
        return view('customerdetails.create');
    }
}
