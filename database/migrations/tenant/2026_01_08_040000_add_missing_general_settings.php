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
        $newSettings = [
            [
                'key' => 'country',
                'value' => 'Türkiye',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Ülke',
                'description' => 'Şirket merkezi ülkesi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tax_office',
                'value' => '',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Vergi Dairesi',
                'description' => 'Bağlı olunan vergi dairesi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tax_number',
                'value' => '',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Vergi Numarası',
                'description' => 'Vergi kimlik numarası.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_email',
                'value' => '',
                'group' => 'general',
                'type' => 'email',
                'label' => 'Şirket E-Posta',
                'description' => 'Genel iletişim e-postası.',
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
        DB::table('settings')->whereIn('key', ['country', 'tax_office', 'tax_number', 'company_email'])->delete();
    }
};
