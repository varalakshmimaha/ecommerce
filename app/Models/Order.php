<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'address_id',
        'name',
        'mobile',
        'email',
        'address',
        'pincode',
        'subtotal',
        'gst_amount',
        'shipping_charge',
        'total_amount',
        'payment_status',
        'order_status',
        'payment_proof',
        'tracking_id',
        'tracking_url',
        'notes',
    ];

    public function addresses()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }

    protected $casts = [
        'subtotal' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'shipping_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(10));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}

