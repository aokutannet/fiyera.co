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
        Schema::create('subscription_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('order_code')->unique()->index();
            $table->string('billing_period'); // monthly, yearly
            $table->decimal('price', 10, 2);
            $table->string('currency')->default('TRY');
            $table->string('status')->default('pending'); // pending, success, failed, cancelled
            $table->text('error_message')->nullable();
            $table->json('payment_metadata')->nullable(); // Store detailed response (masked)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_attempts');
    }
};
