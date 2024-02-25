<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\item;


class ViewwholeitembillController extends Controller
{


    public function returnWholebillitems(Request $req)
    {
        $breadcrumb = [
            'subtitle' => 'Total Sales',
            'title' => 'Calculate Total Sales',
            'link' => 'View Total Sales'
        ];

       
                        
            // Check if items exist for the provided bill number and company name
            $items = item::where('billno',$req->billno ) ->where('did', $req->companyid) ->get();
           
           // Items found, return them to the view for display
          return view('viewwholeitembill.list', ['all' => $items, 'breadcrumb' => $breadcrumb]);
    }      
    }

