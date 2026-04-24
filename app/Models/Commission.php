<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'beneficiary_user_id',
        'beneficiary_role',
        'base_amount',
        'percentage',
        'amount',
        'status',
        'paid_at',
        'payout_ref',
        'withdrawal_id',
        'notes',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(User::class, 'beneficiary_user_id');
    }

    public function withdrawal()
    {
        return $this->belongsTo(Withdrawal::class);
    }

    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }

    public function scopeApproved($q)
    {
        return $q->where('status', 'approved');
    }

    public function scopePaid($q)
    {
        return $q->where('status', 'paid');
    }

    public function scopeForUser($q, int $userId)
    {
        return $q->where('beneficiary_user_id', $userId);
    }
}
