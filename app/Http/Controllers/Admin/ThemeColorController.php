<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class ThemeColorController extends Controller
{
    public function index()
    {
        $themes = ThemeColor::orderBy('created_at', 'desc')->get();
        $activeTheme = ThemeColor::getActive();
        
        return view('admin.theme-colors.index', compact('themes', 'activeTheme'));
    }

    public function create()
    {
        return view('admin.theme-colors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'brand_gold' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_amber' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_burnt' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_crimson' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_heading' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_body' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_muted' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_white' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'surface_primary' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'surface_secondary' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'surface_light' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'surface_medium' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'surface_dark' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_primary' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_success' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_warning' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_error' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_info' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_hover' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_border' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_focus' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'gradient_primary' => 'required|string|max:255',
            'gradient_secondary' => 'required|string|max:255',
            'gradient_hero' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        // If this theme is being set as active, deactivate all others
        if ($request->has('is_active') && $request->is_active) {
            ThemeColor::where('is_active', true)->update(['is_active' => false]);
        }

        $theme = ThemeColor::create($validated);

        // Generate CSS and Tailwind files
        $this->generateThemeFiles();

        return redirect()->route('admin.theme-colors.index')
            ->with('success', 'Theme created successfully!');
    }

    public function edit(ThemeColor $themeColor)
    {
        return view('admin.theme-colors.edit', compact('themeColor'));
    }

    public function update(Request $request, ThemeColor $themeColor)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'brand_gold' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_amber' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_burnt' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_crimson' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_heading' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_body' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_muted' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_white' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'surface_primary' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'surface_secondary' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'surface_light' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'surface_medium' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'surface_dark' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_primary' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_success' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_warning' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_error' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_info' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_hover' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_border' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ui_focus' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'gradient_primary' => 'required|string|max:255',
            'gradient_secondary' => 'required|string|max:255',
            'gradient_hero' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        // If this theme is being set as active, deactivate all others
        if ($request->has('is_active') && $request->is_active) {
            ThemeColor::where('is_active', true)->where('id', '!=', $themeColor->id)->update(['is_active' => false]);
        }

        $themeColor->update($validated);

        // Generate CSS and Tailwind files
        $this->generateThemeFiles();

        return redirect()->route('admin.theme-colors.index')
            ->with('success', 'Theme updated successfully!');
    }

    public function destroy(ThemeColor $themeColor)
    {
        // Prevent deletion of active theme
        if ($themeColor->is_active) {
            return redirect()->route('admin.theme-colors.index')
                ->with('error', 'Cannot delete active theme!');
        }

        $themeColor->delete();

        return redirect()->route('admin.theme-colors.index')
            ->with('success', 'Theme deleted successfully!');
    }

    public function activate(ThemeColor $themeColor)
    {
        // Deactivate all other themes
        ThemeColor::where('is_active', true)->update(['is_active' => false]);
        
        // Activate this theme
        $themeColor->update(['is_active' => true]);

        // Generate CSS and Tailwind files
        $this->generateThemeFiles();

        return redirect()->route('admin.theme-colors.index')
            ->with('success', 'Theme activated successfully!');
    }

    public function preview(ThemeColor $themeColor)
    {
        return view('admin.theme-colors.preview', compact('themeColor'));
    }

    /**
     * Generate CSS and Tailwind config files based on active theme
     */
    private function generateThemeFiles()
    {
        $activeTheme = ThemeColor::getActive();
        
        if (!$activeTheme) {
            return;
        }

        // Generate CSS variables file
        $this->generateCssFile($activeTheme);
        
        // Generate Tailwind config file
        $this->generateTailwindConfig($activeTheme);

        // Clear cache
        Cache::forget('active_theme');
    }

    /**
     * Generate CSS file with theme variables
     */
    private function generateCssFile($theme)
    {
        $cssVariables = $theme->toCssVariables();
        $cssContent = ":root {\n";
        
        foreach ($cssVariables as $variable => $value) {
            $cssContent .= "  {$variable}: {$value};\n";
        }
        
        $cssContent .= "}\n\n";
        
        // Add utility classes
        $cssContent .= ".bg-gradient-to-r.from-brand-gold.via-brand-amber.to-brand-crimson {\n";
        $cssContent .= "  background: linear-gradient(to right, {$theme->brand_gold}, {$theme->brand_amber}, {$theme->brand_crimson});\n";
        $cssContent .= "}\n\n";

        $cssContent .= ".bg-gradient-to-r.from-brand-amber.via-brand-burnt.to-brand-crimson {\n";
        $cssContent .= "  background: linear-gradient(to right, {$theme->brand_amber}, {$theme->brand_burnt}, {$theme->brand_crimson});\n";
        $cssContent .= "}\n";

        // Write to CSS file
        File::put(resource_path('css/theme-variables.css'), $cssContent);
    }

    /**
     * Generate Tailwind config file
     */
    private function generateTailwindConfig($theme)
    {
        $tailwindColors = $theme->toTailwindConfig();
        
        $configContent = "// Auto-generated theme colors\n";
        $configContent .= "const themeColors = " . json_encode($tailwindColors, JSON_PRETTY_PRINT) . ";\n\n";
        $configContent .= "module.exports = {\n";
        $configContent .= "  content: ['./resources/**/*.blade.php', './resources/**/*.js', './resources/**/*.vue'],\n";
        $configContent .= "  theme: {\n";
        $configContent .= "    extend: {\n";
        $configContent .= "      colors: themeColors\n";
        $configContent .= "    }\n";
        $configContent .= "  }\n";
        $configContent .= "};\n";

        // Write to Tailwind config file
        File::put(base_path('tailwind.theme.config.js'), $configContent);
    }
}
