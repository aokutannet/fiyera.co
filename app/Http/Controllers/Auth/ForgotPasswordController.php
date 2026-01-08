<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('tenant.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        // Burada normalde mail gönderilir.
        // Şimdilik sadece başarılı mesajı dönelim.
        
        return back()->with('status', 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi (Simüle edildi).');
    }
}
