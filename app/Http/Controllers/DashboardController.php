<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\Proposal;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tenant = $user->tenant;
        
        // Metrics
        $totalProposals = Proposal::count();
        $pendingProposals = Proposal::where('status', 'pending')->count();
        $approvedProposalsCount = Proposal::where('status', 'approved')->count();
        
        $acceptanceRate = $totalProposals > 0 
            ? ($approvedProposalsCount / $totalProposals) * 100 
            : 0;

        $startOfMonth = now()->startOfMonth();
        $monthlyVolume = Proposal::where('status', 'approved')
            ->where('proposal_date', '>=', $startOfMonth)
            ->sum('total_amount');
            
        // New Business Overview Metrics
        $totalCustomers = \App\Models\Customer::where('status', 'active')->count();
        $totalProducts = \App\Models\Product::where('status', 'active')->count();

        // Optional: Simple mock trends or previous month comparison could be added here
        // For now, static trends in view or simple logical ones
        
        $recentProposals = Proposal::with('customer')
            ->latest()
            ->take(5)
            ->get();

        $revenueExpectation = Proposal::where('status', 'pending')->sum('total_amount');

        $recentActivities = \App\Models\ProposalActivity::with(['user', 'proposal'])
            ->latest()
            ->take(5)
            ->get();

        // Subscription & Limits
        $remainingTrialDays = null;
        if ($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture()) {
            $remainingTrialDays = $tenant->trial_ends_at->diffInDays(now()) + 1; // +1 to include today
        }
        
        // Limits
        $planLimits = $tenant->plan->limits ?? [];
        $usage = [
            'proposals' => [
                'used' => $totalProposals,
                'limit' => $planLimits['proposal_monthly'] ?? 0,
            ],
            'users' => [
                'used' => \App\Models\User::count(),
                'limit' => $planLimits['user_count'] ?? 0,
            ]
        ];

        return view('tenant.dashboard', compact(
            'user', 
            'tenant',
            'totalProposals',
            'pendingProposals',
            'acceptanceRate',
            'monthlyVolume',
            'recentProposals',
            'revenueExpectation',
            'recentActivities',
            'totalCustomers',
            'totalProducts',
            'remainingTrialDays',
            'usage'
        ));
    }
}
