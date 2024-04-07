<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\trackcustomerledger; // Add this line


class TrackCustomerLedgerPayment extends Controller
{
    public function index()
    {
       
        $breadcrumb= [
            'subtitle'=>'View',
            'title'=>'Track customer ledger payment ',
            'link'=>'Track  customer ledger payment ',
        ];



     return view('trackcustomerledger.list',['breadcrumb'=>$breadcrumb ]);   
}
}