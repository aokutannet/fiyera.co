<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Subscription::with(['tenant', 'plan'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Subscription::with(['tenant', 'plan'])->findOrFail($id);
        
        // Billing Details Logic
        $billingDetails = $order->billing_snapshot ?? [];

        if (empty($billingDetails) && $order->tenant) {
             $tenant = $order->tenant;
             
             // 1. Try to fetch from Tenant DB Settings (Source of Truth)
             try {
                $dbName = "tenant_{$tenant->id}_teklif";
                config(['database.connections.temp_tenant_order.driver' => 'mysql']);
                config(['database.connections.temp_tenant_order.host' => env('DB_HOST', '127.0.0.1')]);
                config(['database.connections.temp_tenant_order.port' => env('DB_PORT', '3306')]);
                config(['database.connections.temp_tenant_order.database' => $dbName]);
                config(['database.connections.temp_tenant_order.username' => env('DB_USERNAME', 'root')]);
                config(['database.connections.temp_tenant_order.password' => env('DB_PASSWORD', '')]);
                
                \Illuminate\Support\Facades\DB::purge('temp_tenant_order');

                $settings = \Illuminate\Support\Facades\DB::connection('temp_tenant_order')->table('settings')
                    ->whereIn('key', ['company_name', 'tax_office', 'tax_number', 'company_address', 'province', 'district', 'country'])
                    ->pluck('value', 'key');
                
                if ($settings->isNotEmpty()) {
                    $billingDetails = [
                         'company_name' => $settings['company_name'] ?? '',
                         'tax_office' => $settings['tax_office'] ?? '',
                         'tax_number' => $settings['tax_number'] ?? '',
                         'address' => $settings['company_address'] ?? '',
                         'city' => $settings['province'] ?? '',
                         'district' => $settings['district'] ?? '',
                         'country' => $settings['country'] ?? '',
                    ];
                }
             } catch (\Exception $e) {
                 // Connection failed or DB not found
             }
             
             // 2. Final Fallback to Tenant Table
             if (empty($billingDetails)) {
                 $billingDetails = $tenant->billing_details ?? [];
             }
        }

        return view('admin.orders.show', compact('order', 'billingDetails'));
    }

    public function uploadInvoice(Request $request, $id)
    {
        $order = Subscription::findOrFail($id);
        
        $request->validate([
            'invoice' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        if ($request->hasFile('invoice')) {
            $path = $request->file('invoice')->store('invoices', 'public');
            $order->update(['invoice_path' => $path]);
        }

        return back()->with('success', 'Fatura başarıyla yüklendi.');
    }
}
