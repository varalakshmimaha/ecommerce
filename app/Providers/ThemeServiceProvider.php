<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\ThemeColor;
use Illuminate\Support\Facades\Cache;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share theme colors with all views
        View::composer('*', function ($view) {
            $activeTheme = Cache::remember('active_theme', 3600, function () {
                return ThemeColor::getActive();
            });

            if ($activeTheme) {
                $view->with('themeColors', $activeTheme->toCssVariables());
                $view->with('themeName', $activeTheme->name ?: 'Default Theme');
            } else {
                // Fallback to default colors if no active theme
                $view->with('themeColors', $this->getDefaultThemeColors());
                $view->with('themeName', 'Default Theme');
            }
        });
    }

    /**
     * Get default theme colors as fallback
     */
    private function getDefaultThemeColors(): array
    {
        return [
            '--brand-gold' => '#F4B41A',
            '--brand-amber' => '#F28C28',
            '--brand-burnt' => '#E36F2D',
            '--brand-crimson' => '#C73A2B',
            '--text-heading' => '#1A1A1A',
            '--text-body' => '#374151',
            '--text-muted' => '#6B6B6B',
            '--text-white' => '#FFFFFF',
            '--surface-primary' => '#FFFFFF',
            '--surface-secondary' => '#F9FAFB',
            '--surface-light' => '#F3F4F6',
            '--surface-medium' => '#E5E7EB',
            '--surface-dark' => '#111827',
            '--ui-primary' => '#3B82F6',
            '--ui-success' => '#10B981',
            '--ui-warning' => '#F59E0B',
            '--ui-error' => '#EF4444',
            '--ui-info' => '#06B6D4',
            '--ui-hover' => '#F3F4F6',
            '--ui-border' => '#D1D5DB',
            '--ui-focus' => '#F4B41A',
        ];
    }
}
