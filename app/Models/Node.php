<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Node extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_node_id',
        'position',
        'account_type',
        'left_points',
        'right_points',
        'current_pair_level',
        'node_cumulative_pending',
        'last_payout_date'
    ];

    protected $casts = [
        'last_payout_date' => 'date:Y-m-d',
    ];

    // Append the dynamic attribute so it is included when the model is converted to JSON
    protected $appends = ['projected_income'];

    /**
     * Relationships
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Node::class, 'parent_node_id');
    }

    public function children()
    {
        return $this->hasMany(Node::class, 'parent_node_id');
    }

    /**
     * Logic Helpers
     */

    // Calculates how many 1:1 pairs are currently available
    public function getPairsAttribute()
    {
        return min($this->left_points, $this->right_points);
    }

    // Calculates the projected income based on the current pair level and available pairs
    public function getProjectedIncomeAttribute()
    {
        // 1. Students and held accounts project 0. (This instantly updates when promoted!)
        if (!$this->user || $this->user->role === 'Student' || $this->user->is_held) {
            return 0;
        }

        // 2. Throttle check: If already paid today, project 0
        if ($this->last_payout_date && $this->last_payout_date->isToday()) {
            return 0;
        }

        $pairs = min($this->left_points, $this->right_points);
        $level = $this->current_pair_level;

        // 3. Maturation Rules
        if ($level == 0 && $pairs >= 1) return 2500;
        if ($level == 1 && $pairs >= 2) return 5000;
        if ($level >= 2 && $pairs >= 3) return 7500;

        return 0;
    }
}