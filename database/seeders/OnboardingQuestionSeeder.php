<?php

namespace Database\Seeders;

use App\Models\OnboardingQuestion;
use Illuminate\Database\Seeder;

class OnboardingQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            [
                'step_id' => 'sector',
                'question' => 'Firmanız hangi sektörde?',
                'options' => ['Ajans', 'İnşaat', 'Yazılım', 'Üretim', 'E-ticaret', 'Danışmanlık', 'Diğer'],
                'type' => 'radio',
                'has_other' => true,
                'order' => 1
            ],
            [
                'step_id' => 'team_size',
                'question' => 'Kaç kişilik bir ekibiniz var?',
                'options' => ['1–3', '4–10', '11–25', '25+'],
                'type' => 'radio',
                'order' => 2,
            ],
            [
                'step_id' => 'monthly_proposals',
                'question' => 'Ayda Kaç Teklif Hazırlıyorsunuz?',
                'options' => ['1–10', '10–50', '50–200', '200+'],
                'type' => 'radio',
                'order' => 3,
            ],
            [
                'step_id' => 'target_audience',
                'question' => 'Teklifleri en çok kime hazırlıyorsunuz?',
                'subtext' => 'Birden fazla seçebilirsiniz',
                'options' => ['Bireysel müşteriler', 'KOBİ', 'Kurumsal'],
                'type' => 'checkbox',
                'order' => 4,
            ],
            [
                'step_id' => 'currency',
                'question' => 'En Çok Kullandığınız Para Birimi',
                'subtext' => 'Birden fazla seçebilirsiniz',
                'options' => ['₺ TRY', '$ USD', '€ EUR'],
                'type' => 'checkbox',
                'order' => 5,
            ],
            [
                'step_id' => 'vat_usage',
                'question' => 'Teklifte KDV Kullanıyor musunuz?',
                'options' => ['Evet', 'Hayır'],
                'type' => 'radio',
                'order' => 6,
            ],
            [
                'step_id' => 'proposal_criteria',
                'question' => 'Teklifte en önemli kriteriniz ne?',
                'options' => ['Fiyat', 'Hız', 'Detaylı açıklama', 'Profesyonel görünüm'],
                'type' => 'radio',
                'order' => 7,
            ],
            [
                'step_id' => 'proposal_preparer',
                'question' => 'Teklifi kim hazırlıyor?',
                'options' => ['Satış', 'Patron', 'Ofis personeli'],
                'type' => 'radio',
                'order' => 8,
            ],
            [
                'step_id' => 'previous_software',
                'question' => 'Daha önce teklif yazılımı kullandınız mı?',
                'options' => ['Evet', 'Hayır'],
                'type' => 'radio',
                'order' => 9,
            ]
        ];

        foreach ($questions as $q) {
            OnboardingQuestion::updateOrCreate(['step_id' => $q['step_id']], $q);
        }
    }
}
