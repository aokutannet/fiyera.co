<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Startup',
                'slug' => 'startup',
                'price_monthly' => 100,
                'price_yearly' => 1000,
                'limits' => [
                    'user_count' => 1,
                    'proposal_monthly' => 30,
                    'customer_count' => 50,
                    'product_count' => 50,
                ],
                'features' => [
                    'proposal_creation',
                    'pdf_export',
                    'status_tracking',
                    'vat_calculation',
                    'email_sending', // Basic email sending
                    'online_proposal_link', // New
                    'category_creation', // New
                    'basic_dashboard',
                ],
                'is_popular' => false,
                'description' => 'Yeni başlayanlar, bireysel kullanıcılar',
            ],
            [
                'name' => 'Başlangıç',
                'slug' => 'baslangic',
                'price_monthly' => 200,
                'price_yearly' => 2000,
                'limits' => [
                    'user_count' => 3,
                    'proposal_monthly' => 100,
                    'customer_count' => 200,
                    'product_count' => 200,
                ],
                'features' => [
                    'copy_proposal',
                    'advanced_dashboard',
                    'excel_import_export',
                    'bulk_price_update',
                    'whatsapp_share',
                    'email_integration', // New (SMTP etc)
                    'online_proposal_link',
                    'category_creation',
                    'proposal_creation', // Ensure base features are present if needed, or rely on UI logic to stack
                    'pdf_export', 
                    'status_tracking',
                    'vat_calculation',
                    'email_sending',
                ],
                'is_popular' => true,
                'description' => 'Küçük ekipler',
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'price_monthly' => 300,
                'price_yearly' => 3000,
                'limits' => [
                    'user_count' => 10,
                    'proposal_monthly' => 500,
                    'customer_count' => 1000,
                    'product_count' => 1000,
                ],
                'features' => [
                    'advanced_reports',
                    'pdf_customization',
                    'logo_signature',
                    'validity_period',
                    'approval_notes',
                    'auto_numbering',
                    'netgsm_integration', // New
                    'sms_sending', // New
                    'email_integration',
                    'online_proposal_link',
                    'category_creation',
                    'excel_import_export',
                    'bulk_price_update',
                    'whatsapp_share',
                    'copy_proposal',
                ],
                'is_popular' => false,
                'description' => 'Aktif satış yapan firmalar',
            ],
            [
                'name' => 'Cloud',
                'slug' => 'cloud',
                'price_monthly' => 500,
                'price_yearly' => 5000,
                'limits' => [
                    'user_count' => -1, // Unlimited
                    'proposal_monthly' => -1,
                    'customer_count' => -1,
                    'product_count' => -1,
                ],
                'features' => [
                    'ai_creation',
                    'proposal_writing',
                    'product_suggestion',
                    'price_suggestion',
                    'ai_improvement',
                    'priority_support',
                    'early_access',
                    'netgsm_integration',
                    'sms_sending',
                    'advanced_reports',
                    'pdf_customization',
                    'logo_signature',
                    'validity_period',
                ],
                'is_popular' => false,
                'description' => 'Kurumsal & ileri seviye kullanıcılar',
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
