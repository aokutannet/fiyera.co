<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price_monthly',
        'price_yearly',
        'features',
        'limits',
        'is_popular',
        'description',
    ];

    protected $casts = [
        'features' => 'array',
        'limits' => 'array',
        'is_popular' => 'boolean',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'subscription_plan_id');
    }

    public static function getAvailableFeatures()
    {
        return [
            'proposal_creation' => 'Teklif oluşturma',
            'pdf_export' => 'PDF çıktı',
            'status_tracking' => 'Teklif durumu takibi',
            'vat_calculation' => 'KDV hesaplama',
            'email_sending' => 'Mail gönderimi',
            'basic_dashboard' => 'Temel dashboard',
            'copy_proposal' => 'Teklif kopyalama',
            'advanced_dashboard' => 'Gelişmiş dashboard',
            'excel_import_export' => 'Excel import/export',
            'bulk_price_update' => 'Toplu fiyat güncelleme',
            'whatsapp_share' => 'WhatsApp paylaşım',
            'advanced_reports' => 'Gelişmiş raporlar',
            'pdf_customization' => 'PDF şablon özelleştirme',
            'logo_signature' => 'Logo & imza ekleme',
            'validity_period' => 'Teklif geçerlilik süresi',
            'approval_notes' => 'Onay / red notları',
            'auto_numbering' => 'Otomatik teklif numaralandırma',
            'ai_creation' => 'AI teklif oluşturma',
            'proposal_writing' => 'Teklif açıklaması yazma',
            'product_suggestion' => 'Ürün/hizmet önerisi',
            'price_suggestion' => 'Fiyat aralığı önerisi',
            'ai_improvement' => 'AI destekli iyileştirme',
            'priority_support' => 'Öncelikli destek',
            'early_access' => 'Erken erişim (beta)',
            'sms_sending' => 'SMS ile teklif gönderme',
            'netgsm_integration' => 'Netgsm entegrasyonu',
            'email_integration' => 'Mail entegrasyonu',
            'online_proposal_link' => 'Online teklif linki',
            'category_creation' => 'Kategori oluşturma',
        ];
    }
}
