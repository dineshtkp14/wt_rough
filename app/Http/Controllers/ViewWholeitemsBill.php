<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Validator;

class ViewWholeitemsBill extends Controller
{



    public function returnWholebillitems(Request $req)
    {
        $breadcrumb = [
            'subtitle' => 'Total Sales',
            'title' => 'Calculate Total Sales',
            'link' => 'View Total Sales'
        ];

        $validator = Validator::make($req->all(), [
            'billNo' => 'required|numeric', // Assuming billNo is numeric
            'companyName' => 'required|string', // Assuming companyName is a string
        ]);

        // Get the bill number and company name from the request
        $billNo = $req->billno;
        $companyName = $req->companyid;

        if($validator->passes()){

                        
            // Check if items exist for the provided bill number and company name
            $items = Item::where('billno', 12) ->where('did', 1) ->get();
           
           // Items found, return them to the view for display
          return view('viewwholeitembill.list', ['all' => $items, 'breadcrumb' => $breadcrumb]);




        }

        

      


      
        
    }

    // public function showwholebilllistpage()
    // {
    //     $breadcrumb = [
    //         'subtitle' => 'View',
    //         'title' => 'View Invoices',
    //         'link' => 'View Invoices'
    //     ];

    //     return view('viewwholeitembill.list', ['breadcrumb' => $breadcrumb]);
    // }
}
