<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update tenants table
        Schema::table('tenants', function (Blueprint $table) {
            // Check if columns exist before adding them to avoid duplication issues during development
            if (!Schema::hasColumn('tenants', 'trial_starts_at')) {
                $table->timestamp('trial_starts_at')->nullable()->after('trial_ends_at');
            }
            if (!Schema::hasColumn('tenants', 'subscription_status')) {
                $table->enum('subscription_status', ['trial', 'active', 'expired', 'cancelled'])
                      ->default('trial')
                      ->after('status');
            }
        });

        // Create subscriptions table
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->enum('billing_period', ['monthly', 'yearly']);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->enum('status', ['active', 'cancelled', 'expired'])->default('active');
            $table->string('payment_provider')->nullable(); // e.g., 'stripe', 'iyzico'
            $table->string('payment_id')->nullable(); // External Reference ID
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['trial_starts_at', 'subscription_status']);
        });
    }
};
