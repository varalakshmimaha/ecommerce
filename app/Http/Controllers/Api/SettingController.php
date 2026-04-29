<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use App\Models\FooterSection;
use App\Models\Setting;
use App\Models\ThemeColor;

class SettingController extends Controller
{
    /**
     * Public app/site settings safe to expose to mobile clients.
     */
    public function public()
    {
        $keys = [
            'site_name', 'site_logo', 'site_favicon',
            'support_email', 'support_phone', 'support_whatsapp',
            'company_address', 'currency', 'currency_symbol',
            'shipping_charge', 'free_shipping_above',
            'default_gst', 'razorpay_key_id',
            'enable_favourites', 'enable_compare',
            'min_order_amount', 'max_wallet_percentage',
            'play_store_url', 'app_store_url',
            'force_update_version_android', 'force_update_version_ios',
        ];

        $values = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

        $footerSections = FooterSection::with(['links' => function ($q) {
            $q->orderBy('sort_order');
        }])->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'settings'        => $values,
                'footer_sections' => $footerSections,
                'features'        => [
                    'enable_favourites' => ($values['enable_favourites'] ?? 'false') === 'true',
                    'enable_compare'    => ($values['enable_compare'] ?? 'false') === 'true',
                ],
            ],
        ]);
    }

    /**
     * Active theme colors + font for dynamic mobile theming.
     */
    public function theme()
    {
        $theme = ThemeColor::getActive();

        $fallback = [
            'name'              => 'default',
            'brand_gold'        => '#F4B41A',
            'brand_amber'       => '#F28C28',
            'brand_burnt'       => '#E36F2D',
            'brand_crimson'     => '#C73A2B',
            'text_heading'      => '#1A1A1A',
            'text_body'         => '#374151',
            'text_muted'        => '#6B6B6B',
            'text_white'        => '#FFFFFF',
            'surface_primary'   => '#FFFFFF',
            'surface_secondary' => '#F9FAFB',
            'surface_light'     => '#F3F4F6',
            'surface_medium'    => '#E5E7EB',
            'surface_dark'      => '#111827',
            'ui_primary'        => '#3B82F6',
            'ui_success'        => '#10B981',
            'ui_warning'        => '#F59E0B',
            'ui_error'          => '#EF4444',
            'ui_info'           => '#06B6D4',
            'ui_hover'          => '#F3F4F6',
            'ui_border'         => '#D1D5DB',
            'ui_focus'          => '#F4B41A',
        ];

        $colors = $theme ? array_intersect_key($theme->toArray(), $fallback) + $fallback : $fallback;

        return response()->json([
            'success' => true,
            'data'    => [
                'colors' => $colors,
                'fonts'  => [
                    'family'  => Setting::get('app_font_family', 'Inter'),
                    'heading' => Setting::get('app_font_heading', null),
                ],
                'logo'    => Setting::get('site_logo'),
                'favicon' => Setting::get('site_favicon'),
            ],
        ]);
    }
}
