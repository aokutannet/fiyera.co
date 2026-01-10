<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user || !$user->tenant) {
            return $next($request);
        }

        $tenant = $user->tenant;

        // Ensure onboarding is complete first
        if (!$tenant->onboarding_completed && !$request->routeIs('tenant.onboarding.*')) {
            // Ensure they are not stuck in a loop if onboarding route is different
             return $next($request);
        }

        // Check if tenant is in Read-Only mode (Expired Trial or Subscription)
        if ($tenant->isReadOnly()) {
             // Always allow subscription management, logout, and profile
            if ($request->routeIs('subscription.*') || $request->routeIs('logout') || $request->routeIs('profile.*')) {
                return $next($request);
            }

            // Block mutating methods (POST, PUT, PATCH, DELETE)
            if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                 if ($request->expectsJson()) {
                     return response()->json(['message' => 'Subscription expired. Read-only mode.'], 403);
                 }
                 return redirect()->route('subscription.index')->with('error', 'Deneme süreniz doldu. İşlem yapabilmek için lütfen paketinizi yükseltin.');
            }

            // Block specific Create/Edit views to prevent showing forms that won't submit
            // Assuming route names follow standard resource conventions (create, edit)
            $routeName = $request->route()->getName();
            if ($routeName && (str_contains($routeName, '.create') || str_contains($routeName, '.edit'))) {
                 return redirect()->route('subscription.index')->with('error', 'Deneme süreniz doldu. Yeni kayıt oluşturmak veya düzenlemek için .');
            }
            
            // Allow viewing (Index, Show) - Read Only
        }

        return $next($request);
    }
}
