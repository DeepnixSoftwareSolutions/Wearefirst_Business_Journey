<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PaymentReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $baseQuery = User::where('role', 'Student')->with('sponsor:id,name');

        if ($user->role === 'Agent') {
            $baseQuery->where('registered_by_agent_id', $user->id);
        }

        // 1. Initial Payments Needed
        $initialPayments = (clone $baseQuery)
            ->whereIn('admission_status', ['Pending Payment', 'Rejected'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Balance Payments Needed (Active, but < 15000)
        $balancePayments = (clone $baseQuery)
            ->where('admission_status', 'Active')
            ->where('admission_fee_paid', '>', 0)
            ->where('admission_fee_paid', '<', 15000)
            ->orderBy('created_at', 'desc')
            ->get();

        $formatter = function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'sponsor_name' => $student->sponsor ? $student->sponsor->name : 'N/A',
                'paid' => $student->admission_fee_paid,
                'owed' => 15000 - $student->admission_fee_paid,
                'status' => $student->admission_status,
                'registration_date' => $student->created_at->format('M d, Y')
            ];
        };

        return Inertia::render('Finance/DuePayments', [
            'initialPayments' => $initialPayments->map($formatter),
            'balancePayments' => $balancePayments->map($formatter),
        ]);
    }

    public function submitPaymentDetails(Request $request, User $student)
    {
        $request->validate([
            'amount' => 'required|numeric|in:5000,10000,15000',
            'transaction_id' => 'required|string|max:255'
        ]);

        // 1. Handling INITIAL Payments
        if (in_array($student->admission_status, ['Pending Payment', 'Rejected'])) {
            
            // Verify the spot hasn't been stolen by an Active Node!
            $nodeTaken = \App\Models\Node::where('parent_node_id', $student->intended_parent_node_id)
                             ->where('position', $student->intended_position)
                             ->exists();

            if ($nodeTaken) {
                return back()->withErrors(['error' => 'CRITICAL: The slot reserved for this student has been taken by another active registration. Please click "Void & Delete Slot" and register this student again to get a new placement.']);
            }

            $student->update([
                'admission_fee_paid' => $request->amount,
                'payment_transaction_id' => $request->transaction_id,
                'admission_status' => 'Pending Approval'
            ]);

            return back()->with([
                'flash_modal' => true,
                'title' => 'Initial Payment Submitted',
                'message' => "The initial payment of Rs {$request->amount} for {$student->name} has been submitted to the Finance Department. The student will be officially placed on the tree once an Accountant verifies the transaction.",
                'theme' => 'success'
            ]);
        } 
        
        // 2. Handling BALANCE Payments
        if ($student->admission_status === 'Active' && $student->admission_fee_paid < 15000) {
            $student->update([
                'payment_transaction_id' => $request->transaction_id . ' (BALANCE)',
                'admission_status' => 'Pending Approval' 
            ]);
            
            return back()->with([
                'flash_modal' => true,
                'title' => 'Balance Payment Submitted',
                'message' => "The final balance payment for {$student->name} has been submitted. The network pairing point will be injected as soon as the Accountant verifies the transaction.",
                'theme' => 'success'
            ]);
        }

        return back()->withErrors(['error' => 'Invalid payment state.']);
    }
}