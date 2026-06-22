<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Node;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Mail\StudentWelcomeMail;
use Illuminate\Support\Facades\Mail;

class StudentRegistrationController extends Controller
{
    /**
     * Show the agent's pending registrations so they can edit typos
     */
    public function index()
    {
        $pendingStudents = User::where('registered_by_agent_id', Auth::id())
            ->whereIn('admission_status', ['Pending Payment', 'Pending Approval', 'Rejected'])
            ->orderBy('created_at', 'desc')
            ->with('intendedParentNode.user')
            ->get();

        return Inertia::render('Agent/PendingRegistrations', [
            'pendingStudents' => $pendingStudents
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('Agent/RegisterStudent', [
            'sponsor' => $request->user()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nic' => 'required|string|unique:users,nic',
            'nic_type' => 'required|in:Own,Mother,Father,Guardian',
            'phone' => 'required|string',
            'district' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'line' => 'required|in:G1,G2,G3,G4',
            // Catch Manual Tree Coordinates
            'manual_parent_id' => 'nullable|exists:nodes,id',
            'manual_position' => 'nullable|in:Left,Right',
        ]);

        $sponsor = Auth::user();

        // Security Check: Are middle legs locked?
        if (in_array($validated['line'], ['G2', 'G3']) && !$sponsor->middle_legs_unlocked) {
            return back()->withErrors(['line' => 'This line is currently locked. Please contact Admin to unlock G2 & G3.']);
        }

        $placement_id = null;
        $placement_position = null;

        // DETERMINE PLACEMENT: Manual vs Auto-Spillover
        if ($request->filled('manual_parent_id') && $request->filled('manual_position')) {
            
            // --- MANUAL TREE PLACEMENT ---
            $placement_id = $validated['manual_parent_id'];
            $placement_position = $validated['manual_position'];
            
            // Final Security Check: Ensure slot wasn't taken while the form was being filled
            $nodeTaken = Node::where('parent_node_id', $placement_id)->where('position', $placement_position)->exists();
            $nodeReserved = User::where('intended_parent_node_id', $placement_id)
                ->where('intended_position', $placement_position)
                ->whereIn('admission_status', ['Pending Payment', 'Pending Approval'])
                ->exists();
            
            if ($nodeTaken || $nodeReserved) {
                return back()->withErrors(['line' => 'This specific slot was just taken by another registration. Please return to the Network Tree and select a new empty slot.']);
            }

        } else {
            // --- AUTO-SPILLOVER PLACEMENT ---
            // Identify the starting Sub-Node and Direction based on Line
            if ($sponsor->role === 'Admin') {
                // Admin only has a Main node. Map G1/G2 to Left, G3/G4 to Right.
                $currentNode = $sponsor->mainNode;
                $position = in_array($validated['line'], ['G1', 'G2']) ? 'Left' : 'Right';
            } else {
                // Standard Agent Triangle
                $currentNode = in_array($validated['line'], ['G1', 'G2']) 
                    ? $sponsor->nodes()->where('account_type', 'Sub_A')->first()
                    : $sponsor->nodes()->where('account_type', 'Sub_B')->first();
                    
                $position = in_array($validated['line'], ['G1', 'G3']) ? 'Left' : 'Right';
            }

            // Extreme Outer Leg Spillover Traversal
            while (true) {
                $childNode = Node::where('parent_node_id', $currentNode->id)
                                ->where('position', $position)
                                ->first();

                if (!$childNode) {
                    // Spot is physically empty. Check for pending reservations.
                    $pending = User::where('intended_parent_node_id', $currentNode->id)
                                ->where('intended_position', $position)
                                ->where('admission_status', ['Pending Payment', 'Pending Approval'])
                                ->exists();
                    
                    if ($pending) {
                        return back()->withErrors(['line' => 'A pending registration is currently reserving the next available slot on this line. Please wait for approval or choose another line.']);
                    }
                    
                    $placement_id = $currentNode->id;
                    $placement_position = $position;
                    break; // Found our spot!
                }

                // Spot is occupied. Drop down to the occupant's equivalent Sub-Node.
                $occupantUser = $childNode->user;
                $currentNode = in_array($validated['line'], ['G1', 'G2']) 
                    ? $occupantUser->nodes()->where('account_type', 'Sub_A')->first() 
                    : $occupantUser->nodes()->where('account_type', 'Sub_B')->first();
            }
        }

        // 4. Create the Student as 'Pending Approval' at the discovered placement
        $student = DB::transaction(function () use ($validated, $request, $sponsor, $placement_id, $placement_position) {
            $nextMemberId = User::max('member_id') + 1;
            return User::create([
                'member_id' => $nextMemberId,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'nic' => $validated['nic'],
                'nic_type' => $validated['nic_type'],
                'phone' => $validated['phone'],
                'district' => $validated['district'],
                'city' => $validated['city'],
                'address' => $validated['address'],
                'role' => 'Student',
                'password' => Hash::make($validated['nic']),
                'admission_status' => 'Pending Payment',
                'payment_transaction_id' => $request->input('payment_transaction_id', 'PENDING_CASH_HANDOVER'),
                'admission_fee_paid' => $request->input('admission_fee_paid', 15000.00),
                // Evaluated Auto-Placement Data
                'registered_by_agent_id' => $sponsor->id, 
                'intended_parent_node_id' => $placement_id,
                'intended_position' => $placement_position, 
            ]);
        });

        // Use the new member_id for the success message formatting
        $newStudentId = str_pad($student->member_id, 4, '0', STR_PAD_LEFT);

        DB::afterCommit(function () use ($student) {
            Mail::to($student->email)->queue(new StudentWelcomeMail($student));
        });

        // Redirect to Due Payments with the new Student ID in the message
        return redirect()->route('payments.due')->with([
            'flash_modal' => true,
            'title' => 'Registration Successful',
            'message' => "{$student->name} has been successfully registered and assigned the Student ID #{$newStudentId}. Please submit their payment details below to secure their placement on the network tree.",
            'theme' => 'success'
        ]);
    }

    /**
     * Update a pending student's details (Typos only, placement cannot be changed)
     */
    public function update(Request $request, User $student)
    {
        // 1. Check if the user trying to edit is actually the sponsor
        if ($student->registered_by_agent_id !== Auth::id()) {
            return back()->withErrors(['error' => 'Unauthorized: You are not the sponsor of this student.']);
        }

        // 2. Check if the student is in an editable state (Not Active/Overdue)
        if (!in_array($student->admission_status, ['Pending Payment', 'Pending Approval', 'Rejected'])) {
            return back()->withErrors(['error' => 'This student is already active on the network and cannot be edited.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'nic' => 'required|string|unique:users,nic,' . $student->id,
            'phone' => 'required|string',
            'district' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
        ]);

        $student->update($validated);

        return back()->with('success', 'Pending student details updated successfully.');
    }

    /**
     * Hard Delete a pending registration and free up the tree node
     */
    public function voidRegistration(User $student)
    {
        // 1. Security Check: Ensure the agent owns this registration OR the user is an Admin
        if ($student->registered_by_agent_id !== Auth::id() && Auth::user()->role !== 'Admin') {
            return back()->withErrors(['error' => 'Unauthorized: You are not the sponsor of this student.']);
        }

        // 2. State Check: Only allow deletion if no payment is currently under review
        if (!in_array($student->admission_status, ['Pending Payment', 'Rejected'])) {
            return back()->withErrors(['error' => 'This registration cannot be deleted because a payment is currently under review or it is already active.']);
        }

        $student->delete();

        return back()->with('success', 'Registration permanently voided. The tree slot is now available.');
    }
}