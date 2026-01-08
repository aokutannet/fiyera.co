<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-tenant {tenant?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate tenant databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        if ($tenantId) {
            $tenants = \App\Models\Tenant::where('id', $tenantId)->get();
        } else {
            $tenants = \App\Models\Tenant::all();
        }

        foreach ($tenants as $tenant) {
            $this->info("Migrating tenant: {$tenant->name} (ID: {$tenant->id})");
            
            $dbName = "tenant_{$tenant->id}_teklif";
            
            // Create database if not exists
            \Illuminate\Support\Facades\DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Set connection
            config(['database.connections.tenant.database' => $dbName]);
            \Illuminate\Support\Facades\DB::purge('tenant');
            
            $this->call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);
        }
        
        $this->info('Tenant migrations completed.');
    }
}
