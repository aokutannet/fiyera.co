<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\User;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->user()->tenant;
        $plan = $tenant->plan;
        
        // Subscription & Limits
        $remainingTrialDays = 0;
        if ($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture()) {
            $remainingTrialDays = $tenant->trial_ends_at->diffInDays(now()) + 1;
        }

        $limits = $plan->limits ?? [];
        
        // Current Usage
        $usage = [
            'proposals' => [
                'used' => Proposal::where('tenant_id', $tenant->id)->count(),
                'limit' => $limits['proposal_monthly'] ?? 0,
                'label' => 'Aylık Teklif'
            ],
            'users' => [
                'used' => User::where('tenant_id', $tenant->id)->count(),
                'limit' => $limits['user_count'] ?? 0,
                'label' => 'Kullanıcı Sayısı'
            ]
        ];

        // Payment History
        $history = \App\Models\SubscriptionAttempt::where('tenant_id', $tenant->id)
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($attempt) {
                return [
                    'plan_name' => $attempt->plan->name ?? 'Bilinmiyor',
                    'amount' => $attempt->price,
                    'period' => $attempt->billing_period,
                    'status' => $attempt->status,
                    'date' => $attempt->created_at->format('d.m.Y H:i'),
                ];
            });

        return response()->json([
            'plan_name' => $plan ? $plan->name : 'Paket Yok',
            'on_trial' => $remainingTrialDays > 0,
            'trial_days_left' => $remainingTrialDays,
            'trial_end_date' => $tenant->trial_ends_at ? $tenant->trial_ends_at->format('d.m.Y') : null,
            'renewal_date' => $tenant->trial_ends_at ? $tenant->trial_ends_at->format('d.m.Y') : null,
            'usage' => $usage,
            'history' => $history
        ]);
    }
}
