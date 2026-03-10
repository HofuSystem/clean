<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Core\Pages\Models\WorkStep;

class WorkStepsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $steps = [
            [
                'icon' => 'fa-solid fa-mobile-screen-button',
                'order' => 0,
                'is_active' => true,
                'ar' => [
                    'title' => '1. الطلب الذكي',
                    'description' => 'تحديد الموعد والموقع عبر التطبيق في ثوانٍ.',
                ],
                'en' => [
                    'title' => '1. Smart Order',
                    'description' => 'Select time and location via app.',
                ],
            ],
            [
                'icon' => 'fa-solid fa-door-closed',
                'order' => 1,
                'is_active' => true,
                'ar' => [
                    'title' => '2. علقها على الباب',
                    'description' => 'ضع ملابسك في الكيس وعلقها على مقبض الباب.',
                ],
                'en' => [
                    'title' => '2. Hang on Door',
                    'description' => 'Put clothes in bag and hang.',
                ],
            ],
            [
                'icon' => 'fa-solid fa-truck-fast',
                'order' => 2,
                'is_active' => true,
                'ar' => [
                    'title' => '3. الاستلام الصامت',
                    'description' => 'مندوبنا الرسمي يستلم الكيس بهدوء تام.',
                ],
                'en' => [
                    'title' => '3. Silent Pickup',
                    'description' => 'We collect silently.',
                ],
            ],
            [
                'icon' => 'fa-solid fa-soap',
                'order' => 3,
                'is_active' => true,
                'ar' => [
                    'title' => '4. الغسيل والمعالجة',
                    'description' => 'غسيل منفصل 100% لكل عميل.',
                ],
                'en' => [
                    'title' => '4. Washing',
                    'description' => '100% separate washing.',
                ],
            ],
            [
                'icon' => 'fa-solid fa-shirt',
                'order' => 4,
                'is_active' => true,
                'ar' => [
                    'title' => '5. الكوي والفرز',
                    'description' => 'كي بالبخار، فحص الجودة، وتغليف فاخر.',
                ],
                'en' => [
                    'title' => '5. Ironing',
                    'description' => 'Steam ironing & quality check.',
                ],
            ],
            [
                'icon' => 'fa-solid fa-check',
                'order' => 5,
                'is_active' => true,
                'ar' => [
                    'title' => '6. ترجع لك معلقة',
                    'description' => 'تصلك جديدة للباب، وتُعلق بانتظارك.',
                ],
                'en' => [
                    'title' => '6. Returned Hung',
                    'description' => 'Arrives fresh to your door.',
                ],
            ],
        ];

        foreach ($steps as $stepData) {
            WorkStep::create([
                'icon' => $stepData['icon'],
                'order' => $stepData['order'],
                'is_active' => $stepData['is_active'],
                'ar' => $stepData['ar'],
                'en' => $stepData['en'],
            ]);
        }
    }
}

