<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BadgeService;
use App\Mail\AgentWelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        // Fetch users who are Active but still have the 'Student' role
        $students = User::where('role', 'Student')
                        ->where('admission_status', 'Active')
                        ->with(['sponsor:id,name', 'intendedParentNode.user'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);

        return inertia('Manager/Promotions', [
            'students' => $students
        ]);
    }
    
    protected $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    public function promoteToAgent(Request $request, User $student)
    {
        if ($student->role !== 'Student') {
            return redirect()->back()->with('error', 'Only Students can be promoted.');
        }

        if ($student->admission_status !== 'Active') {
            return redirect()->back()->with('error', 'Student must be Active (Payment Approved) to be promoted.');
        }

        if ($student->admission_fee_paid < 15000) {
            return redirect()->back()->with('error', 'Student has not completed full payment of Rs 15,000.');
        }

        // Check if the current password is still the default (NIC)
        $hasDefaultPassword = Hash::check($student->nic, $student->password);
        
        $rawPassword = null;

        if ($hasDefaultPassword) {
            // Generate a secure password only if they haven't changed it themselves
            $rawPassword = Str::random(10);
            $student->password = Hash::make($rawPassword);
        }

        // Update the User profile
        $student->role = 'Agent';
        $student->save();

        // Send the automated email
        // If $rawPassword is null, we tell the email template to print "Your existing custom password"
        $displayPassword = $rawPassword ? $rawPassword : 'Your existing custom password';
        Mail::to($student->email)->queue(new AgentWelcomeMail($student, $displayPassword));

        // Trigger the Recursive Badge Engine for the Upline
        $uplineParent = $student->uplineUser();
        if ($uplineParent) {
            $this->badgeService->evaluate($uplineParent);
        }

        return redirect()->back()->with('success', 'Student successfully promoted to Agent. Credentials emailed.');
    }
}