<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change ends_at from TIMESTAMP to DATETIME to support dates beyond 2038
        // Using raw SQL because doctrine/dbal might not be installed
        DB::statement('ALTER TABLE subscriptions MODIFY starts_at DATETIME NULL');
        DB::statement('ALTER TABLE subscriptions MODIFY ends_at DATETIME NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to TIMESTAMP if needed (though unlikely to want to revert this limitation)
        DB::statement('ALTER TABLE subscriptions MODIFY starts_at TIMESTAMP NULL');
        DB::statement('ALTER TABLE subscriptions MODIFY ends_at TIMESTAMP NULL');
    }
};
