<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class ApiSetTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->tenant) {
            return response()->json(['message' => 'Kullanıcının bir firması bulunamadı.'], 403);
        }

        $dbName = "tenant_{$user->tenant->id}_teklif";
        
        // Config'i ayarla
        Config::set('database.connections.tenant.database', $dbName);
        
        // Bağlantıyı temizle ve yeniden bağlan
        DB::purge('tenant');
        
        try {
            DB::connection('tenant')->getPdo();
        } catch (\Exception $e) {
             return response()->json(['message' => 'Firma veritabanına erişilemedi.'], 500);
        }

        return $next($request);
    }
}
