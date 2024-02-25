<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\item;
use App\Models\company;



class ViewwholeitembillController extends Controller
{


    public function returnWholebillitems(Request $req)
    {
        $breadcrumb = [
            'subtitle' => 'View',
            'title' => 'View Whole Bill List',
            'link' => 'View Whole Bill List'
        ];

       // Get the bill number and company ID from the request
    $billNo = $req->billno;
    $companyId = $req->companyid;

    // Fetch the company name based on the company ID
    $companyName = company::where('id', $companyId)->value('name');
                        
            // Check if items exist for the provided bill number and company name
            $items = item::where('billno',$req->billno ) ->where('companyid', $req->companyid) ->get();
           

            $totalSum = $items->sum('total');
            return view('viewwholeitembill.list', [
                'all' => $items,
                'breadcrumb' => $breadcrumb,
                'billNo' => $billNo,
                'companyName' => $companyName,
                'totalSum' => $totalSum
            ]);
    }

}

