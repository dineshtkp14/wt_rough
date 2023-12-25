<?php

namespace App\Http\Controllers;

use App\Models\customerinfo;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\Auth;

class CustomerPdfGenerator extends Controller
{
    public function index()
    {
        if(Auth::check()){

         $cus=customerinfo::orderBy('id','DESC')->paginate(6);;
       

        // return view('customerinfo.list',['all'=>$cus]);   
    }
    return redirect('/login');
 }
    public function pdfview(){

        if(Auth::check()){

        $cus=customerinfo::orderBy('id','DESC')->paginate(6);;
       
      
        return view('pdf.pdf_view',['all'=>$cus]);

    }
    return redirect('/login');
 }
    public function pdfgenerate(){
        if(Auth::check()){

        $cus=customerinfo::orderBy('id','DESC')->get();;
        
        $pdfview=FacadePdf::setOptions(['dpi' => 150,'defaultFont' => 'dejavu serif'])->loadView('pdf.pdf_convert',['all'=>$cus]);;


     dd($pdfview);
     
       return $pdfview->download('invoice.pdf');

   
      
        
    }
    return redirect('/login');
}
}
