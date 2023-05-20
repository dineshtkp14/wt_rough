<?php

namespace App\Http\Controllers;

use App\Models\company;
use Illuminate\Http\Request;

class companyNameSearchAPI extends Controller
{
    public function index(Request $req)
    {

        $companies = company::where('name', 'LIKE', '%'.$req->name.'%')->orWhere('email', 'LIKE', '%'.$req->name.'%')->orWhere('phoneno', 'LIKE', '%'.$req->name.'%')->get();
        return json_encode($companies);
      



    }
}
