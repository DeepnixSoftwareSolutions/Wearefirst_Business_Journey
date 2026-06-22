<?php

namespace App\Services;

use App\Models\Node;

class ReferralEngine
{
    /**
     * Injects a point into the tree and traverses upward to the root.
     * 
     * @param Node $node The node where the student was registered
     * @param string $side 'Left' or 'Right'
     */
    public function injectPoint(Node $node, $side)
    {
        $currentNode = $node;

        while ($currentNode) {
            // 1. Increment points on the current node
            if ($side === 'Left') {
                $currentNode->increment('left_points');
            } else {
                $currentNode->increment('right_points');
            }

            // 2. Update the "Today's Projected Income" for this node based on points
            $this->updateProjectedIncome($currentNode);

            // 3. Move up the tree
            $parent = $currentNode->parent;
            if ($parent) {
                // The side for the parent is determined by which child the current node is
                $side = $currentNode->position; 
                $currentNode = $parent;
            } else {
                // We reached the root (Company Main Account)
                $currentNode = null;
            }
        }
    }

    /**
     * Logic: Graduation Step-Up System
     * Level 0: 1:1 = 2500, 2:2 = 5000, 3:3 = 7500
     * Level 1: 2:2 = 5000, 3:3 = 7500
     * Level 2: 3:3 = 7500
     */
    private function updateProjectedIncome(Node $node)
    {
        // If the user is on hold OR is just a Student, they do not project any income
        if ($node->user && ($node->user->is_held || $node->user->role === 'Student')) {
            $node->update(['today_income_projected' => 0]);
            return;
        }

        $pairs = min($node->left_points, $node->right_points);
        $level = $node->current_pair_level;
        $income = 0;

        // Apply graduation rules to determine projected income
        if ($pairs >= 3) {
            $income = 7500; // Always pays if they hit 3+
        } elseif ($pairs == 2 && $level < 2) {
            $income = 5000; // Only pays if they haven't graduated past 2:2
        } elseif ($pairs == 1 && $level == 0) {
            $income = 2500; // Only pays if they are brand new (Level 0)
        }

        $node->update(['today_income_projected' => $income]);
    }
}