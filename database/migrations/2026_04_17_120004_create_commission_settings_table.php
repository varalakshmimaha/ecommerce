<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['affiliate', 'rm', 'manager'])->unique();
            $table->decimal('percentage', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        DB::table('commission_settings')->insert([
            ['role' => 'affiliate', 'percentage' => 5.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['role' => 'rm',        'percentage' => 3.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['role' => 'manager',   'percentage' => 2.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_settings');
    }
};
