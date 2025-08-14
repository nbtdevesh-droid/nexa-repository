<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function admin_login_func(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('web')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard')->withSuccess('Login Successfully');
        }

        if (Auth::guard('member')->attempt($credentials, $request->remember)) {
            $member = Auth::guard('member')->user();
            if($member->status == 1){
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard')->with('success', 'Login Successfully');
            }else{
                Auth::guard('member')->logout();
                return back()->with('error', 'Account blocked by admin.');
            }
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function recover_password_func(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        if(!empty($request->email)){
            if(Admin::where('email', $request->email)->first()){
                $admin = Admin::where('email', $request->email)->first();

                $token = Str::random(64);

                $updatePassword = DB::table('password_reset_tokens')->where(['email' => $admin->email])->first();
                if(!$updatePassword){
                    DB::table('password_reset_tokens')->insert([
                        'email' => $admin->email,
                        'token' => $token,
                        'created_at' => now()->addMinutes(60),
                    ]);
                }else{
                    DB::table('password_reset_tokens')->where(['email' => $admin->email])->update([
                        'email' => $admin->email,
                        'token' => $token,
                        'created_at' => now()->addMinutes(60),
                    ]);
                }

                Mail::send('admin.email.forgetPassword', ['token' => $token], function ($message) use ($admin) {
                    $message->to($admin->email);
                    $message->subject('Reset Password');
                });

                return back()->withSuccess('We have e-mailed your password reset link!');
            }elseif(Staff::where('email', $request->email)->first()){
                $staff = Staff::where('email', $request->email)->first();

                $token = Str::random(64);

                $updatePassword = DB::table('password_reset_tokens')->where(['email' => $staff->email])->first();
                if(!$updatePassword){
                    DB::table('password_reset_tokens')->insert([
                        'email' => $staff->email,
                        'token' => $token,
                        'created_at' => now()->addMinutes(60),
                    ]);
                }else{
                    DB::table('password_reset_tokens')->where(['email' => $staff->email])->update([
                        'email' => $staff->email,
                        'token' => $token,
                        'created_at' => now()->addMinutes(60),
                    ]);
                }

                Mail::send('admin.email.forgetPassword', ['token' => $token], function ($message) use ($staff) {
                    $message->to($staff->email);
                    $message->subject('Reset Password');
                });

                return back()->withSuccess('We have e-mailed your password reset link!');
            }else{
                return back()->with('error', 'Email id does not exist');
            }
        }
    }

    public function reset_password_func(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = DB::table('password_reset_tokens')->where(['email' => $request->email, 'token' => $request->token])->first();
        if(empty($updatePassword)){
            return back()->with('error', 'Email id does not exist');
        }
        if ($updatePassword->created_at < now()) {
            DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();
            return back()->withInput()->with('error', 'Token Expired!');
        }else{
            if (!$updatePassword) {
                return back()->withInput()->with('error', 'Invalid token!');
            }else{
                if (Admin::where('email', $request->email)->first()) {
                    $user = Admin::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
                    DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();
                    return redirect('/')->withSuccess('Your password has been changed!');
                } else {
                    $staff = Staff::where('email', $request->email)->first();
                    if($staff){
                        $user = Staff::where('email', $request->email)->update(['password' => Hash::make($request->password), 'plain_password' => $request->password]);
                        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();
                        return redirect('/')->withSuccess('Your password has been changed!');
                    }
                }
            }
        }
    }

    public function logout(Request $request)
    {
        if(Auth::guard('web')->check()){
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/')->withSuccess('Loged out successfully');
        }elseif(Auth::guard('member')->check()){
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/')->withSuccess('Loged out successfully');
        }
    }

    public function AccountDelete(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $customer = User::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
        }

        // Delete the account
        $customer->delete();

        return back()->withSuccess('Account deleted successfully.');
    }
}
