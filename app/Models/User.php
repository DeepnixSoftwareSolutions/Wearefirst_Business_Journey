<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'member_id',
        'name',
        'email',
        'nic',
        'nic_type',
        'phone',
        'district',
        'city',
        'address',
        'profile_image_path',
        'role',
        'badge',
        'pending_payout_balance',
        'total_historical_earned',
        'middle_legs_unlocked',
        'is_held',
        'beneficiary_details',
        'bank_details',
        'password',
        'intended_parent_node_id',
        'intended_position',
        'registered_by_agent_id',
        'admission_fee_paid',
        'admission_status',
        'payment_transaction_id',
        'special_offers',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'beneficiary_details' => 'array',
            'bank_details' => 'array',
            'is_held' => 'boolean',
            'middle_legs_unlocked' => 'boolean',
            'special_offers' => 'array',
        ];
    }

    /**
     * Relationships
     */

    // A user owns 3 nodes (Main, Sub_A, Sub_B)
    public function nodes()
    {
        return $this->hasMany(Node::class);
    }

    // Helper to get only the Main node
    public function mainNode()
    {
        return $this->hasOne(Node::class)->where('account_type', 'Main');
    }

    /**
     * Accessors
     */

    // Calculates the total income from all 3 nodes plus direct commissions
    public function getTotalCalculatedIncomeAttribute()
    {
        return $this->pending_payout_balance;
    }

    // Get the agent who registered this user
    public function sponsor()
    {
        return $this->belongsTo(User::class, 'registered_by_agent_id');
    }

    // Get the node where this user is waiting to be placed
    public function intendedParentNode()
    {
        return $this->belongsTo(Node::class, 'intended_parent_node_id');
    }

    // Get the User directly above this user in the tree
    public function uplineUser()
    {
        $mainNode = $this->mainNode;
        if (!$mainNode || !$mainNode->parent_node_id) {
            return null;
        }
        
        // Return the user who owns the parent node of this user's main node
        return Node::find($mainNode->parent_node_id)->user;
    }

    // Get the (up to 4) direct sub-users situated under Sub A and Sub B
    public function directSubUsers()
    {
        // 1. Get the IDs of this user's Sub_A and Sub_B nodes
        $subNodeIds = $this->nodes()->whereIn('account_type', ['Sub_A', 'Sub_B'])->pluck('id');
        
        // 2. Find Users whose Main Node has a parent_node_id matching those Sub Nodes
        return User::whereHas('nodes', function($query) use ($subNodeIds) {
            $query->whereIn('parent_node_id', $subNodeIds)
                  ->where('account_type', 'Main');
        })->get();
    }
}
