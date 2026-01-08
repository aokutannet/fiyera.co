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
        // Remove the code editor setting
        DB::table('settings')->where('key', 'proposal_template_code')->delete();

        // Default Layout Configuration
        // Defines the default order and visibility of sections
        $defaultLayout = json_encode([
            [
                'id' => 'header',
                'title' => 'Üst Bilgi (Logo & Başlık)',
                'visible' => true,
                'order' => 1
            ],
            [
                'id' => 'separator_1',
                'title' => 'Ayırıcı Çizgi',
                'visible' => true,
                'order' => 2
            ],
            [
                'id' => 'recipient',
                'title' => 'Alıcı Bilgileri',
                'visible' => true,
                'order' => 3
            ],
            [
                'id' => 'items',
                'title' => 'Ürün/Hizmet Tablosu',
                'visible' => true,
                'order' => 4
            ],
            [
                'id' => 'summary',
                'title' => 'Toplamlar & Özet',
                'visible' => true,
                'order' => 5
            ],
            [
                'id' => 'notes',
                'title' => 'Notlar',
                'visible' => true,
                'order' => 6
            ],
            [
                'id' => 'footer',
                'title' => 'Alt Bilgi',
                'visible' => true,
                'order' => 7
            ]
        ]);

        DB::table('settings')->insert([
            [
                'key' => 'proposal_layout',
                'value' => $defaultLayout,
                'group' => 'proposal_design',
                'type' => 'json',
                'label' => 'Teklif Düzeni',
                'description' => 'Sürükle bırak ile teklif bölümlerini sıralayın.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Re-adding color settings since we deleted the group in previous migration
            [
                'key' => 'proposal_color_primary',
                'value' => '#111827',
                'group' => 'proposal_design',
                'type' => 'color',
                'label' => 'Ana Renk',
                'description' => 'Başlıklar ve vurgular için.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'proposal_color_secondary',
                'value' => '#6B7280',
                'group' => 'proposal_design',
                'type' => 'color',
                'label' => 'İkincil Renk',
                'description' => 'Metinler ve detaylar için.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['proposal_layout', 'proposal_color_primary', 'proposal_color_secondary'])->delete();
    }
};
