<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'manager', 'rm', 'affiliate', 'customer'])->default('customer')->after('is_admin');
            $table->foreignId('parent_id')->nullable()->after('role')->constrained('users')->nullOnDelete();
            $table->string('referral_code', 20)->nullable()->unique()->after('parent_id');
            $table->enum('affiliate_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('referral_code');
            $table->timestamp('approved_at')->nullable()->after('affiliate_status');

            $table->index(['role', 'parent_id']);
            $table->index('affiliate_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['role', 'parent_id']);
            $table->dropIndex(['affiliate_status']);
            $table->dropUnique(['referral_code']);
            $table->dropColumn(['role', 'parent_id', 'referral_code', 'affiliate_status', 'approved_at']);
        });
    }
};
