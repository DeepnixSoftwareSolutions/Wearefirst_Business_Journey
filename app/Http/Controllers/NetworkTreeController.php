<?php

namespace App\Http\Controllers;

use App\Models\Node;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NetworkTreeController extends Controller
{
    /**
     * Render the standalone Network Tree page
     */
    public function index()
    {
        return \Inertia\Inertia::render('NetworkTree');
    }
    
    /**
     * Fetch the tree data formatted for D3.js
     */
    public function getTreeData(Request $request)
    {
        $user = Auth::user();

        // 1. HIGH PERFORMANCE: Fetch ALL nodes and users in a single query.
        $allNodes = Node::with('user')->get();

        // 2. Find the starting root node for the currently logged-in user
        // If Admin, it grabs the absolute top. If an Agent, it grabs their specific network.
        $rootNode = $allNodes->where('user_id', $user->id)->where('account_type', 'Main')->first();

        // Fetch all pending students who have reserved a spot
        // ADDED: 'admission_status' to the select array
        $pendingPlacements = User::whereIn('admission_status', ['Pending Payment', 'Pending Approval', 'Rejected'])
                                ->whereNotNull('intended_parent_node_id')
                                ->get(['intended_parent_node_id', 'intended_position', 'name', 'admission_status']);

        if (!$rootNode) {
            return response()->json(['error' => 'No network tree found.'], 404);
        }

        // 3. Build the hierarchical JSON structure in server memory
        $tree = $this->buildNestedTreeInMemory($rootNode, $allNodes, $pendingPlacements);

        return response()->json($tree);
    }

    /**
     * Recursive function to build the tree from the in-memory collection
     */
    private function buildNestedTreeInMemory($node, $allNodes, $pendingPlacements)
    {
        $user = $node->user;
        $accountType = $node->account_type === 'Main' ? 'Main Account' : 'Sub Account';
        
        $initials = '??';
        $paddedId = '0000';
        $uniqueName = 'COMPANY';

        // Automatically treat the Admin account as completely unlocked
        $isUnlocked = $user ? ($user->role === 'Admin' || $user->middle_legs_unlocked) : false;

        if ($user) {
            $words = explode(' ', $user->name);
            $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
            if ($user->member_id) {
                $paddedId = str_pad($user->member_id, 4, '0', STR_PAD_LEFT);
            } else {
                $paddedId = 'STAFF';
            }
            
            $uniqueName = $paddedId;
            if ($node->account_type === 'Sub_A') $uniqueName .= ' L';
            if ($node->account_type === 'Sub_B') $uniqueName .= ' R';
        }

        $data = [
            'id' => $node->id,
            'name' => $user ? $user->name : 'Terminated',
            'attributes' => [
                'unique_name' => $uniqueName, 
                'node_id' => $node->id,
                'user_id' => $user ? $user->id : null,
                'user_role' => $user ? $user->role : 'Empty',
                'node_type' => $accountType,
                'position' => $node->position ?? 'Root',
                'middle_legs_unlocked' => $isUnlocked,
                'left_points' => $node->left_points,
                'right_points' => $node->right_points,
                'today_projected' => $node->projected_income ?? 0,
                'node_cumulative' => $node->node_cumulative_pending ?? 0,
                'user_total_cumulative' => $node->account_type === 'Main' && $user ? $user->pending_payout_balance : null,
                'admission_fee_paid' => $user ? (float) $user->admission_fee_paid : 0, 
                'is_terminated' => ($user && $user->name === 'Terminated Account'),
                'is_held' => $user ? $user->is_held : false,
                'is_empty' => false,
                'is_pending' => false,
                'initials' => $initials
            ]
        ];

        $children = $allNodes->where('parent_node_id', $node->id);
        $leftChild = $children->where('position', 'Left')->first();
        $rightChild = $children->where('position', 'Right')->first();

        $data['children'] = [];
        
        $pendingLeft = $pendingPlacements->where('intended_parent_node_id', $node->id)->where('intended_position', 'Left')->first();
        $data['children'][] = $leftChild ? $this->buildNestedTreeInMemory($leftChild, $allNodes, $pendingPlacements) : $this->getEmptyPlaceholder('Left', $node->id, $pendingLeft, $uniqueName . ' G' . ($node->account_type === 'Sub_B' ? '3' : '1'), $isUnlocked);

        $pendingRight = $pendingPlacements->where('intended_parent_node_id', $node->id)->where('intended_position', 'Right')->first();
        $data['children'][] = $rightChild ? $this->buildNestedTreeInMemory($rightChild, $allNodes, $pendingPlacements) : $this->getEmptyPlaceholder('Right', $node->id, $pendingRight, $uniqueName . ' G' . ($node->account_type === 'Sub_B' ? '4' : '2'), $isUnlocked);

        return $data;
    }

    private function getEmptyPlaceholder($position, $parentId, $pendingUser = null, $lineName = 'Slot', $unlocked = false)
    {
        // Determine the specific string to send to the frontend based on the exact DB status
        $displayStatus = null;
        if ($pendingUser) {
            $displayStatus = in_array($pendingUser->admission_status, ['Pending Payment', 'Rejected']) 
                ? 'Pending Payment' 
                : 'Pending Approval';
        }

        return [
            'id' => 'empty_' . $position . '_' . $parentId,
            'name' => $pendingUser ? 'Reserved: ' . explode(' ', $pendingUser->name)[0] : 'Available Slot',
            'attributes' => [
                'unique_name' => $lineName, // e.g. "0004 L G1"
                'parent_id' => $parentId,
                'position' => $position,
                'middle_legs_unlocked' => $unlocked,
                'is_empty' => true,
                'is_pending' => $pendingUser ? true : false,
                'pending_status' => $displayStatus // 'Pending Payment' or 'Pending Approval'
            ]
        ];
    }
}