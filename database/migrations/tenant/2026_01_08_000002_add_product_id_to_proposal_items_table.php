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
        if (!Schema::hasColumn('proposal_items', 'product_id')) {
            Schema::table('proposal_items', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id')->nullable()->after('proposal_id');
                $table->index('product_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposal_items', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
    }
};
