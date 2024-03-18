<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackcompanyledgerController extends Controller
{
    public function index()
    {
       
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'Track COMPANY LEDGER/COMPANY PAYMENT/COMPANY BILL ENTRY  ',
            'link'=>'TRACK COMPANY LEDGER'
        ];
   

     return view('trackcompanyledger.list',['breadcrumb'=>$breadcrumb]);   

}
}
