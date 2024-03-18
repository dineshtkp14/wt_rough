<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\item;
use App\Models\company;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;




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
    $companyall = company::where('id', $req->companyid)->orderBy('id', 'DESC')->get();
              
            // Check if items exist for the provided bill number and company name
            $items = item::where('billno',$req->billno ) ->where('companyid', $req->companyid) ->get();
           

            $totalSum = $items->sum('total');
            return view('viewwholeitembill.list', [
                'all' => $items,
                'breadcrumb' => $breadcrumb,
                'billNo' => $billNo,
                'companyName' => $companyName,
                'totalSum' => $totalSum,
                'companyid' => $req->companyid,
                'companyall' => $companyall,

            ]);
    }


    public function PDF_returnWholebillitems(Request $req)
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
           
            $companyall = company::where('id', $req->companyid)->orderBy('id', 'DESC')->get();

            $totalSum = $items->sum('total');
            $pdfView =  view('viewwholeitembill.pdfwholebilllist', [
                'all' => $items,
                'breadcrumb' => $breadcrumb,
                'billNo' => $billNo,
                'companyName' => $companyName,
                'totalSum' => $totalSum,
                'companyid' => $req->companyid,
                'companyall' => $companyall,

                            
            ]);

    
            // Generate PDF using FacadePdf
            $pdf = FacadePdf::setOptions(['dpi' => 150, 'defaultFont' => 'dejavu serif'])->loadHtml($pdfView);
    
            // Save the PDF to a temporary file
            $pdfFile = tempnam(sys_get_temp_dir(), 'invoice');
            $pdf->save($pdfFile);
    
            // Send headers to instruct the browser to open the PDF in a new tab
            return response()->file($pdfFile, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="invoice.pdf"',
            ]);
    }



}

