<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        $features = Plan::getAvailableFeatures();
        return view('admin.plans.create', compact('features'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'limits' => 'nullable|array',
            'is_popular' => 'boolean',
            'description' => 'required|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Ensure default values for limits if not present
        $defaultLimits = [
            'user_count' => 1,
            'proposal_monthly' => 10,
            'product_count' => 20,
            'customer_count' => 50,
        ];

        $validated['limits'] = array_merge($defaultLimits, $request->input('limits', []));
        $validated['is_popular'] = $request->has('is_popular');

        Plan::create($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Paket başarıyla oluşturuldu.');
    }

    public function edit(Plan $plan)
    {
        $features = Plan::getAvailableFeatures();
        return view('admin.plans.edit', compact('plan', 'features'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'limits' => 'nullable|array',
            'is_popular' => 'boolean',
            'description' => 'required|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Merge limits properly
        $currentLimits = $plan->limits ?? [];
        $newLimits = $request->input('limits', []);
        $validated['limits'] = array_merge($currentLimits, $newLimits);
        
        $validated['is_popular'] = $request->has('is_popular');
        if(!$request->has('features')){
            $validated['features'] = [];
        }

        $plan->update($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Paket başarıyla güncellendi.');
    }

    public function destroy(Plan $plan)
    {
        if ($plan->tenants()->count() > 0) {
            return back()->with('error', 'Bu paketi kullanan müşteriler olduğu için silemezsiniz.');
        }
        
        $plan->delete();

        return redirect()->route('admin.plans.index')->with('success', 'Paket başarıyla silindi.');
    }
}
