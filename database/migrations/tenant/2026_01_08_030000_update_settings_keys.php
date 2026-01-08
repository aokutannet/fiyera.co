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
        // Delete old keys
        DB::table('settings')->whereIn('key', ['site_title'])->delete();

        // Insert new keys
        $newSettings = [
            // General - Detailed Company Info
            [
                'key' => 'tax_title',
                'value' => '',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Vergi Ünvanı',
                'description' => 'Resmi fatura ünvanı.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'province',
                'value' => '',
                'group' => 'general',
                'type' => 'text',
                'label' => 'İl',
                'description' => 'Şirket merkezi ili.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'district',
                'value' => '',
                'group' => 'general',
                'type' => 'text',
                'label' => 'İlçe',
                'description' => 'Şirket merkezi ilçesi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_person',
                'value' => '',
                'group' => 'general',
                'type' => 'text',
                'label' => 'İlgili Kişi',
                'description' => 'Şirket yetkilisi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Logo
            [
                'key' => 'logo_path',
                'value' => '',
                'group' => 'logo',
                'type' => 'file',
                'label' => 'Firma Logosu',
                'description' => 'Teklif ve faturalarda kullanılacak logo (PNG/JPG).',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Mail
            [
                'key' => 'mail_provider',
                'value' => 'smtp',
                'group' => 'email',
                'type' => 'select',
                'label' => 'E-Posta Sağlayıcı',
                'description' => 'Kullanılacak e-posta altyapısı.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mail_host',
                'value' => '',
                'group' => 'email',
                'type' => 'text',
                'label' => 'SMTP Host',
                'description' => 'Örn: smtp.yandex.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mail_port',
                'value' => '587',
                'group' => 'email',
                'type' => 'number',
                'label' => 'SMTP Port',
                'description' => 'Genellikle 587 veya 465.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mail_username',
                'value' => '',
                'group' => 'email',
                'type' => 'text',
                'label' => 'Kullanıcı Adı (Email)',
                'description' => 'SMTP kullanıcı adı.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mail_password',
                'value' => '',
                'group' => 'email',
                'type' => 'password',
                'label' => 'Şifre',
                'description' => 'SMTP şifresi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mail_encryption',
                'value' => 'tls',
                'group' => 'email',
                'type' => 'select',
                'label' => 'Şifreleme',
                'description' => 'TLS veya SSL.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mail_from_address',
                'value' => '',
                'group' => 'email',
                'type' => 'text',
                'label' => 'Gönderen Adresi',
                'description' => 'E-postaların gönderileceği adres.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'mail_from_name',
                'value' => '',
                'group' => 'email',
                'type' => 'text',
                'label' => 'Gönderen Adı',
                'description' => 'E-postaların görüneceği isim.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SMS - Netgsm
            [
                'key' => 'sms_provider',
                'value' => 'netgsm',
                'group' => 'sms',
                'type' => 'text',
                'label' => 'SMS Sağlayıcı',
                'description' => 'Varsayılan: Netgsm',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'sms_username',
                'value' => '',
                'group' => 'sms',
                'type' => 'text',
                'label' => 'Netgsm Kullanıcı Adı',
                'description' => '850... ile başlayan numaranız.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'sms_password',
                'value' => '',
                'group' => 'sms',
                'type' => 'password',
                'label' => 'Netgsm Şifre',
                'description' => 'API şifreniz.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'sms_header',
                'value' => '',
                'group' => 'sms',
                'type' => 'text',
                'label' => 'SMS Başlığı',
                'description' => 'Onaylı gönderici başlığınız.',
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
        // No simple rollback for data changes, but typically we'd delete the added keys
    }
};
