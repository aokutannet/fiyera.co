<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'position' => $user->position,
            'bio' => $user->bio,
            'two_factor_enabled' => (bool)$user->two_factor_enabled,
            'tenant' => $user->tenant ? [
                'id' => $user->tenant->id,
                'name' => $user->tenant->name,
            ] : null,
            'avatar' => 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0f172a&color=fff&size=128',
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:500'],
            'two_factor_enabled' => ['boolean'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->forceFill([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? $user->phone,
            'position' => $validated['position'] ?? $user->position,
            'bio' => $validated['bio'] ?? $user->bio,
            'two_factor_enabled' => $validated['two_factor_enabled'] ?? $user->two_factor_enabled,
        ]);

        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'subject_type' => get_class($user),
            'subject_id' => $user->id,
            'event' => 'updated',
            'description' => 'Profil bilgileri güncellendi (Mobil).',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'properties' => ['source' => 'mobile_api'],
        ]);

        return response()->json([
            'message' => 'Profil başarıyla güncellendi.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'position' => $user->position,
                'bio' => $user->bio,
                'two_factor_enabled' => (bool)$user->two_factor_enabled,
            ]
        ]);
    }
}
