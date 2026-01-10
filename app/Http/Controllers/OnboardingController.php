<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

class OnboardingController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        
        if ($tenant->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        // Check if onboarding questions are already answered
        $hasCompletedWizard = DB::connection('tenant')->table('settings')
            ->where('key', 'onboarding_sector')
            ->exists();
            
        // Check if company details are filled
        $hasCompanyDetails = DB::connection('tenant')->table('settings')
            ->where('key', 'company_name')
            ->exists();

        if ($hasCompletedWizard) {
            if (!$hasCompanyDetails) {
                 return redirect()->route('onboarding.company-details');
            }
            return redirect()->route('onboarding.plans');
        }

        return view('tenant.onboarding.wizard');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sector' => 'required',
            'team_size' => 'required',
            'monthly_proposals' => 'required',
            // 'target_audience' => 'required', // Can be null/empty array
            // 'currency' => 'required',
            'vat_usage' => 'required',
            'proposal_criteria' => 'required',
            'proposal_preparer' => 'required',
            'previous_software' => 'required',
        ]);

        $tenant = auth()->user()->tenant;
        // Format answers for storage
        $answers = [
            ['key' => 'onboarding_sector', 'label' => 'Sektör', 'value' => $request->sector === 'Diğer' ? $request->sector_custom : $request->sector],
            ['key' => 'onboarding_team_size', 'label' => 'Ekip Sayısı', 'value' => $request->team_size],
            ['key' => 'onboarding_monthly_proposals', 'label' => 'Aylık Teklif', 'value' => $request->monthly_proposals],
            ['key' => 'onboarding_target_audience', 'label' => 'Hedef Kitle', 'value' => is_array($request->target_audience) ? implode(', ', $request->target_audience) : $request->target_audience],
            ['key' => 'onboarding_currency', 'label' => 'Para Birimi', 'value' => is_array($request->currency) ? implode(', ', $request->currency) : $request->currency],
            ['key' => 'onboarding_vat_usage', 'label' => 'KDV Kullanımı', 'value' => $request->vat_usage],
            ['key' => 'onboarding_proposal_criteria', 'label' => 'Teklif Kriteri', 'value' => $request->proposal_criteria],
            ['key' => 'onboarding_proposal_preparer', 'label' => 'Teklifi Hazırlayan', 'value' => $request->proposal_preparer],
            ['key' => 'onboarding_previous_software', 'label' => 'Önceki Yazılım', 'value' => $request->previous_software],
        ];

        foreach ($answers as $answer) {
            DB::connection('tenant')->table('settings')->updateOrInsert(
                ['key' => $answer['key']],
                [
                    'value' => $answer['value'],
                    'label' => $answer['label'],
                    'group' => 'onboarding',
                    'type' => 'text',
                    'description' => 'Onboarding sırasında verilen cevap',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Save to Tenant Table for Super Admin visibility
        $tenant->update([
            'onboarding_data' => $answers
        ]);

        return redirect()->route('onboarding.company-details');
    }

    public function companyDetails()
    {
         return view('tenant.onboarding.company_details');
    }

    public function storeCompanyDetails(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'tax_title' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'country' => 'nullable|string',
            'province' => 'nullable|string',
            'district' => 'nullable|string',
            'company_phone' => 'nullable|string',
            'company_email' => 'nullable|email',
            'tax_office' => 'nullable|string',
            'tax_number' => 'nullable|string',
        ]);

        $tenant = auth()->user()->tenant;

        // Update Tenant Table main info
        $tenant->update([
            'name' => $request->company_name,
            'phone' => $request->company_phone ?? $tenant->phone,
            'email' => $request->company_email ?? $tenant->email, 
        ]);

        // Map settings to be saved in tenant settings table
        $settingsMap = [
            'company_name' => ['Firma Adı', 'general', 'text', 'Firmanızın ticari adı'],
            'tax_title' => ['Vergi Ünvanı', 'general', 'text', 'Resmi fatura ünvanı'],
            'company_address' => ['Adres', 'general', 'textarea', 'Firma adresi'],
            'country' => ['Ülke', 'general', 'text', ''],
            'province' => ['Şehir', 'general', 'text', ''],
            'district' => ['İlçe', 'general', 'text', ''],
            'company_phone' => ['Telefon', 'general', 'text', 'İletişim numarası'],
            'company_email' => ['E-posta', 'general', 'email', 'İletişim e-postası'],
            'tax_office' => ['Vergi Dairesi', 'general', 'text', ''],
            'tax_number' => ['Vergi Numarası', 'general', 'text', ''],
        ];

        foreach ($settingsMap as $key => $meta) {
            $value = $request->input($key);
            if ($value) {
                DB::connection('tenant')->table('settings')->updateOrInsert(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'label' => $meta[0],
                        'group' => $meta[1],
                        'type' => $meta[2],
                        'description' => $meta[3],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        return redirect()->route('onboarding.plans');
    }

    public function plans()
    {
        $tenant = auth()->user()->tenant;
        
        if ($tenant->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        $plans = \App\Models\Plan::all();
        $selectedPlan = null;

        if (request()->has('plan')) {
            $selectedPlan = $plans->where('id', request('plan'))->first();
            
            // Prepare Billing Details from Settings
            $settings = DB::connection('tenant')->table('settings')
                ->whereIn('key', ['company_name', 'tax_office', 'tax_number', 'company_address', 'province', 'district', 'country'])
                ->pluck('value', 'key');
            
            $billingDetails = [
                 'company_name' => $settings['company_name'] ?? '',
                 'tax_office' => $settings['tax_office'] ?? '',
                 'tax_number' => $settings['tax_number'] ?? '',
                 'address' => $settings['company_address'] ?? '',
                 'city' => $settings['province'] ?? '',
                 'district' => $settings['district'] ?? '',
                 'country' => $settings['country'] ?? 'TR',
             ];
        } else {
             $billingDetails = [];
        }

        return view('tenant.onboarding.plans', compact('plans', 'selectedPlan', 'billingDetails'));
    }

    public function processing()
    {
        // View-only route, logic handled via frontend simulation for UX
        return view('tenant.onboarding.processing');
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan' => 'required|exists:plans,slug',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $tenant = auth()->user()->tenant;
        $plan = \App\Models\Plan::where('slug', $request->plan)->firstOrFail();
        
        // Check if plan is free
        $price = $request->billing_cycle === 'monthly' ? $plan->price_monthly : $plan->price_yearly;

        if ($price == 0) {
            // Immediately activate free plan
            $tenant->update([
                'subscription_plan' => $plan->slug,
                'subscription_plan_id' => $plan->id,
                'subscription_status' => 'active',
                'trial_ends_at' => null, // No trial needed for free plan
                'onboarding_completed' => true,
                'status' => 'active', 
            ]);

             // Create a zero-cost subscription record for consistency
             \App\Models\Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'billing_period' => $request->billing_cycle,
                'price' => 0,
                'starts_at' => now(),
                'ends_at' => now()->addYears(100), // Indefinite
                'status' => 'active',
                'payment_provider' => 'free',
            ]);
        } else {
             // Paid Plan Logic
             // If already on trial, just switch plan but keep original date
             if ($tenant->onTrial()) {
                 $tenant->update([
                     'subscription_plan' => $plan->slug,
                     'subscription_plan_id' => $plan->id,
                     // Do NOT update trial_starts_at or trial_ends_at
                 ]);
             } else {
                 // New Trial
                 $trialEndsAt = now()->addDays(14);
                 $tenant->update([
                    'subscription_plan' => $plan->slug,
                    'subscription_plan_id' => $plan->id,
                    'subscription_status' => 'trial',
                    'trial_starts_at' => now(),
                    'trial_ends_at' => $trialEndsAt,
                    'onboarding_completed' => true,
                    'status' => 'active', 
                ]);
             }
        }

        // Also reactivate the user if they were passive
        if (auth()->user()->status === 'passive') {
            auth()->user()->update(['status' => 'active']);
        }

        // Hoşgeldin maili gönder (Demo/Pro başlangıcı)
        try {
            \Illuminate\Support\Facades\Mail::to(auth()->user()->email)->send(new \App\Mail\WelcomeEmail(auth()->user()));
        } catch (\Exception $e) {
            // Mail gönderimi başarısız olsa bile akışı bozma
            \Illuminate\Support\Facades\Log::error('Welcome email sending failed: ' . $e->getMessage());
        }

        if ($request->has('from_subscription_page')) {
            return redirect()->route('subscription.index')->with('plan_upgraded', true);
        }

        return redirect()->route('onboarding.processing');
    }
}
