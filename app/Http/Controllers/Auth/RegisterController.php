<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('tenant.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'company_name' => ['required', 'string', 'max:255'],
        ]);

        try {
            DB::beginTransaction();

            $tenant = Tenant::create([
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

            DB::commit();

            // Veritabanı oluşturma ve Migration'ları transaction dışında çalıştır
            // app:migrate-tenant komutu zaten CREATE DATABASE IF NOT EXISTS yapıyor.
            \Illuminate\Support\Facades\Artisan::call('app:migrate-tenant', [
                'tenant' => $tenant->id
            ]);

            // Sahip kullanıcıyı tenant veritabanına da ekle
            $dbName = "tenant_{$tenant->id}_teklif";
            Config::set("database.connections.tenant_temp", array_merge(
                Config::get('database.connections.mysql'),
                ['database' => $dbName]
            ));

            DB::connection('tenant_temp')->table('users')->insert([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'is_owner' => true,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Auth::login($user);

            if (!$user->google_id) {
                 $user->generateTwoFactorCode();
                 \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TwoFactorCode($user->two_factor_code));

                 return redirect()->route('verify.index'); 
            }

            return redirect()->route('onboarding.index');

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Registration Error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Kayıt sırasında bir hata oluştu: ' . $e->getMessage()])->withInput();
        }
    }
}
