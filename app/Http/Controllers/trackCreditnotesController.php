<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class trackCreditnotesController extends Controller
{
   
    public function index()
    {
       
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'Track Credit Notes/Sales Return  ',
            'link'=>'Track Credit Notes/Sales Return'
        ];
   

     return view('trackcreditnotes.list',['breadcrumb'=>$breadcrumb]);   

}
}
