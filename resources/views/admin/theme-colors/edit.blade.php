@extends('layouts.admin')

@section('title', 'Edit Theme')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-text-heading">Edit Theme: {{ $themeColor->name ?: 'Theme #' . $themeColor->id }}</h2>
    <a href="{{ route('admin.theme-colors.index') }}" class="text-brand-gold hover:text-brand-amber font-medium">← Back to Themes</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.theme-colors.update', $themeColor) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Theme Name -->
        <div>
            <label class="block text-sm font-semibold text-text-heading mb-2">Theme Name (Optional)</label>
            <input type="text" name="name" value="{{ old('name', $themeColor->name) }}" class="input-field" placeholder="e.g., Summer Theme, Corporate Blue">
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
                        <input type="color" name="brand_gold_preview" value="{{ $themeColor->brand_gold }}" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=brand_gold]').value = this.value">
                        <input type="text" name="brand_gold" value="{{ old('brand_gold', $themeColor->brand_gold) }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Brand Amber</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="brand_amber_preview" value="{{ $themeColor->brand_amber }}" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=brand_amber]').value = this.value">
                        <input type="text" name="brand_amber" value="{{ old('brand_amber', $themeColor->brand_amber) }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Brand Burnt</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="brand_burnt_preview" value="{{ $themeColor->brand_burnt }}" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=brand_burnt]').value = this.value">
                        <input type="text" name="brand_burnt" value="{{ old('brand_burnt', $themeColor->brand_burnt) }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Brand Crimson</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="brand_crimson_preview" value="{{ $themeColor->brand_crimson }}" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=brand_crimson]').value = this.value">
                        <input type="text" name="brand_crimson" value="{{ old('brand_crimson', $themeColor->brand_crimson) }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
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
                        <input type="color" name="text_heading_preview" value="{{ $themeColor->text_heading }}" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=text_heading]').value = this.value">
                        <input type="text" name="text_heading" value="{{ old('text_heading', $themeColor->text_heading) }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Body Text</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="text_body_preview" value="{{ $themeColor->text_body }}" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=text_body]').value = this.value">
                        <input type="text" name="text_body" value="{{ old('text_body', $themeColor->text_body) }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Muted Text</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="text_muted_preview" value="{{ $themeColor->text_muted }}" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=text_muted]').value = this.value">
                        <input type="text" name="text_muted" value="{{ old('text_muted', $themeColor->text_muted) }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">White Text</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="text_white_preview" value="{{ $themeColor->text_white }}" class="w-12 h-12 rounded cursor-pointer border border-gray-300" onchange="document.querySelector('input[name=text_white]').value = this.value">
                        <input type="text" name="text_white" value="{{ old('text_white', $themeColor->text_white) }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
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
                        <input type="color" name="ui_primary_preview" value="{{ $themeColor->ui_primary }}" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=ui_primary]').value = this.value">
                        <input type="text" name="ui_primary" value="{{ old('ui_primary', $themeColor->ui_primary) }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Success UI</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="ui_success_preview" value="{{ $themeColor->ui_success }}" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=ui_success]').value = this.value">
                        <input type="text" name="ui_success" value="{{ old('ui_success', $themeColor->ui_success) }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Warning UI</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="ui_warning_preview" value="{{ $themeColor->ui_warning }}" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=ui_warning]').value = this.value">
                        <input type="text" name="ui_warning" value="{{ old('ui_warning', $themeColor->ui_warning) }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Error UI</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="ui_error_preview" value="{{ $themeColor->ui_error }}" class="w-12 h-12 rounded cursor-pointer" onchange="document.querySelector('input[name=ui_error]').value = this.value">
                        <input type="text" name="ui_error" value="{{ old('ui_error', $themeColor->ui_error) }}" class="input-field flex-1" pattern="^#[0-9A-Fa-f]{6}$" required>
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
                    <input type="text" name="gradient_primary" value="{{ old('gradient_primary', $themeColor->gradient_primary) }}" class="input-field" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Secondary Gradient</label>
                    <input type="text" name="gradient_secondary" value="{{ old('gradient_secondary', $themeColor->gradient_secondary) }}" class="input-field" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-heading mb-2">Hero Gradient</label>
                    <input type="text" name="gradient_hero" value="{{ old('gradient_hero', $themeColor->gradient_hero) }}" class="input-field" required>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_active" value="1" {{ $themeColor->is_active ? 'checked' : '' }} class="accent-brand-gold">
                <span class="text-sm font-medium text-text-heading">Set as active theme (will deactivate current theme)</span>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.theme-colors.index') }}" class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-medium transition-all duration-300">Cancel</a>
            <button type="submit" class="bg-gradient-to-r from-brand-gold via-brand-amber to-brand-crimson text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">Update Theme</button>
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
