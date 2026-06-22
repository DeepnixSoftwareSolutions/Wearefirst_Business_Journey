<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SalaryRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PayoutController extends Controller
{
    public function getEligibleAgents(Request $request)
    {
        $query = User::whereIn('role', ['Agent', 'Manager'])
                     ->where('pending_payout_balance', '>', 0)
                     ->where('is_held', false)
                     ->whereNotIn('id', function($q) {
                         $q->select('user_id')
                           ->from('salary_requests')
                           ->whereIn('status', ['Pending Admin Approval', 'Approved by Admin']);
                     });

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nic', 'like', '%' . $search . '%');
                if (is_numeric($search)) {
                    $q->orWhere('member_id', (int) $search);
                }
            });
        }

        $eligibleAgents = $query->get(['id', 'name', 'nic', 'phone', 'badge', 'pending_payout_balance']);
        $totalLiability = $eligibleAgents->sum('pending_payout_balance');

        $pendingRequests = SalaryRequest::with('user:id,name,nic,badge')
                                        ->where('status', 'Pending Admin Approval')
                                        ->orderBy('created_at', 'desc')
                                        ->get();

        return Inertia::render('Accountant/Payouts', [
            'eligibleAgents' => $eligibleAgents,
            'totalLiability' => $totalLiability,
            'pendingRequests' => $pendingRequests,
            'filters' => $request->only('search')
        ]);
    }

    public function submitRequests(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $accountantId = Auth::id();
        
        DB::transaction(function () use ($request, $accountantId) {
            $agents = User::whereIn('id', $request->user_ids)->get();

            foreach ($agents as $agent) {
                if ($agent->pending_payout_balance > 0) {
                    
                    // 1. Capture the exact amount to lock in escrow
                    $lockedAmount = $agent->pending_payout_balance;

                    // 2. Create the Escrow Request
                    SalaryRequest::create([
                        'user_id' => $agent->id,
                        'amount' => $lockedAmount,
                        'status' => 'Pending Admin Approval',
                        'requested_by' => $accountantId
                    ]);

                    // 3. ESCROW : Deduct it immediately so it can't be double-spent
                    $agent->pending_payout_balance -= $lockedAmount;
                    $agent->save();
                }
            }
        });

        return back()->with('success', 'Salary requests submitted to Admin. Agent balances successfully locked in escrow.');
    }

    public function getApprovedPayouts(Request $request)
    {
        $approvedRequests = SalaryRequest::with('user:id,name,nic,phone,badge,bank_details')
                                         ->where('status', 'Approved by Admin')
                                         ->orderBy('updated_at', 'desc')
                                         ->get();

        return Inertia::render('Accountant/DisbursePayments', [
            'approvedRequests' => $approvedRequests
        ]);
    }

    public function markAsPaid(Request $request)
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:salary_requests,id',
        ]);

        DB::transaction(function () use ($request) {
            $requests = SalaryRequest::whereIn('id', $request->request_ids)
                                     ->where('status', 'Approved by Admin')
                                     ->with('user')
                                     ->get();

            foreach ($requests as $salaryReq) {
                $user = $salaryReq->user;

                // 1. Mark Request as Paid
                $salaryReq->update(['status' => 'Paid (Bank Transfer Complete)']);

                // 2. Log Master Transaction
                // The money was already deducted during the 'submitRequests' phase,
                // so now we just generate the final receipt for the ledger.
                Transaction::create([
                    'user_id' => $user->id,
                    'node_id' => null,
                    'type' => 'Payout Withdrawal',
                    'amount' => -$salaryReq->amount,
                    'description' => "Weekly Salary Disbursed to Bank Account"
                ]);
            }
        });

        return back()->with('success', 'Selected payments marked as Paid & ledger updated.');
    }
}