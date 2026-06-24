<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Node;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class CalculateDailyPayouts extends Command
{
    protected $signature = 'payouts:calculate-daily';
    protected $description = 'Calculates daily pair balances and flushes points according to stepped maturation rules.';

    public function handle()
    {
        $this->info('Starting Daily Payout Calculation ...');

        // If the cron runs at 12:00 AM to 1:00 AM, we assume it is processing YESTERDAY'S cutoff.
        // Otherwise, we assume it's running manually for the current day.
        $logicalBusinessDate = now()->hour < 2 
            ? now()->subDay()->toDateString() 
            : now()->toDateString();

        // Eager load the user to prevent N+1 query performance issues
        $nodes = Node::with('user')->where('left_points', '>', 0)->where('right_points', '>', 0)->get();

        DB::transaction(function () use ($nodes, $logicalBusinessDate) {
            foreach ($nodes as $node) {
                
                $user = $node->user;

                // 1. PROTECTION: If no user, or user is a Student/Held, SKIP entirely. 
                if (!$user || $user->role === 'Student' || $user->is_held) {
                    continue;
                }

                // 2. Throttle Check: Compare against the Logical Business Date
                if ($node->last_payout_date && $node->last_payout_date->toDateString() === $logicalBusinessDate) {
                    continue;
                }

                $pairs = min($node->left_points, $node->right_points);
                $level = $node->current_pair_level;
                
                $payout = 0;
                $newLevel = $level;
                $payablePairs = 0;

                // 3. Evaluate Payout & Flush Points
                if ($level == 0 && $pairs >= 1) {
                    $payout = 2500;
                    $payablePairs = 1;
                    $newLevel = 1;
                    $node->left_points -= 1;
                    $node->right_points -= 1;
                    
                } elseif ($level == 1 && $pairs >= 2) {
                    $payout = 5000;
                    $payablePairs = 2;
                    $newLevel = 2;
                    $node->left_points -= 2;
                    $node->right_points -= 2;
                    
                } elseif ($level >= 2 && $pairs >= 3) {
                    $payout = 7500;
                    $payablePairs = 3;
                    $newLevel = 3;
                    // HARD FLUSH
                    $node->left_points = 0;
                    $node->right_points = 0;
                }

                // 4. Skip if no income was generated
                if ($payout == 0) {
                    continue; 
                }

                // 5. Apply the state changes & lock using the LOGICAL date
                $node->current_pair_level = $newLevel; 
                $node->last_payout_date = $logicalBusinessDate; 
                $node->node_cumulative_pending += $payout;
                $node->save();

                $user->pending_payout_balance += $payout;
                $user->total_historical_earned += $payout;
                $user->save();

                Transaction::create([
                    'user_id' => $user->id,
                    'node_id' => $node->id,
                    'type' => 'Pair Bonus',
                    'amount' => $payout,
                    'description' => "Daily Pair Bonus ($payablePairs payable pairs)"
                ]);
            }
        });

        $this->info('Daily Payouts Calculated Successfully for Business Date: ' . $logicalBusinessDate);
    }
}