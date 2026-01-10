<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SyncOnboardingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-onboarding-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs onboarding answers from tenant databases to the main tenants table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sync of onboarding data...');

        $tenants = Tenant::all();
        $bar = $this->output->createProgressBar(count($tenants));

        foreach ($tenants as $tenant) {
            try {
                // Configure dynamic connection
                $dbName = "tenant_{$tenant->id}_teklif";
                Config::set("database.connections.tenant_temp.database", $dbName);
                Config::set("database.connections.tenant_temp.username", config('database.connections.mysql.username'));
                Config::set("database.connections.tenant_temp.password", config('database.connections.mysql.password'));
                Config::set("database.connections.tenant_temp.driver", 'mysql');
                Config::set("database.connections.tenant_temp.host", '127.0.0.1');
                Config::set("database.connections.tenant_temp.port", '3306');

                // Reconnect
                DB::purge('tenant_temp');

                // Check if connection works
                try {
                     DB::connection('tenant_temp')->getPdo();
                } catch (\Exception $e) {
                     // Database might not exist for some tenants or failed creation
                     continue;
                }

                // Fetch onboarding settings
                $settings = DB::connection('tenant_temp')->table('settings')
                    ->where('group', 'onboarding')
                    ->orWhere('key', 'like', 'onboarding_%')
                    ->get();
                
                if ($settings->isNotEmpty()) {
                    $data = [];
                    foreach ($settings as $setting) {
                        // Reconstruct array format used in Controller
                        $data[] = [
                            'key' => $setting->key,
                            'label' => $setting->label,
                            'value' => $setting->value
                        ];
                    }

                    // Update main tenant record
                    $tenant->onboarding_data = $data;
                    $tenant->save();
                }

            } catch (\Exception $e) {
                $this->error("Failed for Tenant #{$tenant->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Sync completed successfully.');
    }
}
