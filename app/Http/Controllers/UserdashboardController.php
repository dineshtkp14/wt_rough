<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserdashboardController extends Controller
{
   
    public function index()
    {
        

     return view('dashboard.userdashboard');    
    }
    public function invoice()
    {
        

     return view('userpages.invoicepages');    
    }

    public function bankdash()
    {
        

     return view('userpages.bankpages');    
    }

    public function itemdash()
    {
        

     return view('userpages.itempages');    
    }

    public function daybookdash()
    {
        

     return view('userpages.daybookpages');    
    }

   

    public function purchaseorderdash()
    {
        

     return view('userpages.purchasepages');    
    }

    public function customerdash()
    {
        

     return view('userpages.customerpages');    
    }

    public function companydash()
    {
        

     return view('userpages.companypages');    
    }
}