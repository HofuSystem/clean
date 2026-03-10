<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Core\Pages\Models\Comparison;

class ComparisonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $comparisons = [
            [
                'order' => 0,
                'is_active' => true,
                'ar' => [
                    'point' => 'حماية القطع (الضياع)',
                    'us_text' => 'تتبع ذكي + تصوير (مستحيل تضيع)',
                    'them_text' => 'يضيعون قطع وينسون قطع',
                ],
                'en' => [
                    'point' => 'Item Protection',
                    'us_text' => 'Smart Tracking',
                    'them_text' => 'Lose items',
                ],
            ],
            [
                'order' => 1,
                'is_active' => true,
                'ar' => [
                    'point' => 'دقة الفرز (التبديل)',
                    'us_text' => 'غسالة خاصة = لا تبديل نهائياً',
                    'them_text' => 'يبدلون قطعك مع غيرك',
                ],
                'en' => [
                    'point' => 'Sorting',
                    'us_text' => 'Private Machine',
                    'them_text' => 'Swap items',
                ],
            ],
            [
                'order' => 2,
                'is_active' => true,
                'ar' => [
                    'point' => 'سياسة التعويض',
                    'us_text' => 'ضمان شامل وتعويض فوري',
                    'them_text' => 'غير مسؤولين ومايعوضك',
                ],
                'en' => [
                    'point' => 'Refund',
                    'us_text' => 'Instant Refund',
                    'them_text' => 'Not Responsible',
                ],
            ],
            [
                'order' => 3,
                'is_active' => true,
                'ar' => [
                    'point' => 'خدمة العملاء',
                    'us_text' => 'معك 24 ساعة (يردون بسرعة)',
                    'them_text' => 'مايردون عليك (تجاهل)',
                ],
                'en' => [
                    'point' => 'Support',
                    'us_text' => '24/7 Support',
                    'them_text' => 'No Reply',
                ],
            ],
        ];

        foreach ($comparisons as $comparisonData) {
            Comparison::create([
                'order' => $comparisonData['order'],
                'is_active' => $comparisonData['is_active'],
                'ar' => $comparisonData['ar'],
                'en' => $comparisonData['en'],
            ]);
        }
    }
}

