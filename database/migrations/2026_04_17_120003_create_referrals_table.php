<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('referral_code', 20);
            $table->enum('source', ['link', 'manual_code'])->default('link');
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index('referrer_user_id');
            $table->index('referred_user_id');
            $table->index('referral_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
