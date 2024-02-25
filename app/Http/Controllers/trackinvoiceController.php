<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class trackinvoiceController extends Controller
{
    
    public function index()
    {
       
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'track invoices',
            'link'=>'track invoices'
        ];
   

     return view('trackinvoice.list',['breadcrumb'=>$breadcrumb]);   

}
    }

