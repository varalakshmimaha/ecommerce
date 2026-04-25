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
        Schema::table('affiliate_profiles', function (Blueprint $table) {
            $table->renameColumn('aadhaar_last4', 'aadhaar_number');
        });
        Schema::table('affiliate_profiles', function (Blueprint $table) {
            $table->string('aadhaar_number', 12)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_profiles', function (Blueprint $table) {
            $table->string('aadhaar_number', 4)->nullable()->change();
        });
        Schema::table('affiliate_profiles', function (Blueprint $table) {
            $table->renameColumn('aadhaar_number', 'aadhaar_last4');
        });
    }
};
