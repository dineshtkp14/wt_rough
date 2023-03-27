<?php

namespace App\Http\Controllers;

use App\Models\customerinfo;
use Illuminate\Http\Request;

class Examcontroller extends Controller
{
    public function examshow(){
        
        $exams = customerinfo::paginate(10);
        return view('exam.manage',compact('exams'));
    }
 
    public function examshow_ajax(Request $request){
       
        if($request->ajax())
         {

              $search = $request->get('search');
              $search = str_replace(" ", "%", $search);
            	$exams = customerinfo::where('id', 'like', '%'.$search.'%')
                          ->orWhere('name', 'like', '%'.$search.'%')
                          ->paginate(10);
            return view('exam.manage', compact('exams'));
         }
    }
}
