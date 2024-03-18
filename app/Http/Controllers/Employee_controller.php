<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


class Employee_controller extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $breadcrumb = [
                'subtitle' => 'View',
                'title' => 'View All Employee',
                'link' => 'View All Employee'
            ];

            return view('employee.list', ['breadcrumb' => $breadcrumb]);
        }
        
        return redirect('/login');
    }


    public function edit($id)

    {
        if(Auth::check()){
        $breadcrumb= [
            'subtitle'=>'Edit',
            'title'=>'Edit Employee Details',
            'link'=>'Edit Employee Details'
        ];
   
        $Employee=User::findOrfail($id);

        return view('employee.edit',['emp'=>$Employee,'breadcrumb'=>$breadcrumb]);   
        
    }
    return redirect('/login');
 }
    public function update($id, Request $req)
    {
        if(Auth::check()){
        $validator=Validator::make($req->all(),[

            // 'name'=>'required',
            // 'address'=>'required',
            // 'phoneno'=>'required', 
           
               
        ]);
    
        if($validator->passes()){
    
            $employinfo= User::find($id);
            $employinfo->name=$req->name;
           $employinfo->address=$req->address;
            $employinfo->email=$req->email;
            $employinfo->phoneno=$req->phoneno;
           $employinfo->added_by = session('user_email');

            $employinfo->save();
  
            return redirect()->route('employees.index')->with('success', 'Employee updated successfully');
        }
        else{
          
          

            return redirect()->route('employees.index')->withErrors($validator)->withInput();
    
        }
    
        
    }
    return redirect('/login');
 }
    public function destroy($id,Request $req){


        $cusiddelete=User::findOrFail($id);
        $cusiddelete->delete();
  
        return redirect()->route('employees.index')->with('success','Employee Deleted sucessfully'); 
        
  }
}
