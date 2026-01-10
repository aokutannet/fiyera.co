<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('proposals')->latest();

        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $query->where(function($q) use ($searchTerm) {
                $q->where('company_name', 'like', "%{$searchTerm}%")
                  ->orWhere('contact_person', 'like', "%{$searchTerm}%")
                  ->orWhere('company_email', 'like', "%{$searchTerm}%")
                  ->orWhere('legal_title', 'like', "%{$searchTerm}%")
                  ->orWhere('tax_number', 'like', "%{$searchTerm}%");
            });
        }

        $customers = $query->paginate(10)->withQueryString();
        return view('tenant.customers.index', compact('customers'));
    }

    public function create()
    {
        $tenant = \App\Models\Tenant::find(auth()->user()->tenant_id);
        $plan = $tenant->plan;
        
        $limit = $plan->limits['customer_count'] ?? 0;
        
        if ($limit != -1) {
             if (Customer::count() >= $limit) {
                return redirect()->route('subscription.plans')->with('error', 'Paketinizin müşteri ekleme limiti doldu. Lütfen paketinizi yükseltin.');
             }
        }

        return view('tenant.customers.create');
    }

    public function edit(Customer $customer)
    {
        return view('tenant.customers.edit', compact('customer'));
    }

    public function store(Request $request)
    {
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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($customer);
        }

        return redirect()->route('customers.index')->with('success', 'Müşteri başarıyla eklendi.');
    }

    public function update(Request $request, Customer $customer)
    {
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

        return redirect()->route('customers.index')->with('success', 'Müşteri bilgileri güncellendi.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tenant.customers,id'
        ]);

        $count = 0;
        foreach ($request->ids as $id) {
            $customer = Customer::find($id);
            if ($customer) {
                // Check if customer has proposals, invocies etc before deleting if needed.
                // For now, straight delete or soft delete if model supports it.
                $customer->delete();
                $count++;
            }
        }

        return redirect()->route('customers.index')->with('success', "{$count} adet müşteri başarıyla silindi.");
    }
    
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Müşteri silindi.');
    }

    public function toggleStatus(Customer $customer)
    {
        $customer->status = $customer->status === 'active' ? 'passive' : 'active';
        $customer->save();

        $message = $customer->status === 'active' ? 'Müşteri aktif edildi.' : 'Müşteri pasife alındı.';
        return back()->with('success', $message);
    }
}
