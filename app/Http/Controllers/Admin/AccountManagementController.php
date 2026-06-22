<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AccountManagementController extends Controller
{
    public function index(Request $request)
    {
        // Eager load the sponsor and parent node user to power the Network Placement UI
        $query = User::whereIn('role', ['Agent', 'Accountant', 'Manager', 'Student'])
                     ->with(['sponsor', 'intendedParentNode.user']);

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

        if ($request->district) {
            $query->where('district', $request->district);
        }

        if ($request->city) {
            $query->where('city', $request->city);
        }

        return inertia('Admin/Users', [
            'users' => $query->orderBy('created_at', 'desc')->paginate(20),
            'filters' => $request->only('search', 'district', 'city')
        ]);
    }

    public function unlockMiddleLegs(User $agent)
    {
        if (in_array($agent->role, ['Admin', 'Manager'])) {
            return back()->withErrors(['error' => 'Cannot modify Admin or Manager accounts.']);
        }

        // Prevent action if already unlocked
        if ($agent->middle_legs_unlocked) {
            return back()->withErrors(['error' => 'Middle legs are already permanently unlocked.']);
        }

        // One-way permanent unlock
        $agent->middle_legs_unlocked = true;
        $agent->save();

        return back()->with('success', "Middle lines (G2 & G3) for {$agent->name} are unlocked.");
    }
    
    public function toggleHoldStatus(User $agent)
    {
        // Only Agents can be placed on hold
        if ($agent->role !== 'Agent') {
            return back()->withErrors(['error' => 'Only Agents can be placed on financial hold.']);
        }

        $agent->is_held = !$agent->is_held;
        $agent->save();

        $status = $agent->is_held ? 'placed on hold' : 'reactivated';

        return back()->with('success', "Agent {$agent->name} has been successfully {$status}.");
    }

    public function safeDeleteUser(User $user)
    {
        if (in_array($user->role, ['Admin', 'Manager'])) {
            return back()->withErrors(['error' => 'Cannot delete Admin or Manager accounts.']);
        }

        // 1. Anonymize the data to free up the NIC/Email for future use if needed
        $randomHash = uniqid();
        $user->update([
            'name' => 'Terminated Account',
            'email' => "deleted_{$randomHash}@wearefirst.lk",
            'nic' => "DELETED_{$randomHash}",
            'phone' => '0000000000',
            'password' => bcrypt(str()->random(20)), // Scramble password so they can never log in
            'is_held' => true, // Ensure no money is ever paid to this node again
            'role' => 'Student' // Strip their agent status
        ]);

        return back()->with('success', 'User safely terminated. Tree structure preserved.');
    }

    public function awardOffer(Request $request, User $user)
    {
        if ($user->role !== 'Agent') {
            return back()->withErrors(['error' => 'Special offers can only be awarded to active Agents.']);
        }

        $request->validate(['offer_name' => 'nullable|string|max:255']);

        $user->update([
            'special_offers' => $request->offer_name
        ]);
        
        $msg = $request->offer_name ? 'Offer awarded successfully.' : 'Offer revoked successfully.';
        return back()->with('success', $msg);
    }

    public function updateMasterProfile(Request $request, User $user)
    {
        if (in_array($user->role, ['Admin'])) {
            return back()->withErrors(['error' => 'Cannot modify Admin account via this interface.']);
        }

        $validated = $request->validate([
            // General Info
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            
            // Location
            'district' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            
            // NIC Details
            'nic' => 'required|string|unique:users,nic,' . $user->id,
            'nic_type' => 'required|in:Own,Mother,Father,Guardian',

            // Beneficiary Details (Optional but structured)
            'beneficiary_name' => 'nullable|string|max:255',
            'beneficiary_relationship' => 'nullable|string|max:255',
            'beneficiary_nic' => 'nullable|string|max:255',
            'beneficiary_phone' => 'nullable|string|max:255',

            // Bank Details (Optional but structured)
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_no' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
        ]);

        // Build Beneficiary JSON
        $beneficiaryDetails = null;
        if ($validated['beneficiary_name']) {
            $beneficiaryDetails = [
                'name' => $validated['beneficiary_name'],
                'relationship' => $validated['beneficiary_relationship'],
                'nic' => $validated['beneficiary_nic'],
                'phone' => $validated['beneficiary_phone'],
            ];
        }

        // Build Bank JSON
        $bankDetails = null;
        if ($validated['bank_account_no']) {
            $bankDetails = [
                'account_name' => $validated['bank_account_name'],
                'account_no' => $validated['bank_account_no'],
                'bank_name' => $validated['bank_name'],
                'branch' => $validated['bank_branch'],
            ];
        }

        // Execute Master Update
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'district' => $validated['district'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'nic' => $validated['nic'],
            'nic_type' => $validated['nic_type'],
            'beneficiary_details' => $beneficiaryDetails,
            'bank_details' => $bankDetails,
        ]);

        return back()->with([
            'flash_modal' => true,
            'title' => 'Master Profile Updated',
            'message' => "The profile for {$user->name} has been successfully updated.",
            'theme' => 'success'
        ]);
    }
}