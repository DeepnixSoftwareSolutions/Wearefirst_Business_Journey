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
        'today_income_projected',
        'node_cumulative_pending'
    ];

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
}