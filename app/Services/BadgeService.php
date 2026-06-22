<?php

namespace App\Services;

use App\Models\User;

class BadgeService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    // A numerical mapping of badges to make tier calculations easy
    protected $badgeTiers = [
        'None' => 0,
        'Ruby' => 1,
        'Diamond' => 2,
        'Crown Diamond' => 3,
        '2 Crown Diamond' => 4,
    ];

    /**
     * Evaluates a user's badge based on their 4 direct sub-users.
     * If the badge upgrades, it recursively evaluates their upline parent.
     */
    public function evaluate(User $user)
    {
        $subUsers = $user->directSubUsers();

        // An Agent MUST have exactly 4 direct sub-users to even qualify for a badge
        if ($subUsers->count() < 4) {
            return; 
        }

        $roles = $subUsers->pluck('role')->toArray();
        
        // If any of the 4 children are still just 'Students', the parent gets no badge
        if (in_array('Student', $roles)) {
            return;
        }

        // Find the lowest badge tier among the 4 children
        $lowestChildTier = 99;
        foreach ($subUsers as $subUser) {
            $tierValue = $this->badgeTiers[$subUser->badge] ?? 0;
            $lowestChildTier = min($lowestChildTier, $tierValue);
        }

        // The Parent's new tier is exactly 1 level higher than the LOWEST child tier
        $newParentTier = $lowestChildTier + 1;
        
        // Cap it at Tier 4 (2 Crown Diamond)
        if ($newParentTier > 4) {
            $newParentTier = 4;
        }

        // Map the numerical tier back to the string name
        $newBadgeName = array_search($newParentTier, $this->badgeTiers);

        // If the badge actually changed, save it and trigger the recursive check upwards
        if ($user->badge !== $newBadgeName) {
            $user->update(['badge' => $newBadgeName]);

            $uplineParent = $user->uplineUser();
            if ($uplineParent) {
                // Recursive call moving up the tree
                $this->evaluate($uplineParent);
            }
        }
    }
}
