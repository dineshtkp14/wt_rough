<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class Employee_controller extends Controller
{
    private function requireAdmin()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if (($user->role ?? null) !== 'admin' && $user->email !== 'dineshtkp14@gmail.com') {
            abort(403, 'Only an administrator can manage user passwords.');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->requireAdmin()) return $redirect;
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
        if ($redirect = $this->requireAdmin()) return $redirect;
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
        if ($redirect = $this->requireAdmin()) return $redirect;
        if(Auth::check()){
        $validator=Validator::make($req->all(),[
            'password' => 'nullable|min:6',
        ]);
    
        if($validator->passes()){
    
            $employinfo= User::find($id);
            $employinfo->name=$req->name;
           $employinfo->address=$req->address;
           $employinfo->email=$req->email;
            $employinfo->phoneno=$req->phoneno;
           $employinfo->added_by = session('user_email');

            // Passwords are stored as hashes, so only replace it when a new
            // password was explicitly provided.
            if ($req->filled('password')) {
                $employinfo->password = Hash::make($req->password);
            }

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

        if ($redirect = $this->requireAdmin()) return $redirect;

        $cusiddelete=User::findOrFail($id);
        $cusiddelete->delete();
  
        return redirect()->route('employees.index')->with('success','Employee Deleted sucessfully'); 
        
  }

    public function toggleLock($id)
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $employee = User::findOrFail($id);
        if ($employee->isAdmin()) {
            return redirect()->route('employees.index')->with('error', 'Administrator accounts cannot be locked.');
        }

        $employee->is_locked = !$employee->is_locked;
        $employee->save();

        return redirect()->route('employees.index')->with(
            'success',
            $employee->is_locked ? 'User login locked successfully.' : 'User login unlocked successfully.'
        );
    }

    public function toggleAllLocks(Request $req)
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $lock = $req->input('action') === 'lock';
        $adminEmail = 'dineshtkp14@gmail.com';

        User::where('email', '!=', $adminEmail)
            ->update(['is_locked' => $lock]);

        return redirect()->route('employees.index')->with(
            'success',
            $lock ? 'All user logins have been locked.' : 'All user logins have been unlocked.'
        );
    }
}
