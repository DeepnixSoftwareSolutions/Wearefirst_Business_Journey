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

            // 2. Move up the tree
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
}