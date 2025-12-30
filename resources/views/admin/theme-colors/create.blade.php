@extends('layouts.admin')

@section('title', 'Create Theme')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-text-heading">Create New Theme</h2>
    <a href="{{ route('admin.theme-colors.index') }}" class="text-brand-gold hover:text-brand-amber font-medium">← Back to Themes</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.theme-colors.store') }}" class="space-y-6">
        @csrf
        
        <!-- Theme Name -->
        <div>
            <label class="block text-sm font-semibold text-text-heading mb-2">Theme Name (Optional)</label>
            <input type="text" name="name" value="{{ old('name') }}" class="input-field" placeholder="e.g., Summer Theme, Corporate Blue">
        </div>

        <!-- Brand Colors -->
        <div>
            <h3 class="text-lg font-semibold text-text-heading mb-4 flex items-center gap-2">
                <span class="w-2 h-6 bg-brand-gold rounded"></span>
                Brand Colors
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Brand Gold</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="brand_gold_preview" value="#F4B41A" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=brand_gold]').value = this.value">
                        <input type="text" name="brand_gold" value="{{ old('brand_gold', '#F4B41A') }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Brand Amber</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="brand_amber_preview" value="#F28C28" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=brand_amber]').value = this.value">
                        <input type="text" name="brand_amber" value="{{ old('brand_amber', '#F28C28') }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Brand Burnt</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="brand_burnt_preview" value="#E36F2D" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=brand_burnt]').value = this.value">
                        <input type="text" name="brand_burnt" value="{{ old('brand_burnt', '#E36F2D') }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Brand Crimson</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="brand_crimson_preview" value="#C73A2B" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=brand_crimson]').value = this.value">
                        <input type="text" name="brand_crimson" value="{{ old('brand_crimson', '#C73A2B') }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Text Colors -->
        <div>
            <h3 class="text-lg font-semibold text-text-heading mb-4 flex items-center gap-2">
                <span class="w-2 h-6 bg-text-heading rounded"></span>
                Text Colors
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Heading Text</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="text_heading_preview" value="#1A1A1A" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=text_heading]').value = this.value">
                        <input type="text" name="text_heading" value="{{ old('text_heading', '#1A1A1A') }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Body Text</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="text_body_preview" value="#374151" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=text_body]').value = this.value">
                        <input type="text" name="text_body" value="{{ old('text_body', '#374151') }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Muted Text</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="text_muted_preview" value="#6B6B6B" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=text_muted]').value = this.value">
                        <input type="text" name="text_muted" value="{{ old('text_muted', '#6B6B6B') }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">White Text</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="text_white_preview" value="#FFFFFF" class="w-12 h-12 rounded cursor-pointer border border-gray-300" onchange="document.querySelector('input[name=text_white]').value = this.value">
                        <input type="text" name="text_white" value="{{ old('text_white', '#FFFFFF') }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- UI Colors -->
        <div>
            <h3 class="text-lg font-semibold text-text-heading mb-4 flex items-center gap-2">
                <span class="w-2 h-6 bg-ui-primary rounded"></span>
                UI Colors
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Primary UI</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="ui_primary_preview" value="#3B82F6" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=ui_primary]').value = this.value">
                        <input type="text" name="ui_primary" value="{{ old('ui_primary', '#3B82F6') }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Success UI</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="ui_success_preview" value="#10B981" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=ui_success]').value = this.value">
                        <input type="text" name="ui_success" value="{{ old('ui_success', '#10B981') }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Warning UI</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="ui_warning_preview" value="#F59E0B" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=ui_warning]').value = this.value">
                        <input type="text" name="ui_warning" value="{{ old('ui_warning', '#F59E0B') }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Error UI</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="ui_error_preview" value="#EF4444" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=ui_error]').value = this.value">
                        <input type="text" name="ui_error" value="{{ old('ui_error', '#EF4444') }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gradients -->
        <div>
            <h3 class="text-lg font-semibold text-text-heading mb-4 flex items-center gap-2">
                <span class="w-2 h-6 bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson rounded"></span>
                Gradients
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Primary Gradient</label>
                    <input type="text" name="gradient_primary" value="{{ old('gradient_primary', 'from-brand-gold via-brand-amber to-brand-crimson') }}" class="input-field" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Secondary Gradient</label>
                    <input type="text" name="gradient_secondary" value="{{ old('gradient_secondary', 'from-brand-amber via-brand-burnt to-brand-crimson') }}" class="input-field" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Hero Gradient</label>
                    <input type="text" name="gradient_hero" value="{{ old('gradient_hero', 'from-brand-gold via-brand-amber to-brand-crimson') }}" class="input-field" required>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_active" value="1" class="accent-brand-gold">
                <span class="text-sm font-medium text-text-heading">Set as active theme (will deactivate current theme)</span>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.theme-colors.index') }}" class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-medium transition-all duration-300">Cancel</a>
            <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Create Theme</button>
        </div>
    </form>
</div>

<script>
// Sync color inputs
document.addEventListener('DOMContentLoaded', function() {
    const colorInputs = document.querySelectorAll('input[type="color"]');
    colorInputs.forEach(input => {
        const textInput = document.querySelector(`input[name="${input.name.replace('_preview', '')}"]`);
        if (textInput) {
            input.value = textInput.value;
        }
    });
});
</script>
@endsection
