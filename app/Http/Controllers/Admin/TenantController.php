<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('plan')->latest()->paginate(10);
        return view('admin.tenants.index', compact('tenants'));
    }

    public function show(Tenant $tenant)
    {
        // Calculate stats by connecting to tenant DB
        $dbName = "tenant_{$tenant->id}_teklif";
        $customerCount = 0;
        $proposalCount = 0;
        $productCount = 0;
        $usersCount = $tenant->users()->count();

        try {
            // Dynamically configure a connection for this tenant
            config(['database.connections.temp_tenant.driver' => 'mysql']);
            config(['database.connections.temp_tenant.host' => env('DB_HOST', '127.0.0.1')]);
            config(['database.connections.temp_tenant.port' => env('DB_PORT', '3306')]);
            config(['database.connections.temp_tenant.database' => $dbName]);
            config(['database.connections.temp_tenant.username' => env('DB_USERNAME', 'root')]);
            config(['database.connections.temp_tenant.password' => env('DB_PASSWORD', '')]);
            
            // Reconnect
            DB::purge('temp_tenant');
            
            $customerCount = DB::connection('temp_tenant')->table('customers')->count();
            $proposalCount = DB::connection('temp_tenant')->table('proposals')->count();
            $productCount = DB::connection('temp_tenant')->table('products')->count();

            // Fetch Billing Settings directly from Tenant DB (Source of Truth)
            $settings = DB::connection('temp_tenant')->table('settings')
                ->whereIn('key', ['company_name', 'tax_office', 'tax_number', 'company_address', 'province', 'district', 'country'])
                ->pluck('value', 'key');
            
            $billingDetails = [
                 'company_name' => $settings['company_name'] ?? $tenant->billing_details['company_name'] ?? '',
                 'tax_office' => $settings['tax_office'] ?? $tenant->billing_details['tax_office'] ?? '',
                 'tax_number' => $settings['tax_number'] ?? $tenant->billing_details['tax_number'] ?? '',
                 'address' => $settings['company_address'] ?? $tenant->billing_details['address'] ?? '',
                 'city' => $settings['province'] ?? $tenant->billing_details['city'] ?? '',
                 'district' => $settings['district'] ?? $tenant->billing_details['district'] ?? '',
                 'country' => $settings['country'] ?? $tenant->billing_details['country'] ?? 'TR',
            ];
            
        } catch (\Exception $e) {
            // Database might not exist or connection failed, fallback to stored details
            $billingDetails = $tenant->billing_details ?? [];
        }

        $plans = Plan::all();
        // $orders = $tenant->subscriptions()->with('plan')->orderBy('created_at', 'desc')->get(); // Old: Just successes
        $attempts = \App\Models\SubscriptionAttempt::where('tenant_id', $tenant->id)->with('plan')->latest()->get();

        return view('admin.tenants.show', compact('tenant', 'customerCount', 'proposalCount', 'productCount', 'usersCount', 'plans', 'attempts', 'billingDetails'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,passive',
            'subscription_plan_id' => 'required|exists:plans,id',
            'billing_period' => 'sometimes|in:monthly,yearly',
        ]);

        if ($request->has('billing_period')) {
            $billingDetails = $tenant->billing_details ?? [];
            $billingDetails['period'] = $request->billing_period;
            $tenant->billing_details = $billingDetails;
        }

        $tenant->update([
            'status' => $validated['status'],
            'subscription_plan_id' => $validated['subscription_plan_id'],
            'billing_details' => $tenant->billing_details, // Explicitly save the modified attribute
        ]);

        return back()->with('success', 'Firma bilgileri güncellendi.');
    }

    public function destroy(Tenant $tenant)
    {
        $dbName = "tenant_{$tenant->id}_teklif";

        try {
            // Drop the database
            DB::statement("DROP DATABASE IF EXISTS `$dbName`");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Veritabanı silinirken hata oluştu: ' . $e->getMessage()]);
        }

        // Delete all associated users from the central users table
        \App\Models\User::where('tenant_id', $tenant->id)->delete();

        // Delete the tenant record
        $tenant->delete();

        return redirect()->route('admin.tenants.index')->with('success', 'Firma ve tüm verileri kalıcı olarak silindi.');
    }

    public function impersonate(Tenant $tenant)
    {
        // Find the owner user or the first user
        $user = $tenant->users()->where('is_owner', true)->first() ?? $tenant->users()->first();

        if (!$user) {
            return back()->withErrors(['error' => 'Bu firmaya ait yönetici kullanıcısı bulunamadı.']);
        }

        // Log in to the main guard (web) as this user
        // Note: This does not log out the 'super_admin' guard, allowing the admin to stay logged in on the admin panel
        // while accessing the tenant dashboard.
        \Illuminate\Support\Facades\Auth::guard('web')->login($user);

        return redirect()->route('dashboard')->with('success', "{$tenant->name} firmasına yönetici olarak giriş yapıldı.");
    }
}
