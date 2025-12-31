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
        Schema::create('variation_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variation_id')->constrained('product_variations')->onDelete('cascade');
            $table->foreignId('product_attribute_id')->constrained('product_attributes')->onDelete('cascade');
            $table->foreignId('attribute_value_id')->constrained('product_attribute_values')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['variation_id', 'product_attribute_id', 'attribute_value_id'], 'var_attr_val_unique');
            $table->index(['variation_id', 'product_attribute_id'], 'var_attr_index');
            $table->index(['product_attribute_id', 'attribute_value_id'], 'attr_val_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variation_attribute_values');
    }
};
