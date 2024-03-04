<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminstockController extends Controller
{
  public function index()
  {
    
        $breadcrumb = [
            'subtitle' => 'AdminStock',
            'title' => 'View Admin Stocks',
            'link' => 'View Admin Stocks'
        ];

      


        return view('adminstock.adminstock', [ 'breadcrumb' => $breadcrumb]);


    return redirect('/login');
  }
}
