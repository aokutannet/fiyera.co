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
        Schema::table('tenants', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Try to update ENUM for MySQL/PostgreSQL
        try {
            DB::statement("ALTER TABLE tenants MODIFY COLUMN status ENUM('active', 'passive', 'deleted') DEFAULT 'active'");
        } catch (\Exception $e) {
            // Fallback or ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        try {
            DB::statement("ALTER TABLE tenants MODIFY COLUMN status ENUM('active', 'passive') DEFAULT 'active'");
        } catch (\Exception $e) {
        }
    }
};
