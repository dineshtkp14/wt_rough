<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackcustomerinfoController extends Controller
{
    public function index()
    {
       
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'Track Customer Infos ',
            'link'=>'Track  Customer Infos ',
        ];

     return view('trackcustomerinfo.list',['breadcrumb'=>$breadcrumb ]);   
}
}