<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Invoicecontroller extends Controller
{

    public function index()
    {
        if(Auth::check()){

            $breadcrumb= [
                'subtitle'=>'View',
                'title'=>'View Invoices',
                'link'=>'View Invoices'
            ];
       
         $alldata=invoice::orderBy('id','DESC')->get();
       

         return view('invoice.list',['all'=>$alldata,'breadcrumb'=>$breadcrumb]);

       
    }

    return redirect('/login');
}
    public function create()
    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Create',
            'title'=>'Invoice',
            'link'=>'Invoice'
        ];

        return view('itemssales.create',['breadcrumb'=>$breadcrumb]);
    }
    return redirect('/login');
}

    public function store()
    {
        if(Auth::check()){

        return view('itemssales.create');
    }

    return redirect('/login');
 }


 public function showonlysalesperday()
    {
        if (Auth::check()) {
            $breadcrumb = [
                'subtitle' => 'Per Day',
                'title' => 'Sales Per Day',
                'link' => 'Sales Per Day'
            ];
    
            $salesPerDay = invoice::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->paginate(20); 
    
            return view('invoice.perday', ['salesPerDay' => $salesPerDay, 'breadcrumb' => $breadcrumb]);
        }
    
        return redirect('/login');
    }

}
