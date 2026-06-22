<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Mail\PaymentWarningMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class CheckPendingPayments extends Command
{
    protected $signature = 'mlm:check-payments';
    protected $description = 'Checks for pending admission payments (10-day warning & 14-day cutoff)';

    public function handle()
    {
        $this->info('Checking for due admission payments...');

        // 1. Send 10-Day Warnings
        $warningDate = \Carbon\Carbon::now()->subDays(10)->toDateString();
        $warnUsers = User::where('admission_fee_paid', '<', 15000)
                         ->whereDate('created_at', $warningDate)
                         ->where('is_held', false) 
                         ->get();

        foreach ($warnUsers as $user) {
            Mail::to($user->email)->send(new PaymentWarningMail($user));
            $this->line("10-Day Warning sent to: {$user->email}");
        }

        // 2. Process 14-Day Terminations
        $cutoffDate = \Carbon\Carbon::now()->subDays(14)->toDateString();
        $cutoffUsers = User::where('admission_fee_paid', '<', 15000)
                           ->whereDate('created_at', '<=', $cutoffDate)
                           ->where('admission_status', '!=', 'Overdue')
                           ->get();

        foreach ($cutoffUsers as $user) {
            
            if ($user->admission_status === 'Active') {
                // They are physically on the tree (Paid 10k but missed the 5k balance)
                // We MUST anonymize them to preserve the tree structure.
                $randomHash = uniqid();
                $user->update([
                    'name' => 'Terminated Account',
                    'email' => "deleted_{$randomHash}@wearefirst.lk",
                    'nic' => "DELETED_{$randomHash}",
                    'phone' => '0000000000',
                    'password' => bcrypt(str()->random(20)),
                    'is_held' => true,
                    'admission_status' => 'Overdue',
                    'role' => 'Student'
                ]);
                $this->error("Account Frozen & Anonymized (14-day cutoff): {$user->email}");
            } else {
                // They never made it to the tree (Pending Payment, Rejected, Pending Approval)
                // Safely HARD DELETE to clean up the database.
                $email = $user->email;
                $user->delete();
                $this->error("Registration Permanently Deleted (14-day cutoff): {$email}");
            }
        }

        $this->info('Payment check complete.');
    }
}