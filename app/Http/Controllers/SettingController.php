<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all settings
        $settings = Setting::all();

        // Group settings by their 'group' column
        $groupedSettings = $settings->groupBy('group');

        return view('tenant.settings.index', compact('groupedSettings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $data = $request->except(['_token']);

        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $tenantId = auth()->user()->tenant_id;
                // Generate a unique name
                $filename = $key . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs("tenants/{$tenantId}/uploads", $filename, 'public');
                $value = $path;
            }
            
            // Only update if value is not null, or if it is explicitly set
            // For files, if no file is uploaded, we don't overwrite with null unless intentional
            if ($value !== null) {
                Setting::where('key', $key)->update(['value' => $value]);
            }
        }

        // Sync with Tenant Billing Details for Admin Visibility
        try {
            $tenant = \App\Models\Tenant::find(auth()->user()->tenant_id);
            $currentBilling = $tenant->billing_details ?? [];
            
            // Map settings to billing structure
            $settingsMap = [
                'company_name' => 'company_name',
                'tax_office' => 'tax_office',
                'tax_number' => 'tax_number',
                'company_address' => 'address',
                'province' => 'city',
                'district' => 'district',
                'country' => 'country',
            ];

            $settings = Setting::whereIn('key', array_keys($settingsMap))->pluck('value', 'key');
            
            foreach ($settingsMap as $settingKey => $billingKey) {
                if (isset($settings[$settingKey])) {
                    $currentBilling[$billingKey] = $settings[$settingKey];
                }
            }
            
            $tenant->update(['billing_details' => $currentBilling]);

        } catch (\Exception $e) {
            // Log silent error, don't block settings update
            \Illuminate\Support\Facades\Log::warning('Failed to sync tenant billing details: ' . $e->getMessage());
        }

        return redirect()->route('settings.index')->with('success', 'Ayarlar başarıyla güncellendi.');
    }

    public function removeFile(Request $request)
    {
        $key = $request->input('key');
        $setting = Setting::where('key', $key)->first();

        if ($setting && $setting->value) {
            // Optional: Delete file from storage if needed
            // Storage::disk('public')->delete($setting->value);
            
            $setting->update(['value' => '']);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function deleteAccount(Request $request)
    {
        $user = auth()->user();
        $tenant = \App\Models\Tenant::find($user->tenant_id);

        if (!$tenant) {
            return back()->with('error', 'Firma bulunamadı.');
        }

        // 1. Drop the database
        $dbName = "tenant_{$tenant->id}_teklif";
        try {
            \Illuminate\Support\Facades\DB::statement("DROP DATABASE IF EXISTS `$dbName`");
        } catch (\Exception $e) {
            // Log error but continue with account deletion
            \Illuminate\Support\Facades\Log::error("Failed to drop database for tenant {$tenant->id}:Html " . $e->getMessage());
        }

        // 2. Delete all associated users from the central users table
        \App\Models\User::where('tenant_id', $tenant->id)->delete();

        // 3. Update status to 'deleted' and Soft Delete
        $tenant->status = 'deleted'; 
        $tenant->save();
        $tenant->delete();

        // 3. Logout
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Hesabınız ve verileriniz silinmiştir. Verileriniz güvenli bir şekilde arşivlenmiştir.');
    }
}
