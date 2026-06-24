<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class GroupServiceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $nodes = $user->nodes()->get()->keyBy('account_type');

        $mainNode = $nodes->get('Main');
        $subA = $nodes->get('Sub_A');
        $subB = $nodes->get('Sub_B');

        $totalProjected = ($mainNode->projected_income ?? 0) 
                        + ($subA->projected_income ?? 0) 
                        + ($subB->projected_income ?? 0);

        return Inertia::render('Agent/GroupService', [
            'networkData' => [
                'total_projected_today' => $totalProjected,
                'nodes' => [
                    'main' => $this->formatNodeData($mainNode, 'Main Account', 'Sub A (Left)', 'Sub B (Right)'),
                    'sub_a' => $this->formatNodeData($subA, 'Sub Account A', 'Line G1', 'Line G2'),
                    'sub_b' => $this->formatNodeData($subB, 'Sub Account B', 'Line G3', 'Line G4'),
                ]
            ]
        ]);
    }

    private function formatNodeData($node, $title, $leftName, $rightName)
    {
        if (!$node) return null;

        $projectedIncome = $node->projected_income ?? 0;
        
        $carriedLeft = $node->left_points;
        $carriedRight = $node->right_points;

        // Calculate surviving points based on tonight's projected payout consumption
        if ($projectedIncome == 7500) {
            // Hard Flush: 0 points survive
            $carriedLeft = 0;
            $carriedRight = 0;
        } elseif ($projectedIncome == 5000) {
            // Level 1: Consumes 2 pairs
            $carriedLeft = max(0, $node->left_points - 2);
            $carriedRight = max(0, $node->right_points - 2);
        } elseif ($projectedIncome == 2500) {
            // Level 0: Consumes 1 pair
            $carriedLeft = max(0, $node->left_points - 1);
            $carriedRight = max(0, $node->right_points - 1);
        }
        // If $projectedIncome == 0, nothing is consumed, original points carry over completely.

        return [
            'id' => $node->id,
            'title' => $title,
            'left_name' => $leftName,
            'right_name' => $rightName,
            
            // Raw Total Counts
            'left_points' => $node->left_points,
            'right_points' => $node->right_points,
            
            // Pass the node's graduation memory
            'current_pair_level' => $node->current_pair_level ?? 0,
            
            // Financial Values (Surviving points * Rs 1250)
            'left_value' => $carriedLeft * 1250,
            'right_value' => $carriedRight * 1250,
            
            'projected_income' => $projectedIncome,
            
            'pending_matches' => min($node->left_points, $node->right_points)
        ];
    }
}