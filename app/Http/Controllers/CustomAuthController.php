<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session as FacadesSession;
use Session;

class CustomAuthcontroller extends Controller
{
    public function viewuser()
    {

        if (Auth::check()) {

            $users = User::orderBy('id', 'DESC')->paginate(115);
            return view('viewuser', ['itm' => $users]);
        }

        return redirect('/login');
    }


    public function destroy($id, Request $req)
    {

        $user = User::findOrFail($id);


        $user->delete();

        //$req->session()->flash('success','Deleted Sucessfully');
        return redirect()->route('viewuser')->with('success', 'Deleted sucessfully');
    }




    public function index()
    {
        return view('login');
    }


    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard')
                ->with('message', 'Signed in!');
        }

        return redirect('/login')->with('message', 'Login details are not valid!');
    }










    public function signup()
    {
        return view('registration');
    }

    public function changepass()
    {
        return view('changepassword');
    }


    public function changePassword()
    {
        if (Auth::check()) {

            return view('changepassword');
        }


        return redirect('/login');
    }

    public function updatePassword(Request $request)
    {
        # Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',

        ]);


        #Match The Old Password
        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return back()->with("error", "Old Password Doesn't match!");
        }


        #Update the new Password
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with("status", "Password changed successfully!");
    }

    public function signupsave(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phoneno' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $data = $request->all();
        $check = $this->create($data);

        return redirect("/login")->with('signupmessage', 'User Registration Success!! You can Login Now');;
    }

    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'phoneno' => $data['phoneno'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }

    public function dashboard()
    {
        if (Auth::check()) {
            return view('dashboard');
        }
        return redirect('/login');
    }

    public function signOut()
    {
        FacadesSession::flush();
        Auth::logout();

        return redirect('login');
    }
}
