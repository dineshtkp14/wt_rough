<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\invoice;


class Invoicecontroller extends Controller
{

    public function index()
    {
        
         $cus=invoice::orderBy('id','DESC')->get();
       

         return view('itemssales.list',['all'=>$cus]);

       
    }

    public function create()
    {
        $breadcrumb= [
            'subtitle'=>'Create',
            'title'=>'Invoice',
            'link'=>'Invoice'
        ];

        return view('itemssales.create',['breadcrumb'=>$breadcrumb]);
    }

    public function store()
    {
      

        return view('itemssales.create');
    }



}
