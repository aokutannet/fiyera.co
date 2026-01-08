<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;

$tenants = Tenant::all();

foreach ($tenants as $tenant) {
    $trialEndDate = $tenant->created_at->copy()->addDays(14);
    $remaining = 0;
    if ($trialEndDate->isFuture()) {
        $remaining = ceil(now()->floatDiffInDays($trialEndDate));
    }
    
    echo "Tenant ID: " . $tenant->id . "\n";
    echo "Created At: " . $tenant->created_at . "\n";
    echo "Now: " . now() . "\n";
    echo "Trial End: " . $trialEndDate . "\n";
    echo "Remaining: " . $remaining . "\n";
    echo "Is Future: " . ($trialEndDate->isFuture() ? 'Yes' : 'No') . "\n";
    echo "-------------------\n";
}
