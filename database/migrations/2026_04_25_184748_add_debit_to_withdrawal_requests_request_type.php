<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE withdrawal_requests MODIFY COLUMN request_type ENUM('withdrawal','credit','debit') NOT NULL DEFAULT 'withdrawal'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE withdrawal_requests MODIFY COLUMN request_type ENUM('withdrawal','credit') NOT NULL DEFAULT 'withdrawal'");
    }
};
