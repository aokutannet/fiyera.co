<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $tenant = $user->tenant;
            
            // Kullanıcı bir tenant'a sahip değilse çıkış yaptır
            if (!$tenant) {
                auth()->logout();
                return redirect()->route('login')->withErrors(['email' => 'Hesabınız bir firmaya bağlı değil.']);
            }
            
            // Not: Status kontrolleri CheckSubscription middleware'inde yapılıyor.
            // Burada sadece tenant bağlantısını kuruyoruz.
            
            // Tenant veritabanını ayarla
            $dbName = "tenant_{$tenant->id}_teklif";

            // Check if database exists
            try {
                // We use the main connection to check for the schema existence
                $dbExists = \Illuminate\Support\Facades\DB::connection('mysql')->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$dbName]);
                
                if (empty($dbExists)) {
                    // Database gone but user exists -> Inconsistent state (likely deleted)
                    auth()->logout();
                    return redirect()->route('login')->withErrors(['email' => 'Firma veritabanı bulunamadı. Hesabınız silinmiş olabilir.']);
                }
            } catch (\Exception $e) {
                // If check fails, safe to fail open or log out? Log out is safer
                \Illuminate\Support\Facades\Log::error('Tenant DB check failed: ' . $e->getMessage());
            }
            
            // Yapılandırmayı set et
            config([
                'database.connections.tenant.database' => $dbName
            ]);

            // Veritabanı bağlantısını temizle (her istekte taze yapılandırma için)
            \Illuminate\Support\Facades\DB::purge('tenant');

            // Singleton olarak tenant'ı set et
            app()->instance('currentTenant', $tenant);
        }

        return $next($request);
    }
}
