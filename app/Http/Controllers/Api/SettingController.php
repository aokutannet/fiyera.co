<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        // Settings are stored in the tenant database, so no need to filter by tenant_id
        $settings = \Illuminate\Support\Facades\DB::connection('tenant')->table('settings')->get();
        
        // Group settings manually or just return keyed array
        $formatted = [];
        foreach ($settings as $setting) {
             $formatted[$setting->key] = [
                 'value' => $setting->value,
                 'group' => $setting->group,
                 'type' => $setting->type,
                 'label' => $setting->label,
             ];
             
             // Append full URL for logo/images
             if ($setting->type === 'image' && $setting->value) {
                 $formatted[$setting->key]['url'] = asset('uploads/' . $setting->value);
             }
        }

        return response()->json(['settings' => $formatted]);
    }

    public function update(Request $request)
    {
        $tenantId = $request->user()->tenant_id;
        $inputs = $request->all();

        foreach ($inputs as $key => $value) {
            // Check if key exists in settings table
            $exists = \Illuminate\Support\Facades\DB::connection('tenant')->table('settings')->where('key', $key)->exists();
            
            if ($exists) {
                $newValue = $value;

                // Handle File Upload
                if ($request->hasFile($key)) {
                    $file = $request->file($key);
                    $path = 'tenants/' . $tenantId . '/uploads';
                    $filename = uniqid() . '_' . $file->getClientOriginalName();
                    
                    // Move file
                    $file->move(public_path('uploads/' . $path), $filename);
                    
                    // Store relative path
                    $newValue = $path . '/' . $filename;
                }
                
                \Illuminate\Support\Facades\DB::connection('tenant')->table('settings')
                    ->where('key', $key)
                    ->update(['value' => $newValue, 'updated_at' => now()]);
            }
        }

        return response()->json(['message' => 'Ayarlar güncellendi.']);
    }
}
