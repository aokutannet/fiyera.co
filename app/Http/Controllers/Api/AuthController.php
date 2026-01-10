<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $tenant = \App\Models\Tenant::create([
                'name' => $request->company_name,
                'status' => 'active',
            ]);

            $user = User::withoutEvents(function () use ($tenant, $request) {
                return User::create([
                    'tenant_id' => $tenant->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'is_owner' => true,
                    'status' => 'active',
                ]);
            });

            \Illuminate\Support\Facades\DB::commit();

            // Run Migrations
            \Illuminate\Support\Facades\Artisan::call('app:migrate-tenant', [
                'tenant' => $tenant->id
            ]);

            // Add User to Tenant DB
            $dbName = "tenant_{$tenant->id}_teklif";
            config([
                'database.connections.tenant_temp.database' => $dbName,
                'database.connections.tenant_temp.username' => config('database.connections.tenant.username', config('database.connections.mysql.username')),
                'database.connections.tenant_temp.password' => config('database.connections.tenant.password', config('database.connections.mysql.password')),
                'database.connections.tenant_temp.driver' => 'mysql',
                'database.connections.tenant_temp.host' => '127.0.0.1',
                'database.connections.tenant_temp.port' => '3306',
            ]);

            \Illuminate\Support\Facades\DB::connection('tenant_temp')->table('users')->insert([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'is_owner' => true,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'tenant' => $tenant,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['message' => 'Kayıt sırasında bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Giriş bilgileri hatalı.'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Configure tenant connection if needed for logging or other scopes
        $this->configureTenantConnection($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Log login activity
        try {
            ActivityLog::create([
                'user_id' => $user->id,
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'event' => 'login',
                'description' => 'Mobil uygulama üzerinden giriş yapıldı.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'properties' => ['source' => 'mobile_api'],
            ]);
        } catch (\Exception $e) {
            // Logging failure shouldn't stop login
            \Illuminate\Support\Facades\Log::error("Failed to log API login: " . $e->getMessage());
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    private function configureTenantConnection($user)
    {
        if ($user && $user->tenant) {
            $dbName = "tenant_{$user->tenant->id}_teklif";
            
            // Log for debugging
            \Illuminate\Support\Facades\Log::info("Configuring tenant DB for User {$user->id}: {$dbName}");
            
            config([
                'database.connections.tenant.database' => $dbName
            ]);
            
            try {
                \Illuminate\Support\Facades\DB::purge('tenant');
                \Illuminate\Support\Facades\DB::reconnect('tenant');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to reconnect tenant DB: " . $e->getMessage());
            }
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        
        // Log logout activity
        try {
            // Need to ensure tenant connection is set if we want to log to tenant DB
            // But logout might happen without full context setup in some flows.
            // Assuming middleware handles tenant setup, or we might need to set it manually if missing.
            if ($user->tenant) {
                 $this->configureTenantConnection($user);
                 
                 ActivityLog::create([
                    'user_id' => $user->id,
                    'subject_type' => User::class,
                    'subject_id' => $user->id,
                    'event' => 'logout',
                    'description' => 'Mobil uygulama üzerinden çıkış yapıldı.',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'properties' => ['source' => 'mobile_api'],
                ]);
            }
        } catch (\Exception $e) {
             \Illuminate\Support\Facades\Log::error("Failed to log API logout: " . $e->getMessage());
        }

        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Başarıyla çıkış yapıldı.']);
    }
}
