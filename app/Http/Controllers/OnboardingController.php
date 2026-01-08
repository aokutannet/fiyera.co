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

        if ($hasCompletedWizard) {
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
            'target_audience' => 'required',
            'currency' => 'required',
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
            
            // Prepare Billing Details for Autofill (Prioritize Settings as Source of Truth)
            // User Request: "/settings buradaki genel ayalardan gelecek"
            $settings = DB::connection('tenant')->table('settings')
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
        
        // Set 14-day trial period
        $trialEndsAt = now()->addDays(14);

        // Update tenant with trial details
        // We use tenant table columns for trial management as requested
        $tenant->update([
            'subscription_plan' => $plan->slug,
            'subscription_plan_id' => $plan->id,
            'subscription_status' => 'trial',
            'trial_starts_at' => now(),
            'trial_ends_at' => $trialEndsAt,
            'onboarding_completed' => true,
            'status' => 'active', 
        ]);

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

        if ($request->has('from_subscription_page')) {
            return redirect()->route('subscription.index')->with('plan_upgraded', true);
        }

        return redirect()->route('onboarding.processing');
    }
}
