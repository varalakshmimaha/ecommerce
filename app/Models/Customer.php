<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'notes',
        'is_active',
        'total_orders',
        'total_spent',
        'last_order_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_spent' => 'decimal:2',
        'last_order_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the orders for the customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    /**
     * Get the queries for the customer.
     */
    public function queries(): HasMany
    {
        return $this->hasMany(Query::class, 'user_id', 'id');
    }

    /**
     * Get the customer's full address.
     */
    public function getFullAddressAttribute(): string
    {
        $addressParts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $addressParts);
    }

    /**
     * Scope a query to only include active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to search customers by name, email, or mobile.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Update customer statistics.
     */
    public function updateStatistics()
    {
        $this->total_orders = $this->orders()->count();
        $this->total_spent = $this->orders()->sum('total_amount');
        $this->last_order_at = $this->orders()->max('created_at');
        $this->save();
    }

    /**
     * Get formatted total spent.
     */
    public function getFormattedTotalSpentAttribute(): string
    {
        return '₹' . number_format($this->total_spent, 2);
    }

    /**
     * Get formatted last order date.
     */
    public function getFormattedLastOrderAttribute(): string
    {
        return $this->last_order_at ? $this->last_order_at->format('M d, Y') : 'No orders';
    }
}
