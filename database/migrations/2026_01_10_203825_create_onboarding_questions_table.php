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
        Schema::create('onboarding_questions', function (Blueprint $table) {
            $table->id();
            $table->string('step_id')->unique(); // e.g., 'sector', 'team_size'
            $table->string('question');
            $table->string('subtext')->nullable();
            $table->enum('type', ['radio', 'checkbox', 'text'])->default('radio');
            $table->json('options')->nullable(); // Array of strings
            $table->boolean('has_other')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_questions');
    }
};
