<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'this_month');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');

        // Date Logic
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        switch ($filter) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'custom':
                if ($customStart && $customEnd) {
                    $startDate = Carbon::parse($customStart)->startOfDay();
                    $endDate = Carbon::parse($customEnd)->endOfDay();
                }
                break;
        }

        // Base Query
        $proposalsQuery = Proposal::whereBetween('proposal_date', [$startDate, $endDate]);

        // KPIs
        $totalProposals = (clone $proposalsQuery)->count();
        $totalRevenue = (clone $proposalsQuery)->where('status', 'approved')->sum('total_amount');
        
        $approvedCount = (clone $proposalsQuery)->where('status', 'approved')->count();
        $conversionRate = $totalProposals > 0 ? ($approvedCount / $totalProposals) * 100 : 0;
        
        // Status Distribution for Charts
        $statusDistribution = (clone $proposalsQuery)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // User Performance
        $userPerformance = User::where('tenant_id', auth()->user()->tenant_id)
            ->withCount(['proposals' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('proposal_date', [$startDate, $endDate]);
            }])
            ->withSum(['proposals as revenue' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'approved')->whereBetween('proposal_date', [$startDate, $endDate]);
            }], 'total_amount')
            ->get()
            ->map(function ($user) use ($startDate, $endDate) {
                $totalParams = ['proposal_date' => [$startDate, $endDate]];
                // We need manual counts for approval rate as withCount with constraints is tricky for multiple statuses
                // Let's do a separate efficient query or subquery relation if needed.
                // For now, simpler:
                $userTotal = $user->proposals_count;
                $userApproved = \App\Models\Proposal::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->whereBetween('proposal_date', [$startDate, $endDate])
                    ->count();
                
                return [
                    'name' => $user->name,
                    'avatar' => 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random',
                    'total_proposals' => $userTotal,
                    'approved_proposals' => $userApproved,
                    'revenue' => $user->revenue ?? 0,
                    'conversion_rate' => $userTotal > 0 ? ($userApproved / $userTotal) * 100 : 0,
                ];
            })
            ->sortByDesc('revenue');

        // Top Products (by Revenue)
        $topProducts = \App\Models\ProposalItem::whereHas('proposal', function($q) use ($startDate, $endDate) {
                $q->where('status', 'approved')->whereBetween('proposal_date', [$startDate, $endDate]);
            })
            ->select('product_id', 'description', DB::raw('sum(quantity) as total_qty'), DB::raw('sum(total_price) as total_revenue'))
            ->groupBy('product_id', 'description')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        // Top Customers (by Revenue)
        $topCustomers = \App\Models\Customer::withSum(['proposals as total_revenue' => function($q) use ($startDate, $endDate) {
                $q->where('status', 'approved')->whereBetween('proposal_date', [$startDate, $endDate]);
            }], 'total_amount')
            ->withCount(['proposals as approved_count' => function($q) use ($startDate, $endDate) {
                $q->where('status', 'approved')->whereBetween('proposal_date', [$startDate, $endDate]);
            }])
            ->having('total_revenue', '>', 0)
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        // Revenue Trend (Daily)
        $trendData = Proposal::where('status', 'approved')
            ->whereBetween('proposal_date', [$startDate, $endDate])
            ->selectRaw('DATE(proposal_date) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->revenue];
            });
            
        // Fill missing dates for trend chart
        $chartData = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dateKey = $current->format('Y-m-d');
            $chartData['labels'][] = $current->format('d M');
            $chartData['revenue'][] = $trendData[$dateKey] ?? 0;
            $current->addDay();
        }

        return view('tenant.reports.index', compact(
            'startDate', 
            'endDate', 
            'filter', 
            'totalProposals', 
            'totalRevenue', 
            'conversionRate', 
            'statusDistribution', 
            'userPerformance',
            'topProducts',
            'topCustomers',
            'chartData'
        ));
    }
}
