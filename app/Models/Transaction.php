<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'node_id', 'type', 'amount', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function node()
    {
        return $this->belongsTo(Node::class);
    }
}