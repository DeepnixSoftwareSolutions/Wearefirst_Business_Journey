<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Bank details are required for Agents, optional for everyone else
        $bankRule = $user->role === 'Agent' ? 'required|string|max:255' : 'nullable|string|max:255';

        // Note: Email and NIC are intentionally excluded from validation so they cannot be changed.
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'district' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            
            // Beneficiary validation
            'beneficiary_name' => 'nullable|string|max:255',
            'beneficiary_relationship' => 'nullable|string|max:255',
            'beneficiary_nic' => 'nullable|string|max:20',
            'beneficiary_phone' => 'nullable|string|max:20',

            // Bank Details validation
            'bank_account_name' => $bankRule,
            'bank_account_no' => $bankRule,
            'bank_name' => $bankRule,
            'bank_branch' => $bankRule,
            
            // Profile Image
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Update standard fields
        $user->fill([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'district' => $validated['district'],
            'city' => $validated['city'],
            'address' => $validated['address'],
        ]);

        // Handle JSON Beneficiary Data
        if ($request->filled('beneficiary_name')) {
            $user->beneficiary_details = [
                'name' => $validated['beneficiary_name'],
                'relationship' => $validated['beneficiary_relationship'],
                'nic' => $validated['beneficiary_nic'],
                'phone' => $validated['beneficiary_phone'],
            ];
        }

        // Handle JSON Bank Data (ONLY IF IT DOES NOT EXIST YET)
        if ($request->filled('bank_account_no')) {
            if (!$user->bank_details || $user->role === 'Admin') { // Lock check!
                $user->bank_details = [
                    'account_name' => $validated['bank_account_name'],
                    'account_no' => $validated['bank_account_no'],
                    'bank_name' => $validated['bank_name'],
                    'branch' => $validated['bank_branch'],
                ];
            }
        }

        // Handle Profile Image Upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if it exists
            if ($user->profile_image_path) {
                Storage::disk('public')->delete($user->profile_image_path);
            }
            $user->profile_image_path = $request->file('profile_image')->store('profiles', 'public');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Safely Delete the user's account (Anonymize).
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Only Agents and Students can delete their accounts
        if (in_array($user->role, ['Admin', 'Manager', 'Accountant'])) {
            return Redirect::route('profile.edit')->withErrors(['error' => 'System staff accounts cannot be deleted. Contact the Admin.']);
        }

        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        Auth::logout();

        // CRITICAL MLM Safe Delete (Anonymize) instead of actual delete!
        $randomHash = uniqid();
        $user->update([
            'name' => 'Terminated Account',
            'email' => "deleted_{$randomHash}@wearefirst.lk",
            'nic' => "DELETED_{$randomHash}",
            'phone' => '0000000000',
            'password' => bcrypt(str()->random(20)),
            'is_held' => true,
            'role' => 'Student',
            'profile_image_path' => null, 
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}