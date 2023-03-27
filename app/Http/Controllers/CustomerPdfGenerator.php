<?php

namespace App\Http\Controllers;

use App\Models\customerinfo;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\Snappy\Facades\SnappyPdf;

class CustomerPdfGenerator extends Controller
{
    public function index()
    {
        
         $cus=customerinfo::orderBy('id','DESC')->paginate(6);;
       

        // return view('customerinfo.list',['all'=>$cus]);   
    }

    public function pdfview(){

        $cus=customerinfo::orderBy('id','DESC')->paginate(6);;
       
      
        return view('pdf.pdf_view',['all'=>$cus]);

    }

    public function pdfgenerate(){

        $cus=customerinfo::orderBy('id','DESC')->get();;
        
        $pdfview=FacadePdf::setOptions(['dpi' => 150,'defaultFont' => 'dejavu serif'])->loadView('pdf.pdf_convert',['all'=>$cus]);;


     dd($pdfview);
     
       return $pdfview->download('invoice.pdf');

   
      
        
    }
}
