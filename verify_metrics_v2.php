<?php
try {
    $user = \App\Models\User::first();
    if (!$user) {
        throw new \Exception('No user found to test with.');
    }
    $tenant = $user->tenant;
    
    // Set tenant context
    config(['database.connections.tenant.database' => 'tenant_' . $tenant->id . '_teklif']);
    \Illuminate\Support\Facades\DB::purge('tenant');
    
    echo 'Testing with User: ' . $user->email . ' (Tenant ID: ' . $tenant->id . ')' . PHP_EOL;

    echo 'Total Proposals: ' . \App\Models\Proposal::count() . PHP_EOL;
    echo 'Pending: ' . \App\Models\Proposal::where('status', 'pending')->count() . PHP_EOL;
    echo 'Approved: ' . \App\Models\Proposal::where('status', 'approved')->count() . PHP_EOL;
    
    $startOfMonth = now()->startOfMonth();
    echo 'Monthly Volume: ' . \App\Models\Proposal::where('status', 'approved')->where('proposal_date', '>=', $startOfMonth)->sum('total_amount') . PHP_EOL;

} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
