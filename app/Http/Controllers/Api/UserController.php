<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('tenant_id', $request->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'position' => $user->position,
                    'status' => $user->status,
                    'is_owner' => (bool)$user->is_owner,
                    'permissions' => $user->permissions,
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=' . ($user->status === 'passive' ? 'e2e8f0' : 'f1f5f9') . '&color=64748b',
                ];
            });

        return response()->json(['data' => $users]);
    }

    public function store(Request $request)
    {
        $tenantinfo = $request->user()->tenant;
        
        // Check Limits
        $limit = $tenantinfo->plan->limits['user_count'] ?? 1;
        $currentCount = User::where('tenant_id', $tenantinfo->id)->count();

        if ($limit != -1 && $currentCount >= $limit) {
            return response()->json(['message' => 'Kullanıcı limitine ulaşıldı. Lütfen paketinizi yükseltin.'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'permissions' => ['array'],
            'position' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'tenant_id' => $tenantinfo->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'permissions' => $validated['permissions'] ?? [],
            'position' => $validated['position'] ?? null,
            'is_owner' => false,
            'status' => 'active',
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'subject_type' => get_class($user),
            'subject_id' => $user->id,
            'event' => 'created',
            'description' => $user->name . ' isimli kullanıcı oluşturuldu (Mobil).',
            'properties' => ['source' => 'mobile_api'],
        ]);

        return response()->json(['message' => 'Kullanıcı başarıyla oluşturuldu.', 'user' => $user], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'permissions' => ['array'],
            'position' => ['nullable', 'string'],
        ]);

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'permissions' => $validated['permissions'] ?? $user->permissions,
            'position' => $validated['position'] ?? $user->position,
        ]);

        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'subject_type' => get_class($user),
            'subject_id' => $user->id,
            'event' => 'updated',
            'description' => $user->name . ' kullanıcısı güncellendi (Mobil).',
            'properties' => ['source' => 'mobile_api'],
        ]);

        return response()->json(['message' => 'Kullanıcı güncellendi.', 'user' => $user]);
    }

    public function destroy(Request $request, $id)
    {
        $user = User::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);

        if ($user->is_owner) {
            return response()->json(['message' => 'Ana yönetici silinemez.'], 403);
        }

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Kendinizi silemezsiniz.'], 403);
        }

        $user->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'subject_type' => get_class($user),
            'subject_id' => $user->id,
            'event' => 'deleted',
            'description' => $user->name . ' kullanıcısı silindi (Mobil).',
            'properties' => ['source' => 'mobile_api'],
        ]);

        return response()->json(['message' => 'Kullanıcı silindi.']);
    }

    public function toggleStatus(Request $request, $id)
    {
        $user = User::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);

        if ($user->is_owner) {
             return response()->json(['message' => 'Ana yönetici pasife alınamaz.'], 403);
        }

        $user->status = $user->status === 'active' ? 'passive' : 'active';
        $user->save();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'subject_type' => get_class($user),
            'subject_id' => $user->id,
            'event' => 'updated',
            'description' => $user->name . ' kullanıcısı ' . ($user->status == 'active' ? 'aktif edildi' : 'pasife alındı') . ' (Mobil).',
            'properties' => ['source' => 'mobile_api'],
        ]);

        return response()->json(['message' => 'Kullanıcı durumu güncellendi.', 'status' => $user->status]);
    }
}
