<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Plan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        
        // Actual revenue for the current month from subscriptions
        $monthlyRevenue = \App\Models\Subscription::where('status', 'active')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('price');

        $totalUsers = 0; 
        // We can't easily sum users across all databases without iterating
        // or keeping a count on the tenant model. 
        // For now, let's just count the tenant users if we had a central table,
        // but since users are in tenant tables (mostly), or central users linked to tenants?
        // Wait, User model is central but scopes to tenant?
        // Let's check User model. active workspace has User.php.
        // It seems User has 'tenant_id'. So we can query User::count() directly if it's a central table.
        // If it's central table with tenant_id, then User::count() is global users.
        
        $totalUsers = \App\Models\User::count();

        $recentTenants = Tenant::with('plan')->latest()->take(5)->get();
        $recentOrders = \App\Models\Subscription::with(['tenant', 'plan'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('totalTenants', 'activeTenants', 'monthlyRevenue', 'totalUsers', 'recentTenants', 'recentOrders'));
    }
}
