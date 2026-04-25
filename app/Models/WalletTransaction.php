<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = ['user_id', 'type', 'amount', 'remark', 'created_by', 'status'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function balanceFor(int $userId): float
    {
        $credit = static::where('user_id', $userId)->where('type', 'credit')->where('status', 'approved')->sum('amount');
        $debit  = static::where('user_id', $userId)->where('type', 'debit')->where('status', 'approved')->sum('amount');
        return (float) ($credit - $debit);
    }
}
