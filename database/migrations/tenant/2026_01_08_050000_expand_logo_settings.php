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
        // Delete old single logo key
        DB::table('settings')->where('key', 'logo_path')->delete();

        $newSettings = [
            [
                'key' => 'company_logo_png',
                'value' => '',
                'group' => 'logo',
                'type' => 'file',
                'label' => 'Firma Logosu (PNG)',
                'description' => 'Şeffaf arkaplanlı yüksek çözünürlüklü logo.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_logo_jpg',
                'value' => '',
                'group' => 'logo',
                'type' => 'file',
                'label' => 'Firma Logosu (JPG)',
                'description' => 'Beyaz arkaplanlı, profil resmi vb. için.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'proposal_logo',
                'value' => '',
                'group' => 'logo',
                'type' => 'file',
                'label' => 'Teklif Logosu',
                'description' => 'Teklif PDF\'lerinde görünecek özel logo.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('settings')->insert($newSettings);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['company_logo_png', 'company_logo_jpg', 'proposal_logo'])->delete();
    }
};
