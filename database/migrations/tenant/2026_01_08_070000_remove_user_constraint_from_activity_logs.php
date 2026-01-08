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
        try {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Error 1091: Can't DROP 'x'; check that column/key exists
            if ($e->errorInfo[1] !== 1091) {
                throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }
};
