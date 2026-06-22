<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CompanyLedgerController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all transactions, filterable by search
        $query = Transaction::with(['user:id,name,nic,badge'])
                            ->where('type', 'Payout Withdrawal') 
                            ->orderBy('created_at', 'desc');

        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nic', 'like', '%' . $request->search . '%');
                if (is_numeric($search)) {
                    $q->orWhere('member_id', (int) $search);
                }
            });
        }

        $transactions = $query->paginate(50);
        
        // 1. Calculate All-Time Revenue
        $totalRevenue = User::sum('admission_fee_paid');

        // 2. Calculate All-Time Actual Cash Disbursed (Make it positive)
        $allTimeDisbursed = abs(Transaction::where('type', 'Payout Withdrawal')->sum('amount'));

        // 3. Live Cash (Money currently sitting in the company bank account)
        $liveCash = $totalRevenue - $allTimeDisbursed;

        // 4. Pending Liabilities (Unpaid salaries owed to agents, excluding internal staff)
        $pendingSalaries = User::whereNotIn('role', ['Admin', 'Manager', 'Accountant'])
                               ->sum('pending_payout_balance');

        return Inertia::render('Finance/CompanyLedger', [
            'transactions' => $transactions,
            'metrics' => [
                'liveCash' => $liveCash,
                'pendingSalaries' => $pendingSalaries,
                'allTimeDisbursed' => $allTimeDisbursed,
            ],
            'filters' => $request->only('search')
        ]);
    }
}