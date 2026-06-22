<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    public function index()
    {
        $last7Days = collect(range(6, 0))->map(fn ($i) => Carbon::now()->subDays($i)->format('Y-m-d'));
        
        $adminId = User::where('role', 'Admin')->value('id');

        // Revenue: Admission fees
        $revenueData = $last7Days->map(fn ($date) => User::whereDate('created_at', $date)->sum('admission_fee_paid'));

        // Expenses: Generated Commissions (Liabilities) by Non-Admins
        $expenseData = $last7Days->map(fn ($date) => Transaction::whereIn('type', ['Direct Referral', 'Pair Bonus'])
            ->where('user_id', '!=', $adminId)
            ->whereDate('created_at', $date)
            ->sum('amount')
        );

        // All-time aggregates for the top tiles
        $totalRevenue = User::sum('admission_fee_paid');
        $totalLiabilities = Transaction::whereIn('type', ['Direct Referral', 'Pair Bonus'])
            ->where('user_id', '!=', $adminId)
            ->sum('amount');

        return Inertia::render('Admin/Analytics', [
            'revenueData' => $revenueData,
            'expenseData' => $expenseData,
            'labels' => $last7Days,
            'stats' => [
                'total_users' => User::count(),
                'active_agents' => User::where('role', 'Agent')->count(),
                'total_revenue' => $totalRevenue,
                'total_expenses' => $totalLiabilities,
                'gross_profit' => $totalRevenue - $totalLiabilities
            ]
        ]);
    }
}