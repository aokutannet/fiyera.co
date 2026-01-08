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
            $table->json('billing_details')->nullable()->after('onboarding_completed');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('billing_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('billing_details');
        });
        
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
