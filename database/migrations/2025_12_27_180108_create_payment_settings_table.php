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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('payment_method', 50)->unique(); // manual, cod, razorpay
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // Store method-specific settings
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['payment_method', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
