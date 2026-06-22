<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryRequest extends Model
{
    protected $fillable = ['user_id', 'amount', 'status', 'requested_by', 'approved_by'];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function requestedBy() {
        return $this->belongsTo(User::class, 'requested_by');
    }
    public function approvedBy() {
        return $this->belongsTo(User::class, 'approved_by');
    }
}