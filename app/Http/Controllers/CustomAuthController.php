<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Services\SmsService;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
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




    public function forlogin()
    {
        if (Auth::check()) {
            Auth::logout(); // Log out the user
            Session::flush(); // Clear all session data
            return redirect()->route('login'); // Redirect back to /login

        }
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

            $request->session()->put('user_email', $request->email);

            return redirect()->intended('dashboard')
                ->with('message', 'Signed in!');
        }

        return redirect('/login')->with('message', 'Login details are not valid!');
    }










    public function signup()
    {
        if (Auth::check()) {

            return view('registration');
        }
         return redirect('/login');
       
    }

    

    public function changePassword()
    {
        if (Auth::check()) {
            $breadcrumb= [
                'subtitle'=>'Password',
                'title'=>'Change Password',
                'link'=>'Change Password'
            ];
            return view('changepassword',['breadcrumb'=>$breadcrumb]);;

            
        }


        return redirect('/login');
    }

    public function updatePassword(Request $request)
    {
        # Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required||min:6|confirmed',

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

    public function forgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }

        return view('Auth.forgot-password');
    }

    public function sendPasswordResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $query = User::where('email', $request->email);

        if (Schema::hasColumn('users', 'role')) {
            $query->where('role', 'admin');
        }

        $user = $query->first();

        if (!$user || empty($user->phoneno)) {
            return back()
                ->withInput()
                ->with('message', 'Administrator email or mobile number was not found.');
        }

        if (!$this->isValidMobileNumber($user->phoneno)) {
            return back()
                ->withInput()
                ->with('message', 'Administrator mobile number is invalid. Please update the admin mobile number first.');
        }

        $recentOtp = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->where('created_at', '>=', now()->subMinute())
            ->first();

        if ($recentOtp) {
            return back()
                ->withInput()
                ->with('message', 'Please wait one minute before requesting another OTP.');
        }

        $otp = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        $phone = SmsService::formatPhoneNumber($user->phoneno);
        $message = "Your WT administrator password reset OTP is {$otp}. It expires in 10 minutes.";
        $response = (new SmsService())->send($phone, $message);

        if (empty($response['success'])) {
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();

            $error = $response['body'] ?? $response['error'] ?? 'Unknown SMS gateway error.';

            return back()
                ->withInput()
                ->with('message', 'OTP could not be sent: ' . $error);
        }

        $request->session()->put('password_reset_email', $user->email);

        return redirect()
            ->route('password.otp.form')
            ->with('status', 'OTP sent to mobile ' . $this->maskPhoneNumber($phone) . '.');
    }

    public function passwordOtpForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }

        if (!$request->session()->has('password_reset_email')) {
            return redirect()->route('password.request');
        }

        return view('Auth.reset-password-otp');
    }

    public function resetPasswordWithOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
            'password' => 'required|min:6|confirmed',
        ]);

        $email = $request->session()->get('password_reset_email');

        if (!$email) {
            return redirect()->route('password.request')->with('message', 'Please request a new OTP.');
        }

        $reset = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$reset || now()->diffInMinutes($reset->created_at) > 10) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return redirect()->route('password.request')->with('message', 'OTP expired. Please request a new OTP.');
        }

        if (!Hash::check($request->otp, $reset->token)) {
            return back()->with('message', 'Invalid OTP. Please check the SMS and try again.');
        }

        $query = User::where('email', $email);

        if (Schema::hasColumn('users', 'role')) {
            $query->where('role', 'admin');
        }

        $user = $query->first();

        if (!$user) {
            return redirect()->route('password.request')->with('message', 'Administrator account was not found.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        $request->session()->forget('password_reset_email');

        return redirect('/login')->with('signupmessage', 'Password reset successfully. You can login now.');
    }

    private function maskPhoneNumber($phone)
    {
        $phone = (string) $phone;

        if (strlen($phone) <= 4) {
            return str_repeat('*', strlen($phone));
        }

        return str_repeat('*', strlen($phone) - 4) . substr($phone, -4);
    }

    private function isValidMobileNumber($phone)
    {
        $phone = preg_replace('/\D+/', '', (string) $phone);

        if (strlen($phone) === 13 && substr($phone, 0, 3) === '977') {
            $phone = substr($phone, 3);
        }

        if (!preg_match('/^9\d{9}$/', $phone)) {
            return false;
        }

        return !in_array($phone, [
            '9999999999',
            '1234567890',
            '1111111111',
            '0000000000',
        ], true);
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
            'address' => $data['address'],
            'added_by' => $data['user_email'],

            
            'password' => Hash::make($data['password'])
        ]);
    }

    public function dashboard()
    {
        if (Auth::check()) {
            return view('dashboard.dashboard');
        }
        return redirect('/login');
    }

    public function signOut(Request $request)
    {
       

        FacadesSession::flush();
        Auth::logout();
        $request->session()->forget('user_email');

        return redirect('login');
    }
}
