<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('super_admin')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Giriş bilgileri hatalı.',
        ])->onlyInput('email');
    }

    public function showVerifyForm()
    {
        if(!session('admin_auth_passed') && !auth('super_admin')->check()){
             return redirect()->route('admin.login');
        }
        return view('admin.auth.verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required|numeric',
        ]);

        $user = Auth::guard('super_admin')->user();

        if ($request->two_factor_code == $user->two_factor_code) {
             
            if($user->two_factor_expires_at < now()) {
                return back()->withErrors(['two_factor_code' => 'Doğrulama kodunun süresi dolmuş.']);
            }

            $user->resetTwoFactorCode();
            session(['admin_two_factor_verified' => true]);
            
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['two_factor_code' => 'Geçersiz doğrulama kodu.']);
    }

    public function resend()
    {
        $user = Auth::guard('super_admin')->user();
        $this->generateTwoFactorCode($user);

        return back()->with('status', 'Doğrulama kodu tekrar gönderildi.');
    }

    public function logout(Request $request)
    {
        Auth::guard('super_admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->forget(['admin_auth_passed', 'admin_two_factor_verified']);

        return redirect()->route('admin.login');
    }


}
