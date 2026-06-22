<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LedgerController extends Controller
{
    /**
     * Display the user's personal transaction history.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Build the base query
        $query = Transaction::where('user_id', $user->id)
                            ->with('node.user:id,member_id');

        // 2. Apply filtering if 'type' is provided in the request
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 3. Fetch transactions, newest first, with pagination
        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString(); // Ensures filters stay in the URL during pagination

        // 4. Calculate lifetime stats
        // Note: These stats remain global (unfiltered) to show the user their actual account health
        $totalEarned = Transaction::where('user_id', $user->id)
            ->where('amount', '>', 0)
            ->sum('amount');

        $totalWithdrawn = Transaction::where('user_id', $user->id)
            ->where('amount', '<', 0)
            ->sum('amount');

        return Inertia::render('Agent/Ledger', [
            'transactions' => $transactions,
            'stats' => [
                'total_earned' => $totalEarned,
                'total_withdrawn' => abs($totalWithdrawn),
                'current_pending' => $user->pending_payout_balance
            ],
            // Pass the current filter back to the view
            'filters' => $request->only(['type'])
        ]);
    }
}