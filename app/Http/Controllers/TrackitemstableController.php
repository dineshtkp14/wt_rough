<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackitemstableController extends Controller
{
    public function index()
    {
       
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'track Item table',
            'link'=>'track Item Table'
        ];
   

     return view('items.trackitemstable',['breadcrumb'=>$breadcrumb]);   

}
}