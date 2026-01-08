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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->index()->default('general');
            $table->string('type')->default('text'); // text, textarea, boolean, select, number
            $table->string('label');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            // General Settings
            [
                'key' => 'site_title',
                'value' => 'fiyera',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Site Başlığı',
                'description' => 'Uygulamanın tarayıcı sekmesinde görünen adı.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_name',
                'value' => 'Şirketim A.Ş.',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Şirket Adı',
                'description' => 'Faturalarda ve tekliflerde görünecek firma adı.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_address',
                'value' => '',
                'group' => 'general',
                'type' => 'textarea',
                'label' => 'Şirket Adresi',
                'description' => 'Firma adresi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_phone',
                'value' => '',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Telefon',
                'description' => 'Firma telefon numarası.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Proposal Settings
            [
                'key' => 'proposal_prefix',
                'value' => 'TEKLIF-',
                'group' => 'proposal',
                'type' => 'text',
                'label' => 'Teklif Öneki',
                'description' => 'Teklif numaralarının başına eklenecek metin.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'proposal_footer',
                'value' => 'Teklifimiz 30 gün geçerlidir.',
                'group' => 'proposal',
                'type' => 'textarea',
                'label' => 'Teklif Alt Bilgisi',
                'description' => 'Her teklifin altında varsayılan olarak görünecek metin.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Invoice Settings (Placeholder for future)
            [
                'key' => 'currency_symbol',
                'value' => '₺',
                'group' => 'invoice',
                'type' => 'text',
                'label' => 'Para Birimi Sembolü',
                'description' => 'Fiyatlarda kullanılacak para birimi sembolü.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
