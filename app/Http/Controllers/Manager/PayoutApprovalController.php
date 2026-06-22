<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\SalaryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PayoutApprovalController extends Controller
{
    public function getPendingRequests(Request $request)
    {
        $query = SalaryRequest::with(['user:id,name,badge,nic,phone', 'requestedBy:id,name'])
                              ->where('status', 'Pending Admin Approval');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nic', 'like', '%' . $search . '%');
                if (is_numeric($search)) {
                    $q->orWhere('member_id', (int) $search);
                }
            });
        }

        $pendingRequests = $query->orderBy('created_at', 'asc')->get();
        $totalAmount = $pendingRequests->sum('amount');

        return Inertia::render('Manager/PayoutApprovals', [
            'pendingRequests' => $pendingRequests,
            'totalAmount' => $totalAmount,
            'filters' => $request->only('search')
        ]);
    }

    public function approveRequests(Request $request)
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:salary_requests,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $requests = SalaryRequest::whereIn('id', $request->request_ids)
                                         ->where('status', 'Pending Admin Approval')
                                         ->get();

                foreach ($requests as $req) {
                    $req->status = 'Approved by Admin';
                    $req->approved_by = Auth::id();
                    $req->save();
                }
            });

            return back()->with('success', "Salary requests authorized successfully. Sent to Accountant for bank transfer.");

        } catch (\Exception $e) {
            Log::error('Mass Payout Approval Failed: ' . $e->getMessage(), [
                'admin_id' => Auth::id(),
                'requested_ids' => $request->request_ids
            ]);

            return back()->withErrors(['error' => 'A database error occurred while authorizing payouts. Please try again or contact IT.']);
        }
    }

    public function rejectRequests(Request $request)
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:salary_requests,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $requests = SalaryRequest::whereIn('id', $request->request_ids)
                                         ->where('status', 'Pending Admin Approval')
                                         ->with('user')
                                         ->get();

                foreach ($requests as $req) {
                    $user = $req->user;

                    // 1. REFUND THE ESCROW: Give the money back to the agent
                    $user->pending_payout_balance += $req->amount;
                    $user->save();

                    // 2. Mark the request as Dead/Rejected
                    $req->status = 'Rejected by Admin';
                    $req->approved_by = Auth::id(); // Tracks who rejected it
                    $req->save();
                }
            });

            return back()->with('success', 'Selected salary requests rejected. Funds have been refunded to the agents\' dashboards.');

        } catch (\Exception $e) {
            Log::error('Payout Rejection Failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'A critical database error occurred while rejecting payouts.']);
        }
    }
}