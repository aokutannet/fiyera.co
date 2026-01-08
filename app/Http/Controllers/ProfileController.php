<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('tenant.profile.edit', [
            'user' => auth()->user()->load('tenant'),
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        // Tenant bağlantısını ayarla (ActivityLog için gerekli)
        if ($user->tenant_id) {
            $dbName = "tenant_{$user->tenant_id}_teklif";
            \Illuminate\Support\Facades\Config::set("database.connections.tenant.database", $dbName);
            \Illuminate\Support\Facades\DB::purge('tenant');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['two_factor_enabled'] = $request->has('two_factor_enabled');

        $user->update($validated);

        if ($request->filled('current_password')) {
            $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);

            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Tenant veritabanını güncelle
        $updateData = [
            'name' => $user->name,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = $user->password;
        }

        \Illuminate\Support\Facades\DB::connection('tenant')->table('users')
            ->where('email', $user->email)
            ->update($updateData);

        return back()->with('success', 'Profiliniz başarıyla güncellendi.');
    }
}
