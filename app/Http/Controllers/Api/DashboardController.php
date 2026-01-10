<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Proposal;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        // Total Customers (Active)
        $totalCustomers = Customer::where('status', 'active')->count();

        // Total Products (Active)
        $totalProducts = Product::where('status', 'active')->count();

        // Pending Proposals
        $pendingProposals = Proposal::where('status', 'pending')->count();

        // Monthly Volume (Approved proposals this month)
        $startOfMonth = now()->startOfMonth();
        $monthlyVolume = Proposal::where('status', 'approved')
            ->where('proposal_date', '>=', $startOfMonth)
            ->sum('total_amount');

        // Recent Proposals
        $recentProposals = Proposal::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($proposal) {
                return [
                    'id' => $proposal->id,
                    'customer' => $proposal->customer ? $proposal->customer->company_name : 'Bilinmeyen Müşteri',
                    'title' => $proposal->title,
                    'proposal_number' => $proposal->proposal_number,
                    'amount' => $proposal->total_amount,
                    'currency' => $proposal->currency,
                    'status' => $proposal->status,
                    'date' => $proposal->proposal_date->format('d.m.Y'),
                    'source' => $proposal->source ?? 'web', // Default to web if null
                ];
            });

        return response()->json([
            'total_customers' => $totalCustomers,
            'total_products' => $totalProducts,
            'pending_proposals' => $pendingProposals,
            'monthly_volume' => $monthlyVolume,
            'recent_proposals' => $recentProposals,
            'currency' => '₺' // Assuming currency is static or can be dynamic later
        ]);
    }
}
