<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->foreignId('withdrawal_id')->nullable()->after('payout_ref')->constrained('withdrawals')->nullOnDelete();
            $table->index('withdrawal_id');
        });
    }

    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->dropForeign(['withdrawal_id']);
            $table->dropIndex(['withdrawal_id']);
            $table->dropColumn('withdrawal_id');
        });
    }
};
