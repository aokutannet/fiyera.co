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
        if (!Schema::hasColumn('products', 'tenant_id')) {
            Schema::table('products', function (Blueprint $table) {
                // Assuming tenant_id matches the users/tenants ID type. Usually bigInteger.
                // It's often good to index it.
                $table->unsignedBigInteger('tenant_id')->after('id')->nullable(); 
                
                // If you want foreign key constraint, it depends if 'tenants' table is in the same DB.
                // Usually in separate-DB multi-tenancy, 'tenants' is in main DB, so no FK.
                $table->index('tenant_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('tenant_id');
        });
    }
};
