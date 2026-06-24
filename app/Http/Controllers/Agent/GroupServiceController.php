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

        // 1. Calculate how many matches have already occurred
        $matches = min($node->left_points, $node->right_points);

        // 2. Subtract the matches to find the remaining unmatched points on each side
        $unmatchedLeft = $node->left_points - $matches;
        $unmatchedRight = $node->right_points - $matches;

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
            
            // Financial Values (Only the UNMATCHED points * Rs 1250)
            'left_value' => $unmatchedLeft * 1250,
            'right_value' => $unmatchedRight * 1250,
            
            'projected_income' => $node->projected_income ?? 0,
            
            'pending_matches' => $matches
        ];
    }
}