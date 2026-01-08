<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        Log::info('Google Callback Initiated');
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            Log::info('Google User Retrieved', ['email' => $googleUser->email, 'id' => $googleUser->id]);
        } catch (\Exception $e) {
            Log::error('Google Login Error (Stateless): ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['email' => 'Google ile giriş yapılırken bir hata oluştu: ' . $e->getMessage()]);
        }

        // 1. Check if user exists by google_id
        $user = User::where('google_id', $googleUser->id)->first();

        if ($user) {
            Log::info('User found by Google ID, logging in.', ['user_id' => $user->id]);
            Auth::login($user);
            return redirect()->intended(route('dashboard'));
        }

        // 2. Check if user exists by email (link account)
        $user = User::where('email', $googleUser->email)->first();

        if ($user) {
            Log::info('User found by email, linking account.', ['user_id' => $user->id]);
            $user->update([
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
            ]);
            Auth::login($user);
            return redirect()->intended(route('dashboard'));
        }

        // 3. Register new user
        Log::info('Registering new user via Google.');
        return $this->registerNewUser($googleUser);
    }

    /**
     * Register a new user from Google data (Logic replicated from RegisterController).
     *
     * @param \Laravel\Socialite\Contracts\User $googleUser
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function registerNewUser($googleUser)
    {
        try {
            DB::beginTransaction();

            $companyName = $googleUser->name . "'Teklif Platformu";


            $tenant = Tenant::create([
                'name' => $companyName,
                'status' => 'active',
            ]);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'password' => Hash::make(Str::random(16)), // Random password
                'is_owner' => true,
                'status' => 'active',
                'email_verified_at' => now(), // Auto verify email from Google
            ]);

            DB::commit();

            // Run Tenant Migrations
            \Illuminate\Support\Facades\Artisan::call('app:migrate-tenant', [
                'tenant' => $tenant->id
            ]);

            // Copy User to Tenant DB
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

            return redirect()->route('onboarding.index');

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Google Registration Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['email' => 'Kayıt sırasında bir hata oluştu: ' . $e->getMessage()]);
        }
    }

}
