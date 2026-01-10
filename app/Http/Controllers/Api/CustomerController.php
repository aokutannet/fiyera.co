<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        $query = Customer::orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('company_email', 'like', "%{$search}%")
                  ->orWhere('mobile_phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate($perPage);

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        // Check limits
        $tenant = \App\Models\Tenant::find(auth()->user()->tenant_id);
        $plan = $tenant->plan;
        
        $limit = $plan->limits['customer_count'] ?? 0;
        
        if ($limit != -1) {
             if (Customer::count() >= $limit) {
                return response()->json([
                    'message' => 'Paketinizin müşteri ekleme limiti doldu. Lütfen paketinizi yükseltin.',
                    'limit_reached' => true
                ], 403);
             }
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'category' => 'nullable|string|max:255',
            'landline_phone' => 'nullable|string|max:255',
            'mobile_phone' => 'nullable|string|max:255',
            'legal_title' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'type' => 'required|in:individual,legal',
            'tax_number' => 'nullable|string|max:255',
            'tax_office' => 'nullable|string|max:255',
        ]);

        $customer = Customer::create($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'subject_type' => Customer::class,
            'subject_id' => $customer->id,
            'event' => 'created',
            'description' => 'Müşteri oluşturuldu (Mobil).',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'properties' => ['source' => 'mobile_api'],
        ]);

        return response()->json([
            'message' => 'Müşteri başarıyla oluşturuldu.',
            'customer' => $customer
        ], 201);
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'category' => 'nullable|string|max:255',
            'landline_phone' => 'nullable|string|max:255',
            'mobile_phone' => 'nullable|string|max:255',
            'legal_title' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'type' => 'required|in:individual,legal',
            'tax_number' => 'nullable|string|max:255',
            'tax_office' => 'nullable|string|max:255',
        ]);

        $customer->update($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'subject_type' => Customer::class,
            'subject_id' => $customer->id,
            'event' => 'updated',
            'description' => 'Müşteri güncellendi (Mobil).',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'properties' => ['source' => 'mobile_api'],
        ]);

        return response()->json([
            'message' => 'Müşteri başarıyla güncellendi.',
            'customer' => $customer
        ]);
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        
        $customer->delete();

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'subject_type' => Customer::class,
            'subject_id' => $id,
            'event' => 'deleted',
            'description' => 'Müşteri silindi (Mobil).',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'properties' => ['source' => 'mobile_api'],
        ]);

        return response()->json(['message' => 'Müşteri başarıyla silindi.']);
    }
}
