@extends('layouts.admin')

@section('title', 'Preview Theme')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-text-heading">Theme Preview: {{ $themeColor->name ?: 'Theme #' . $themeColor->id }}</h2>
    <div class="flex space-x-3">
        <a href="{{ route('admin.theme-colors.index') }}" class="text-brand-gold hover:text-brand-amber font-medium">← Back to Themes</a>
        @if(!$themeColor->is_active)
            <form action="{{ route('admin.theme-colors.activate', $themeColor) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-4 py-2 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Activate Theme</button>
            </form>
        @endif
    </div>
</div>

<!-- Live Preview with Theme Colors -->
<div class="admin-card">
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-text-heading mb-4">Live Preview</h3>
        <p class="text-text-muted mb-4">This preview shows how your theme will look across different components.</p>
    </div>

    <style>
        .theme-preview {
            --brand-gold: {{ $themeColor->brand_gold }};
            --brand-amber: {{ $themeColor->brand_amber }};
            --brand-burnt: {{ $themeColor->brand_burnt }};
            --brand-crimson: {{ $themeColor->brand_crimson }};
            --text-heading: {{ $themeColor->text_heading }};
            --text-body: {{ $themeColor->text_body }};
            --text-muted: {{ $themeColor->text_muted }};
            --text-white: {{ $themeColor->text_white }};
            --surface-primary: {{ $themeColor->surface_primary }};
            --surface-secondary: {{ $themeColor->surface_secondary }};
            --surface-light: {{ $themeColor->surface_light }};
            --surface-medium: {{ $themeColor->surface_medium }};
            --surface-dark: {{ $themeColor->surface_dark }};
            --ui-primary: {{ $themeColor->ui_primary }};
            --ui-success: {{ $themeColor->ui_success }};
            --ui-warning: {{ $themeColor->ui_warning }};
            --ui-error: {{ $themeColor->ui_error }};
            --ui-info: {{ $themeColor->ui_info }};
            --ui-hover: {{ $themeColor->ui_hover }};
            --ui-border: {{ $themeColor->ui_border }};
            --ui-focus: {{ $themeColor->ui_focus }};
        }
        
        .theme-preview .bg-gradient-to-r.from-brand-gold.via-brand-amber.to-brand-crimson {
            background: linear-gradient(to right, {{ $themeColor->brand_gold }}, {{ $themeColor->brand_amber }}, {{ $themeColor->brand_crimson }});
        }
        
        .theme-preview .bg-gradient-to-r.from-brand-amber.via-brand-burnt.to-brand-crimson {
            background: linear-gradient(to right, {{ $themeColor->brand_amber }}, {{ $themeColor->brand_burnt }}, {{ $themeColor->brand_crimson }});
        }
    </style>

    <div class="theme-preview space-y-8">
        <!-- Header Section -->
        <div class="p-6 bg-surface-primary rounded-lg border border-ui-border">
            <h1 class="text-3xl font-bold text-text-heading mb-2">Welcome to Your Theme</h1>
            <p class="text-text-body mb-4">This is how your headings and body text will appear.</p>
            <div class="flex space-x-4">
                <button class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-text-white px-6 py-2 rounded-lg font-semibold">Primary Button</button>
                <button class="bg-ui-primary text-text-white px-6 py-2 rounded-lg font-semibold">UI Button</button>
                <button class="border border-ui-border text-text-heading px-6 py-2 rounded-lg font-semibold">Outline Button</button>
            </div>
        </div>

        <!-- Color Palette -->
        <div>
            <h3 class="text-xl font-semibold text-text-heading mb-4">Color Palette</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div class="text-center">
                    <div class="w-full h-20 rounded-lg mb-2" style="background-color: {{ $themeColor->brand_gold }}"></div>
                    <p class="text-sm font-medium text-text-heading">Brand Gold</p>
                    <p class="text-xs text-text-muted">{{ $themeColor->brand_gold }}</p>
                </div>
                <div class="text-center">
                    <div class="w-full h-20 rounded-lg mb-2" style="background-color: {{ $themeColor->brand_amber }}"></div>
                    <p class="text-sm font-medium text-text-heading">Brand Amber</p>
                    <p class="text-xs text-text-muted">{{ $themeColor->brand_amber }}</p>
                </div>
                <div class="text-center">
                    <div class="w-full h-20 rounded-lg mb-2" style="background-color: {{ $themeColor->brand_burnt }}"></div>
                    <p class="text-sm font-medium text-text-heading">Brand Burnt</p>
                    <p class="text-xs text-text-muted">{{ $themeColor->brand_burnt }}</p>
                </div>
                <div class="text-center">
                    <div class="w-full h-20 rounded-lg mb-2" style="background-color: {{ $themeColor->brand_crimson }}"></div>
                    <p class="text-sm font-medium text-text-heading">Brand Crimson</p>
                    <p class="text-xs text-text-muted">{{ $themeColor->brand_crimson }}</p>
                </div>
                <div class="text-center">
                    <div class="w-full h-20 rounded-lg mb-2" style="background-color: {{ $themeColor->text_heading }}"></div>
                    <p class="text-sm font-medium text-text-heading">Text Heading</p>
                    <p class="text-xs text-text-muted">{{ $themeColor->text_heading }}</p>
                </div>
                <div class="text-center">
                    <div class="w-full h-20 rounded-lg mb-2" style="background-color: {{ $themeColor->text_body }}"></div>
                    <p class="text-sm font-medium text-text-heading">Text Body</p>
                    <p class="text-xs text-text-muted">{{ $themeColor->text_body }}</p>
                </div>
            </div>
        </div>

        <!-- Form Elements -->
        <div class="p-6 bg-surface-secondary rounded-lg">
            <h3 class="text-xl font-semibold text-text-heading mb-4">Form Elements</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Text Input</label>
                    <input type="text" placeholder="Enter text..." class="w-full px-4 py-2 border border-ui-border rounded-lg text-text-heading placeholder-text-muted focus:ring-2 focus:ring-ui-focus focus:border-ui-focus">
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Select Dropdown</label>
                    <select class="w-full px-4 py-2 border border-ui-border rounded-lg text-text-heading focus:ring-2 focus:ring-ui-focus focus:border-ui-focus">
                        <option>Option 1</option>
                        <option>Option 2</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Checkbox</label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="accent-brand-gold">
                        <span class="text-text-body">I agree to terms</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Radio Buttons</label>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="radio" class="accent-brand-gold">
                            <span class="text-text-body">Option 1</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="radio" class="accent-brand-gold">
                            <span class="text-text-body">Option 2</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Messages -->
        <div>
            <h3 class="text-xl font-semibold text-text-heading mb-4">Status Messages</h3>
            <div class="space-y-3">
                <div class="p-4 bg-ui-success/10 border border-ui-success/20 rounded-lg">
                    <p class="text-ui-success font-medium">Success message</p>
                    <p class="text-text-body text-sm">This is a success message with your theme colors.</p>
                </div>
                <div class="p-4 bg-ui-warning/10 border border-ui-warning/20 rounded-lg">
                    <p class="text-ui-warning font-medium">Warning message</p>
                    <p class="text-text-body text-sm">This is a warning message with your theme colors.</p>
                </div>
                <div class="p-4 bg-ui-error/10 border border-ui-error/20 rounded-lg">
                    <p class="text-ui-error font-medium">Error message</p>
                    <p class="text-text-body text-sm">This is an error message with your theme colors.</p>
                </div>
                <div class="p-4 bg-ui-info/10 border border-ui-info/20 rounded-lg">
                    <p class="text-ui-info font-medium">Info message</p>
                    <p class="text-text-body text-sm">This is an info message with your theme colors.</p>
                </div>
            </div>
        </div>

        <!-- Gradients -->
        <div>
            <h3 class="text-xl font-semibold text-text-heading mb-4">Gradients</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-6 bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson rounded-lg text-center">
                    <p class="text-text-white font-semibold">Primary Gradient</p>
                </div>
                <div class="p-6 bg-gradient-to-r from-brand-amber via-brand-burnt to-brand-crimson rounded-lg text-center">
                    <p class="text-text-white font-semibold">Secondary Gradient</p>
                </div>
                <div class="p-6 bg-gradient-to-r from-brand-gold via-brand-burnt to-brand-crimson rounded-lg text-center">
                    <p class="text-text-white font-semibold">Custom Gradient</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Theme Details -->
<div class="admin-card mt-6">
    <h3 class="text-lg font-semibold text-text-heading mb-4">Theme Details</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h4 class="font-medium text-text-heading mb-2">Basic Information</h4>
            <dl class="space-y-2">
                <div class="flex justify-between">
                    <dt class="text-text-muted">Name:</dt>
                    <dd class="text-text-heading">{{ $themeColor->name ?: 'Not set' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-text-muted">Status:</dt>
                    <dd>
                        @if($themeColor->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-ui-success/10 text-ui-success">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-text-muted">Created:</dt>
                    <dd class="text-text-heading">{{ $themeColor->created_at->format('M d, Y H:i') }}</dd>
                </div>
            </dl>
        </div>
        <div>
            <h4 class="font-medium text-text-heading mb-2">Quick Actions</h4>
            <div class="space-y-2">
                <a href="{{ route('admin.theme-colors.edit', $themeColor) }}" class="block w-full text-center bg-ui-primary text-text-white px-4 py-2 rounded-lg font-medium hover:opacity-90 transition-opacity">Edit Theme</a>
                @if(!$themeColor->is_active)
                    <form action="{{ route('admin.theme-colors.activate', $themeColor) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-text-white px-4 py-2 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Activate Theme</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
