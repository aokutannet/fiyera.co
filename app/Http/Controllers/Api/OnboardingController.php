<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    /**
     * Check onboarding status of the tenant.
     */
    /**
     * Check onboarding status of the tenant.
     */
    public function checkStatus(Request $request)
    {
        $tenant = $request->user()->tenant;
        
        if (!$tenant) {
            return response()->json(['message' => 'Firma bulunamadı.'], 404);
        }

        // Check if onboarding questions are already answered
        $hasCompletedWizard = \Illuminate\Support\Facades\DB::connection('tenant')->table('settings')
            ->where('key', 'onboarding_sector')
            ->exists();

        // Check if company details are filled
        $hasCompanyDetails = \Illuminate\Support\Facades\DB::connection('tenant')->table('settings')
            ->where('key', 'company_name')
            ->exists();

        $currentStep = 'wizard';
        if ($hasCompletedWizard) {
            $currentStep = 'company_details';
        }
        if ($hasCompletedWizard && $hasCompanyDetails) {
            $currentStep = 'select_plan';
        }
        if ($tenant->onboarding_completed) {
            $currentStep = 'completed';
        }

        return response()->json([
            'onboarding_completed' => (bool) $tenant->onboarding_completed,
            'current_step' => $currentStep,
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'plan_id' => $tenant->subscription_plan_id,
            ]
        ]);
    }

    /**
     * Get Wizard Questions
     */
    public function questions()
    {
        $questions = \App\Models\OnboardingQuestion::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function ($q) {
                return [
                    'id' => $q->step_id,
                    'question' => $q->question,
                    'subtext' => $q->subtext,
                    'options' => $q->options,
                    'type' => $q->type,
                    'has_other' => $q->has_other
                ];
            });

        return response()->json(['data' => $questions]);
    }

    /**
     * Store Wizard Answers
     */
    public function storeWizard(Request $request)
    {
        $questions = \App\Models\OnboardingQuestion::where('is_active', true)->get();
        
        $rules = [];
        foreach ($questions as $q) {
            $rules[$q->step_id] = 'required';
        }
        
        $request->validate($rules);

        $tenant = $request->user()->tenant;
        $answers = [];

        foreach ($questions as $q) {
            $key = $q->step_id;
            $value = $request->input($key);

            // Handle "Other" custom input
            if ($q->has_other && $value === 'Diğer') {
                $customKey = $key . '_custom';
                if ($request->has($customKey)) {
                    $value = $request->input($customKey);
                }
            }
            
            // Handle array values (checkboxes)
            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            $answers[] = [
                'key' => 'onboarding_' . $key,
                'label' => strip_tags($q->question), // Remove any HTML if present
                'value' => $value
            ];
        }

        foreach ($answers as $answer) {
            \Illuminate\Support\Facades\DB::connection('tenant')->table('settings')->updateOrInsert(
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

        return response()->json(['message' => 'Cevaplar kaydedildi.', 'next_step' => 'company_details']);
    }

    /**
     * Store Company Details
     */
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

        $tenant = $request->user()->tenant;

        // Update Tenant Table main info
        $tenant->update([
            'name' => $request->company_name,
            // Only update email/phone if provided, otherwise keep existing
            'phone' => $request->company_phone ?? $tenant->phone,
            'email' => $request->company_email ?? $tenant->email, 
        ]);

        // Map settings to be saved in tenant settings table
        // Format: key => [label, group, type, description]
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
                \Illuminate\Support\Facades\DB::connection('tenant')->table('settings')->updateOrInsert(
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

        return response()->json(['message' => 'Firma bilgileri kaydedildi.', 'next_step' => 'select_plan']);
    }

    /**
     * List available plans for onboarding.
     */
    public function plans()
    {
        $plans = Plan::where('is_active', true)->orderBy('price_monthly', 'asc')->get();
        
        return response()->json(['data' => $plans]);
    }

    /**
     * Start the 14-day free trial with selected plan.
     */
    public function startTrial(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $tenant = $request->user()->tenant;
        
        // Prevent re-onboarding if already done (unless we want to allow re-selection, but typically once)
        if ($tenant->onboarding_completed) {
            return response()->json(['message' => 'Kurulum zaten tamamlanmış.'], 400);
        }

        $plan = Plan::find($request->plan_id);

        $tenant->update([
            'subscription_plan_id' => $plan->id,
            'subscription_status' => 'trial',
            'trial_starts_at' => now(),
            'trial_ends_at' => now()->addDays(14),
            'onboarding_completed' => true,
        ]);

        // Log this activity? "Trial Started"
        
        return response()->json([
            'message' => 'Deneme süresi başlatıldı.',
            'trial_ends_at' => $tenant->trial_ends_at->format('d.m.Y'),
            'onboarding_completed' => true
        ]);
    }
}
