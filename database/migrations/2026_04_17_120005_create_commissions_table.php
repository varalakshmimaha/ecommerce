<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('beneficiary_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('beneficiary_role', ['affiliate', 'rm', 'manager']);
            $table->decimal('base_amount', 12, 2);
            $table->decimal('percentage', 5, 2);
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'approved', 'paid', 'reversed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('payout_ref')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['beneficiary_user_id', 'status']);
            $table->index('order_id');
            $table->index(['beneficiary_role', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
