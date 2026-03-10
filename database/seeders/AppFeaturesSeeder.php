<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Core\Pages\Models\Feature;

class AppFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // B2C Features (Business to Consumer)
        $b2cFeatures = [
            [
                'icon' => 'fa-solid fa-wallet',
                'section' => 'b2c',
                'is_active' => true,
                'ar' => [
                    'title' => 'المحفظة الذكية',
                    'description' => 'اشحن رصيدك واستمتع برصيد مجاني إضافي فوراً.',
                ],
                'en' => [
                    'title' => 'Smart Wallet',
                    'description' => 'Top up & get bonus credit.',
                ],
            ],
            [
                'icon' => 'fa-solid fa-gift',
                'section' => 'b2c',
                'is_active' => true,
                'ar' => [
                    'title' => 'نقاط الولاء',
                    'description' => 'كل ريال = نقطة. جمع النقاط واستبدلها بخدمات مجانية...',
                ],
                'en' => [
                    'title' => 'Loyalty Points',
                    'description' => '1 SAR = 1 Point.',
                ],
            ],
            [
                'icon' => 'fa-solid fa-map-location-dot',
                'section' => 'b2c',
                'is_active' => true,
                'ar' => [
                    'title' => 'تتبع لحظي',
                    'description' => 'تابع المندوب على الخريطة من الاستلام حتى التسليم.',
                ],
                'en' => [
                    'title' => 'Live Tracking',
                    'description' => 'Track driver on map.',
                ],
            ],
            [
                'icon' => 'fa-solid fa-tags',
                'section' => 'b2c',
                'is_active' => true,
                'ar' => [
                    'title' => 'شفافية الأسعار',
                    'description' => 'تعرف على تكلفة كل قطعة قبل الطلب. لا مفاجآت.',
                ],
                'en' => [
                    'title' => 'Transparent Pricing',
                    'description' => 'Know cost before order.',
                ],
            ],
        ];

        // B2B Features (Business to Business)
        $b2bFeatures = [
            [
                'icon' => 'fa-solid fa-hotel',
                'section' => 'b2b',
                'is_active' => true,
                'ar' => [
                    'title' => 'الفنادق والشقق',
                    'description' => 'غسيل البياضات والشراشف بمعايير فندقية 5 نجوم.',
                ],
                'en' => [
                    'title' => 'Hotels',
                    'description' => '5-star standards.',
                ],
            ],
            [
                'icon' => 'fa-solid fa-hospital',
                'section' => 'b2b',
                'is_active' => true,
                'ar' => [
                    'title' => 'المستشفيات والعيادات',
                    'description' => 'تعقيم طبي معتمد للملابس والستائر.',
                ],
                'en' => [
                    'title' => 'Hospitals',
                    'description' => 'Medical sterilization.',
                ],
            ],
            [
                'icon' => 'fa-solid fa-scissors',
                'section' => 'b2b',
                'is_active' => true,
                'ar' => [
                    'title' => 'صالونات التجميل',
                    'description' => 'تنظيف المناشف والأرواب وإزالة بقع الصبغات.',
                ],
                'en' => [
                    'title' => 'Salons',
                    'description' => 'Towels & robes cleaning.',
                ],
            ],
            [
                'icon' => 'fa-solid fa-dumbbell',
                'section' => 'b2b',
                'is_active' => true,
                'ar' => [
                    'title' => 'النوادي الرياضية',
                    'description' => 'تعقيم مستمر للمناشف لضمان صحة المشتركين.',
                ],
                'en' => [
                    'title' => 'Gyms',
                    'description' => 'Continuous sanitization.',
                ],
            ],
        ];

        // Merge all features
        $allFeatures = array_merge($b2cFeatures, $b2bFeatures);

        foreach ($allFeatures as $featureData) {
            Feature::create([
                'icon' => $featureData['icon'],
                'section' => $featureData['section'],
                'is_active' => $featureData['is_active'],
                'ar' => $featureData['ar'],
                'en' => $featureData['en'],
            ]);
        }
    }
}

