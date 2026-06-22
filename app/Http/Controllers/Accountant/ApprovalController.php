<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Node;
use App\Models\Transaction;
use App\Services\ReferralEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Mail\PaymentInvoiceMail;
use Illuminate\Support\Facades\Mail;

class ApprovalController extends Controller
{
    protected $referralEngine;

    public function __construct(ReferralEngine $referralEngine)
    {
        $this->referralEngine = $referralEngine;
    }

    /**
     * Display the list of pending students for the Accountant (Inertia Vue)
     */
    public function index()
    {
        $pendingStudents = User::where('admission_status', 'Pending Approval')
            ->with(['sponsor:id,name,email', 'intendedParentNode.user'])
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('Accountant/PendingApprovals', [
            'pendingStudents' => $pendingStudents
        ]);
    }

    /**
     * Process the approval, place on tree, pay commission, and inject points
     */
    public function approveRegistration(Request $request, User $student)
    {
        if ($student->admission_status !== 'Pending Approval') {
            return back()->withErrors(['error' => 'Student is not pending approval.']);
        }

        DB::beginTransaction();

        try {
            // 1. Verify intended placement is still available (Security Check)
            $nodeTaken = Node::where('parent_node_id', $student->intended_parent_node_id)
                             ->where('position', $student->intended_position)
                             ->exists();

            if ($nodeTaken) {
                DB::rollBack();
                // Instead of crashing, softly reject it back to the agent with an explicit error
                $student->update([
                    'admission_status' => 'Rejected',
                    'admission_fee_paid' => 0,
                    'payment_transaction_id' => null
                ]);
                return back()->withErrors(['error' => 'Placement node was taken by another registration. Payment rejected. Please instruct the agent to void the registration and re-register.']);
            }

            // 2. Mark as Active
            $student->update([
                'admission_status' => 'Active'
            ]);

            // 3. Create the Student's 3 Nodes
            // Main Node (placed under the intended parent)
            $mainNode = Node::create([
                'user_id' => $student->id,
                'parent_node_id' => $student->intended_parent_node_id,
                'position' => $student->intended_position,
                'account_type' => 'Main'
            ]);

            // Sub A (Left of Main)
            Node::create([
                'user_id' => $student->id,
                'parent_node_id' => $mainNode->id,
                'position' => 'Left',
                'account_type' => 'Sub_A'
            ]);

            // Sub B (Right of Main)
            Node::create([
                'user_id' => $student->id,
                'parent_node_id' => $mainNode->id,
                'position' => 'Right',
                'account_type' => 'Sub_B'
            ]);

            // 4. Pay the Rs. 2000 Direct Commission to the Sponsor
            $sponsor = User::find($student->registered_by_agent_id);
            if ($sponsor) {
                $sponsor->pending_payout_balance += 2000;
                $sponsor->total_historical_earned += 2000;
                $sponsor->save();

                Transaction::create([
                    'user_id' => $sponsor->id,
                    'node_id' => null, // Direct commission, not tied to a specific node balance
                    'type' => 'Direct Referral',
                    'amount' => 2000,
                    'description' => "Direct Referral Commission for registering Student: {$student->name}"
                ]);
            }

            // 5. Inject 1 Point up the tree ONLY IF Full Payment is made
            if ($student->admission_fee_paid >= 15000) {
                $parentNode = Node::find($student->intended_parent_node_id);
                if ($parentNode) {
                    $this->referralEngine->injectPoint($parentNode, $student->intended_position);
                }
            }
            
            DB::commit();

            // Send Invoice & Confirmation Email
            Mail::to($student->email)->send(new PaymentInvoiceMail($student));
            
            return back()->with('success', 'Student approved! Nodes generated and direct commission paid.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'A critical error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark the remainder of the admission fee as paid, inject the deferred point, and send final invoice
     */
    public function markRemainderPaid(User $student)
    {
        // 1. Only allow if currently partially paid
        if ($student->admission_fee_paid >= 15000) {
            return back()->withErrors(['error' => 'Student has already paid in full.']);
        }

        DB::beginTransaction();

        try {
            // Clean up the transaction ID for the database
            $cleanTrxId = str_replace(' (BALANCE)', '', $student->payment_transaction_id);

            // 2. Update the amount, reset status to Active, and clean the Trx ID
            $student->update([
                'admission_fee_paid' => 15000,
                'admission_status' => 'Active',
                'payment_transaction_id' => $cleanTrxId
            ]);

            // 3. Inject the deferred point!
            // We use the 'mainNode' relationship to find exactly where they sit on the tree
            $mainNode = $student->mainNode;
            if ($mainNode && $mainNode->parent_node_id) {
                $parentNode = Node::find($mainNode->parent_node_id);
                if ($parentNode) {
                    $this->referralEngine->injectPoint($parentNode, $mainNode->position);
                }
            }

            DB::commit();
            
            // 4. Send final invoice email (true flags it as a Remainder payment)
            Mail::to($student->email)->send(new PaymentInvoiceMail($student, true));
            
            return back()->with('success', "Full payment confirmed for {$student->name}. Deferred point injected into the network!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to process remainder: ' . $e->getMessage()]);
        }
    }

    /**
     * Soft Reject a bad payment (Handles both Initial and Balance rejections correctly)
     */
    public function rejectRegistration(Request $request, User $student)
    {
        if ($student->admission_status !== 'Pending Approval') {
            return back()->withErrors(['error' => 'Student is not pending approval.']);
        }

        // 1. REJECTING A BALANCE PAYMENT
        // If the transaction ID has the balance tag, we only reject the final 5k.
        if (str_contains($student->payment_transaction_id, '(BALANCE)')) {
            $student->update([
                'admission_status' => 'Active', // Move them back to the active tree state
                'payment_transaction_id' => 'Previous Trx Verified. Awaiting Balance.' // Wipe the bad Trx ID, keep the 10k fee intact!
            ]);
            return back()->with('success', 'Balance payment rejected. The agent has been notified to resubmit.');
        }

        // 2. REJECTING AN INITIAL PAYMENT
        // If it was the first payment, wipe everything so the agent starts over.
        $student->update([
            'admission_status' => 'Rejected',
            'admission_fee_paid' => 0,
            'payment_transaction_id' => null
        ]);

        return back()->with('success', 'Initial registration payment rejected. The agent has been notified.');
    }

    /**
     * Hard Delete a pending registration and free up the tree node
     */
    public function voidRegistration(User $student)
    {
        if (!in_array($student->admission_status, ['Pending Payment', 'Pending Approval', 'Rejected'])) {
            return back()->withErrors(['error' => 'Active students cannot be voided.']);
        }

        $student->delete();

        return back()->with('success', 'Registration permanently voided. The tree slot is now available.');
    }
}