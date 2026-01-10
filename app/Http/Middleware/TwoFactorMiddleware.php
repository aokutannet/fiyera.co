<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && !$user->google_id) {
            
            $needsVerification = false;

            // 1. Check if email is verified (registration flow)
            if (is_null($user->email_verified_at)) {
                $needsVerification = true;
            }

            // 2. Check if 2FA is enabled (login flow)


            if ($needsVerification) {
                if (!$request->is('verify*') && !$request->is('logout')) {
                     return redirect()->route('verify.index');
                }
            }
        }

        return $next($request);
    }
}
