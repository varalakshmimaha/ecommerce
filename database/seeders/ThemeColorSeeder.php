<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ThemeColor;

class ThemeColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default theme with current colors
        ThemeColor::create([
            'name' => 'Default Theme',
            'brand_gold' => '#F4B41A',
            'brand_amber' => '#F28C28',
            'brand_burnt' => '#E36F2D',
            'brand_crimson' => '#C73A2B',
            'text_heading' => '#1A1A1A',
            'text_body' => '#374151',
            'text_muted' => '#6B6B6B',
            'text_white' => '#FFFFFF',
            'surface_primary' => '#FFFFFF',
            'surface_secondary' => '#F9FAFB',
            'surface_light' => '#F3F4F6',
            'surface_medium' => '#E5E7EB',
            'surface_dark' => '#111827',
            'ui_primary' => '#3B82F6',
            'ui_success' => '#10B981',
            'ui_warning' => '#F59E0B',
            'ui_error' => '#EF4444',
            'ui_info' => '#06B6D4',
            'ui_hover' => '#F3F4F6',
            'ui_border' => '#D1D5DB',
            'ui_focus' => '#F4B41A',
            'gradient_primary' => 'from-brand-gold via-brand-amber to-brand-crimson',
            'gradient_secondary' => 'from-brand-amber via-brand-burnt to-brand-crimson',
            'gradient_hero' => 'from-brand-gold via-brand-amber to-brand-crimson',
            'is_active' => true,
        ]);

        // Create alternative themes
        ThemeColor::create([
            'name' => 'Ocean Blue',
            'brand_gold' => '#0EA5E9',
            'brand_amber' => '#0284C7',
            'brand_burnt' => '#0369A1',
            'brand_crimson' => '#075985',
            'text_heading' => '#0F172A',
            'text_body' => '#334155',
            'text_muted' => '#64748B',
            'text_white' => '#FFFFFF',
            'surface_primary' => '#FFFFFF',
            'surface_secondary' => '#F8FAFC',
            'surface_light' => '#F1F5F9',
            'surface_medium' => '#E2E8F0',
            'surface_dark' => '#0F172A',
            'ui_primary' => '#0EA5E9',
            'ui_success' => '#10B981',
            'ui_warning' => '#F59E0B',
            'ui_error' => '#EF4444',
            'ui_info' => '#06B6D4',
            'ui_hover' => '#F1F5F9',
            'ui_border' => '#CBD5E1',
            'ui_focus' => '#0EA5E9',
            'gradient_primary' => 'from-brand-gold via-brand-amber to-brand-crimson',
            'gradient_secondary' => 'from-brand-amber via-brand-burnt to-brand-crimson',
            'gradient_hero' => 'from-brand-gold via-brand-amber to-brand-crimson',
            'is_active' => false,
        ]);

        ThemeColor::create([
            'name' => 'Forest Green',
            'brand_gold' => '#10B981',
            'brand_amber' => '#059669',
            'brand_burnt' => '#047857',
            'brand_crimson' => '#065F46',
            'text_heading' => '#14532D',
            'text_body' => '#166534',
            'text_muted' => '#6B7280',
            'text_white' => '#FFFFFF',
            'surface_primary' => '#FFFFFF',
            'surface_secondary' => '#F0FDF4',
            'surface_light' => '#DCFCE7',
            'surface_medium' => '#BBF7D0',
            'surface_dark' => '#14532D',
            'ui_primary' => '#10B981',
            'ui_success' => '#10B981',
            'ui_warning' => '#F59E0B',
            'ui_error' => '#EF4444',
            'ui_info' => '#06B6D4',
            'ui_hover' => '#F0FDF4',
            'ui_border' => '#BBF7D0',
            'ui_focus' => '#10B981',
            'gradient_primary' => 'from-brand-gold via-brand-amber to-brand-crimson',
            'gradient_secondary' => 'from-brand-amber via-brand-burnt to-brand-crimson',
            'gradient_hero' => 'from-brand-gold via-brand-amber to-brand-crimson',
            'is_active' => false,
        ]);

        ThemeColor::create([
            'name' => 'Royal Purple',
            'brand_gold' => '#8B5CF6',
            'brand_amber' => '#7C3AED',
            'brand_burnt' => '#6D28D9',
            'brand_crimson' => '#5B21B6',
            'text_heading' => '#1E1B4B',
            'text_body' => '#312E81',
            'text_muted' => '#6B7280',
            'text_white' => '#FFFFFF',
            'surface_primary' => '#FFFFFF',
            'surface_secondary' => '#F5F3FF',
            'surface_light' => '#EDE9FE',
            'surface_medium' => '#DDD6FE',
            'surface_dark' => '#1E1B4B',
            'ui_primary' => '#8B5CF6',
            'ui_success' => '#10B981',
            'ui_warning' => '#F59E0B',
            'ui_error' => '#EF4444',
            'ui_info' => '#06B6D4',
            'ui_hover' => '#F5F3FF',
            'ui_border' => '#DDD6FE',
            'ui_focus' => '#8B5CF6',
            'gradient_primary' => 'from-brand-gold via-brand-amber to-brand-crimson',
            'gradient_secondary' => 'from-brand-amber via-brand-burnt to-brand-crimson',
            'gradient_hero' => 'from-brand-gold via-brand-amber to-brand-crimson',
            'is_active' => false,
        ]);
    }
}
