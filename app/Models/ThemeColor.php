<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThemeColor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand_gold',
        'brand_amber',
        'brand_burnt',
        'brand_crimson',
        'text_heading',
        'text_body',
        'text_muted',
        'text_white',
        'surface_primary',
        'surface_secondary',
        'surface_light',
        'surface_medium',
        'surface_dark',
        'ui_primary',
        'ui_success',
        'ui_warning',
        'ui_error',
        'ui_info',
        'ui_hover',
        'ui_border',
        'ui_focus',
        'gradient_primary',
        'gradient_secondary',
        'gradient_hero',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the active theme colors
     */
    public static function getActive()
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Get theme colors as CSS variables
     */
    public function toCssVariables()
    {
        return [
            '--brand-gold' => $this->brand_gold,
            '--brand-amber' => $this->brand_amber,
            '--brand-burnt' => $this->brand_burnt,
            '--brand-crimson' => $this->brand_crimson,
            '--text-heading' => $this->text_heading,
            '--text-body' => $this->text_body,
            '--text-muted' => $this->text_muted,
            '--text-white' => $this->text_white,
            '--surface-primary' => $this->surface_primary,
            '--surface-secondary' => $this->surface_secondary,
            '--surface-light' => $this->surface_light,
            '--surface-medium' => $this->surface_medium,
            '--surface-dark' => $this->surface_dark,
            '--ui-primary' => $this->ui_primary,
            '--ui-success' => $this->ui_success,
            '--ui-warning' => $this->ui_warning,
            '--ui-error' => $this->ui_error,
            '--ui-info' => $this->ui_info,
            '--ui-hover' => $this->ui_hover,
            '--ui-border' => $this->ui_border,
            '--ui-focus' => $this->ui_focus,
        ];
    }

    /**
     * Get theme colors as Tailwind config
     */
    public function toTailwindConfig()
    {
        return [
            'brand' => [
                'gold' => $this->brand_gold,
                'amber' => $this->brand_amber,
                'burnt' => $this->brand_burnt,
                'crimson' => $this->brand_crimson,
            ],
            'text' => [
                'heading' => $this->text_heading,
                'body' => $this->text_body,
                'muted' => $this->text_muted,
                'white' => $this->text_white,
            ],
            'surface' => [
                'primary' => $this->surface_primary,
                'secondary' => $this->surface_secondary,
                'light' => $this->surface_light,
                'medium' => $this->surface_medium,
                'dark' => $this->surface_dark,
            ],
            'ui' => [
                'primary' => $this->ui_primary,
                'success' => $this->ui_success,
                'warning' => $this->ui_warning,
                'error' => $this->ui_error,
                'info' => $this->ui_info,
                'hover' => $this->ui_hover,
                'border' => $this->ui_border,
                'focus' => $this->ui_focus,
            ]
        ];
    }
}
