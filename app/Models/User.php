<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'mobile',
        'email',
        'password',
        'is_admin',
        'role',
        'parent_id',
        'referral_code',
        'affiliate_status',
        'approved_at',
        'otp',
        'otp_expires_at',
        'is_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_verified' => 'boolean',
        'password' => 'hashed',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function queries()
    {
        return $this->hasMany(Query::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function affiliateProfile()
    {
        return $this->hasOne(AffiliateProfile::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'beneficiary_user_id');
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['affiliate', 'rm', 'manager'], true);
    }

    public function uplineByRole(): array
    {
        $chain = [];
        $node = $this->parent;
        $guard = 0;
        while ($node && $guard++ < 10) {
            if (in_array($node->role, ['affiliate', 'rm', 'manager'], true) && !isset($chain[$node->role])) {
                $chain[$node->role] = $node;
            }
            $node = $node->parent;
        }
        return $chain;
    }
}

