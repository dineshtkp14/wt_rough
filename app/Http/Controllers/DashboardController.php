<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class DashboardController extends Controller
{
   public function index()
   {
   
    $breadcrumb= [
        'subtitle'=>'Dashboard',
        'title'=>' View Dashboard ',
        'link'=>'View Dashboard'
    ];

    return view('dashboard.dashboard',['breadcrumb'=>$breadcrumb]) ;
   }
}
