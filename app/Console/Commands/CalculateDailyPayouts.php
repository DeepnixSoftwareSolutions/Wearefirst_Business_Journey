<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Node;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class CalculateDailyPayouts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'payouts:calculate-daily';

    /**
     * The console command description.
     */
    protected $description = 'Calculates and finalizes daily pair balances for the referral tree, applying the 7500 cap.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Daily Payout Calculation ...');

        $nodes = Node::where('left_points', '>', 0)->where('right_points', '>', 0)->get();

        DB::transaction(function () use ($nodes) {
            foreach ($nodes as $node) {
                
                $pairs = min($node->left_points, $node->right_points);
                if ($pairs == 0) continue;

                $user = $node->user;
                $level = $node->current_pair_level;
                
                $payablePairs = 0;
                $flushedPairs = 0; // Tracks exactly how many points to wipe
                $payout = 0;
                $newLevel = $level;

                // 1. Calculate Payout based on Graduation Rules
                if ($pairs >= 3) {
                    $payablePairs = 3;         // Cap the paid amount at 3 pairs
                    $flushedPairs = $pairs;    // FLUSH ALL EXCESS PAIRS (e.g., if 4 pairs, wipes 4)
                    $payout = 7500;
                    $newLevel = 2;             // They have hit 3:3, so they are permanently Level 2
                } elseif ($pairs == 2 && $level < 2) {
                    $payablePairs = 2;
                    $flushedPairs = 2;
                    $payout = 5000;
                    $newLevel = 2;             // They hit 2:2, graduate to Level 2
                } elseif ($pairs == 1 && $level == 0) {
                    $payablePairs = 1;
                    $flushedPairs = 1;
                    $payout = 2500;
                    $newLevel = 1;             // They hit 1:1, graduate to Level 1
                }

                // 2. If no income was generated (e.g., they are Level 2 but only got 1 or 2 pairs today)
                // We do NOT flush any points, and we do NOT pay anything.
                if ($payout == 0) {
                    $node->today_income_projected = 0; 
                    $node->save();
                    continue; 
                }

                // 3. Flush the pairs (Leaves ONLY the unbalanced points)
                $node->left_points -= $flushedPairs;
                $node->right_points -= $flushedPairs;
                $node->current_pair_level = $newLevel; // Save their new graduated level

                // 4. Determine if the user is eligible to actually receive the cash
                $isEligibleForPay = $user && $user->role !== 'Student' && !$user->is_held;

                if ($isEligibleForPay) {
                    $node->node_cumulative_pending += $payout;

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

                // Reset projection for the new day
                $node->today_income_projected = 0; 
                $node->save();
            }
        });

        $this->info('Daily Payouts Calculated Successfully.');
    }
}