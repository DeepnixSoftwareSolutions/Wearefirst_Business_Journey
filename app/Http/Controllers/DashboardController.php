<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Define the current "Payout Week" (Last Friday Midnight to This Friday Midnight)
        // If today is Friday or later in the weekend, the start is this past Friday.
        // Otherwise, it's the Friday of the previous week.
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::SATURDAY); 
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::FRIDAY);

        // 1. Calculate Agent Rankings (Weekly)
        $rankings = $this->getWeeklyRankings($startOfWeek, $endOfWeek);

        // 2. Base response for all users
        $response = [
            'my_profile' => $user,
            'top_earners' => $rankings['earners'],
            'top_recruiters' => $rankings['recruiters'],
        ];

        // 3. Append Admin/Manager/Accountant Analytics
        if (in_array($user->role, ['Admin', 'Manager', 'Accountant'])) {
            $response['analytics'] = $this->getAdminAnalytics($startOfWeek, $endOfWeek);
        }

        // --- Universal User Analytics (Recruits & Earnings Breakdown) ---
        if ($user->role !== 'Accountant') {
            // Active students registered by this user this week
            $weeklyRecruits = User::where('registered_by_agent_id', $user->id)
                ->where('admission_status', 'Active')
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->count();

            // Total lifetime active students registered by this user
            $lifetimeRecruits = User::where('registered_by_agent_id', $user->id)
                ->where('admission_status', 'Active')
                ->count();

            // Weekly Earnings Breakdown
            $weeklyDirect = Transaction::where('user_id', $user->id)
                ->where('type', 'Direct Referral')
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->sum('amount');

            $weeklyPair = Transaction::where('user_id', $user->id)
                ->where('type', 'Pair Bonus')
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->sum('amount');

            // Lifetime Earnings Breakdown
            $lifetimeDirect = Transaction::where('user_id', $user->id)
                ->where('type', 'Direct Referral')
                ->sum('amount');

            $lifetimePair = Transaction::where('user_id', $user->id)
                ->where('type', 'Pair Bonus')
                ->sum('amount');

            $response['my_stats'] = [
                'weekly_recruits' => $weeklyRecruits,
                'lifetime_recruits' => $lifetimeRecruits,
                'weekly_direct' => $weeklyDirect,
                'weekly_pair' => $weeklyPair,
                'lifetime_direct' => $lifetimeDirect,
                'lifetime_pair' => $lifetimePair,
            ];
        }

        return Inertia::render('Dashboard', [
            'dashboardData' => $response
        ]);
    }

    /**
     * Calculates the Top 10 Earners and Top 10 Recruiters for the current week.
     */
    private function getWeeklyRankings($start, $end)
    {
        // Top 10 Earners: Sum of transactions this week
        $topEarners = Transaction::select('user_id', DB::raw('SUM(amount) as weekly_income'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('user_id')
            ->orderByDesc('weekly_income')
            ->limit(10)
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'badge', 'profile_image_path', 'special_offers');
            }])
            ->get();

        // Top 10 Recruiters: Count of active students registered by the agent this week
        $topRecruiters = User::select('registered_by_agent_id', DB::raw('COUNT(*) as weekly_referrals'))
            ->whereNotNull('registered_by_agent_id')
            ->where('admission_status', 'Active') // Only count approved admissions
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('registered_by_agent_id')
            ->orderByDesc('weekly_referrals')
            ->limit(10)
            ->with(['sponsor' => function($query) {
                $query->select('id', 'name', 'badge', 'profile_image_path', 'special_offers');
            }])
            ->get();

        return [
            'earners' => $topEarners,
            'recruiters' => $topRecruiters
        ];
    }

    /**
     * Calculates the Company-wide Financials for the Admin Dashboard.
     */
    private function getAdminAnalytics($start, $end)
    {
        // Revenue: Sum of all admission fees paid this week
        $weeklyRevenue = User::where('admission_status', 'Active')
            ->whereBetween('created_at', [$start, $end])
            ->sum('admission_fee_paid');

        // Identify the Admin so we don't count their "earnings" as an expense
        $adminId = User::where('role', 'Admin')->value('id');

        // Expenses: Sum of all commissions GENERATED by agents this week (Liabilities)
        $weeklyExpenses = Transaction::whereIn('type', ['Direct Referral', 'Pair Bonus'])
            ->where('user_id', '!=', $adminId)
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $totalUsers = User::count();
        $activeAgents = User::where('role', 'Agent')->count();

        return [
            'period' => $start->format('M d') . ' - ' . $end->format('M d'),
            'total_users' => $totalUsers,
            'active_agents' => $activeAgents,
            'weekly_revenue' => $weeklyRevenue,
            'weekly_expenses' => $weeklyExpenses,
            'weekly_profit' => $weeklyRevenue - $weeklyExpenses,
        ];
    }
}