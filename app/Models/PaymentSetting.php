<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_method',
        'is_active',
        'settings',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public static function getActiveMethods()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('payment_method')
            ->toArray();
    }

    public static function getSettings($method)
    {
        $setting = self::where('payment_method', $method)
            ->where('is_active', true)
            ->first();
        
        return $setting ? $setting->settings : [];
    }
}
