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
        // Insert design settings
        DB::table('settings')->insert([
            // Design Settings
            [
                'key' => 'proposal_design_primary_color',
                'value' => '#111827',
                'group' => 'proposal_design',
                'type' => 'color',
                'label' => 'Ana Renk',
                'description' => 'Başlıklar, butonlar ve vurgular için kullanılan ana renk.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'proposal_design_secondary_color',
                'value' => '#6B7280',
                'group' => 'proposal_design',
                'type' => 'color',
                'label' => 'İkincil Renk',
                'description' => 'Alt başlıklar ve detay metinleri için kullanılan renk.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'proposal_design_title',
                'value' => 'TEKLİF',
                'group' => 'proposal_design',
                'type' => 'text',
                'label' => 'Doküman Başlığı',
                'description' => 'Dokümanın sağ üst köşesindeki ana başlık (Örn: TEKLİF, PROFORMA).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'key' => 'proposal_design_show_prepared_by',
                'value' => '1',
                'group' => 'proposal_design',
                'type' => 'boolean',
                'label' => 'Hazırlayan Bilgisi',
                'description' => 'Hazırlayan kişinin adını ve e-postasını göster.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'proposal_design_compact_mode',
                'value' => '0',
                'group' => 'proposal_design',
                'type' => 'boolean',
                'label' => 'Kompakt Mod',
                'description' => 'Daha sıkışık bir görünüm kullan.',
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
        DB::table('settings')->where('group', 'proposal_design')->delete();
    }
};
