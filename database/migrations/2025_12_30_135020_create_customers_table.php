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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('total_orders')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->timestamp('last_order_at')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['is_active']);
            $table->index(['created_at']);
            $table->index(['last_order_at']);
            $table->index(['total_orders']);
            $table->index(['total_spent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
