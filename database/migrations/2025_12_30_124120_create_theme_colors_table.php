<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('theme_colors', function (Blueprint $table) {
            $table->id();
            
            // Brand Colors
            $table->string('brand_gold')->default('#F4B41A');
            $table->string('brand_amber')->default('#F28C28');
            $table->string('brand_burnt')->default('#E36F2D');
            $table->string('brand_crimson')->default('#C73A2B');
            
            // Text Colors
            $table->string('text_heading')->default('#1A1A1A');
            $table->string('text_body')->default('#374151');
            $table->string('text_muted')->default('#6B6B6B');
            $table->string('text_white')->default('#FFFFFF');
            
            // Surface Colors
            $table->string('surface_primary')->default('#FFFFFF');
            $table->string('surface_secondary')->default('#F9FAFB');
            $table->string('surface_light')->default('#F3F4F6');
            $table->string('surface_medium')->default('#E5E7EB');
            $table->string('surface_dark')->default('#111827');
            
            // UI Colors
            $table->string('ui_primary')->default('#3B82F6');
            $table->string('ui_success')->default('#10B981');
            $table->string('ui_warning')->default('#F59E0B');
            $table->string('ui_error')->default('#EF4444');
            $table->string('ui_info')->default('#06B6D4');
            $table->string('ui_hover')->default('#F3F4F6');
            $table->string('ui_border')->default('#D1D5DB');
            $table->string('ui_focus')->default('#F4B41A');
            
            // Gradients
            $table->string('gradient_primary')->default('from-brand-gold via-brand-amber to-brand-crimson');
            $table->string('gradient_secondary')->default('from-brand-amber via-brand-burnt to-brand-crimson');
            $table->string('gradient_hero')->default('from-brand-gold via-brand-amber to-brand-crimson');
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->string('name')->nullable(); // Theme name for identification
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_colors');
    }
};
