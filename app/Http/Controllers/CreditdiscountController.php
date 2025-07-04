<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customerledgerdetails;

use Illuminate\Support\Facades\DB; //
class CreditdiscountController extends Controller
{
    public function CreditdueDiscount(Request $req)
    {
           
    $cl = new customerledgerdetails();
    $cl->customerid = $req->customerid;
    $cl->particulars = $req->particulars;
    $cl->voucher_type = $req->voucher_type;
    $cl->save();

    }
}
