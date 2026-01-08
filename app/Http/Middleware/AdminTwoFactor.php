<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminTwoFactor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('super_admin')->check() && !session('admin_two_factor_verified')) {
             if (!$request->is('superadmin/verify*') && !$request->is('superadmin/logout')) {
                return redirect()->route('admin.verify.index');
             }
        }

        return $next($request);
    }
}
