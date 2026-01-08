<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\TwoFactorCode;
use Illuminate\Support\Facades\Mail;

class TwoFactorController extends Controller
{
    public function index()
    {
        return view('auth.two_factor');
    }

    public function store(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required|string',
        ]);

        $user = auth()->user();

        if ($request->two_factor_code == $user->two_factor_code) {
            
            if ($user->two_factor_expires_at < now()) {
                return back()->withErrors(['two_factor_code' => 'Doğrulama kodunun süresi dolmuş.']);
            }

            $user->resetTwoFactorCode();
            
            // Mark email as verified if not already (for registration flow)
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
                $user->save();
            }

            session(['two_factor_verified' => true]);

            return redirect()->intended('dashboard');
        }

        return back()->withErrors(['two_factor_code' => 'Girdiğiniz kod hatalı.']);
    }

    public function resend()
    {
        $user = auth()->user();
        $user->generateTwoFactorCode();
        Mail::to($user->email)->send(new TwoFactorCode($user->two_factor_code));
        
        return back()->with('status', 'Doğrulama kodu tekrar gönderildi.');
    }
}
