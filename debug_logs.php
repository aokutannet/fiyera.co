<?php

use Illuminate\Contracts\Console\Kernel;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// Simulate tenant context for tenant 7
$tenantId = 7;
$dbName = "tenant_{$tenantId}_teklif";
config(['database.connections.tenant.database' => $dbName]);
DB::purge('tenant');

DB::enableQueryLog();
DB::connection('tenant')->enableQueryLog();

$log = ActivityLog::latest()->first();

if ($log) {
    echo "Log ID: " . $log->id . "\n";
    echo "User ID in Log: " . var_export($log->user_id, true) . "\n";
    
    // Check user relationship
    try {
        $user = $log->user;
        echo "User from Relationship: " . ($user ? "$user->name (ID: $user->id)" : "NULL") . "\n";
    } catch (\Exception $e) {
        echo "User Relationship Error: " . $e->getMessage() . "\n";
    }
    
    print_r(DB::getQueryLog());
    print_r(DB::connection('tenant')->getQueryLog());

    // Direct User query to see if User model works
    if ($log->user_id) {
        try {
            $directUser = User::find($log->user_id);
            echo "User from Direct Query (User::find): " . ($directUser ? "$directUser->name (ID: $directUser->id)" : "NULL") . "\n";
        } catch (\Exception $e) {
            echo "Direct User Query Error: " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "No logs found in tenant database.\n";
}
