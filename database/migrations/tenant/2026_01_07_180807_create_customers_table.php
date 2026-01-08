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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id'); // No FK to main DB
            $table->string('company_name'); // Firma Adı
            $table->string('contact_person')->nullable(); // İlgili Kişi
            $table->string('category')->nullable(); // Kategori
            $table->string('landline_phone')->nullable(); // Sabit Tel
            $table->string('mobile_phone')->nullable(); // Mobil Tel
            $table->string('legal_title')->nullable(); // Firma Ünvanı
            $table->text('address')->nullable(); // Adres
            $table->string('country')->default('Türkiye'); // Ülke
            $table->string('city')->nullable(); // İl
            $table->string('district')->nullable(); // İlçe
            $table->enum('type', ['individual', 'legal'])->default('legal'); // TÜRÜ (Gerçek / Tüzel)
            $table->string('tax_number')->nullable(); // VKN / TCKN
            $table->string('tax_office')->nullable(); // Vergi Dairesi
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
