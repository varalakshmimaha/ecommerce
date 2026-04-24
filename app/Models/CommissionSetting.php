<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CommissionSetting extends Model
{
    use HasFactory;

    protected $fillable = ['role', 'percentage', 'is_active', 'updated_by'];

    protected $casts = [
        'percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public static function rateFor(string $role): float
    {
        $row = Cache::remember("commission_setting.$role", 300, function () use ($role) {
            return self::where('role', $role)->where('is_active', true)->first();
        });
        return $row ? (float) $row->percentage : 0.0;
    }

    public static function flushCache(): void
    {
        foreach (['affiliate', 'rm', 'manager'] as $r) {
            Cache::forget("commission_setting.$r");
        }
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::flushCache());
        static::deleted(fn () => self::flushCache());
    }
}
