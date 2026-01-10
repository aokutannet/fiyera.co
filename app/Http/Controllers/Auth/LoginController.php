<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $key = 'login|'.$request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 4)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => 'Çok fazla giriş denemesi. Lütfen '.$seconds.' saniye sonra tekrar deneyiniz.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials)) {
            \Illuminate\Support\Facades\RateLimiter::clear($key);
            $request->session()->regenerate();

            $user = Auth::user();

            // Tenant bağlantısını ayarla (ActivityLog için gerekli)
            if ($user->tenant_id) {
                $dbName = "tenant_{$user->tenant_id}_teklif";
                \Illuminate\Support\Facades\Config::set("database.connections.tenant.database", $dbName);
                \Illuminate\Support\Facades\DB::purge('tenant');
            }


            // Tenant status check is now handled by CheckSubscription middleware
            // to allow redirection to account-inactive page instead of immediate logout.

            return redirect()->intended('dashboard');
        }

        \Illuminate\Support\Facades\RateLimiter::hit($key);

        return back()->withErrors([
            'email' => 'Girdiğiniz bilgiler hatalı.',
        ])->onlyInput('email');
    }
    
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
