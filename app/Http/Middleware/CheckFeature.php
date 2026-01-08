<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = auth()->user();

        if (!$user || !$user->tenant) {
             return redirect()->route('login');
        }

        if (!$user->tenant->hasFeature($feature)) {
             if ($request->ajax() || $request->wantsJson()) {
                 return response()->json(['message' => 'Bu özellik mevcut paketinizde aktif değil.', 'required_feature' => $feature], 403);
             }
             
             // Redirect back with a flash message/flag to trigger the popup
             return redirect()->back()->with('upgrade_required', true)->with('feature_name', $feature);
        }

        return $next($request);
    }
}
