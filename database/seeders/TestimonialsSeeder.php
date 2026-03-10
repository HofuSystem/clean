<?php

namespace Database\Seeders;

use Core\Pages\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing testimonials
        Testimonial::query()->delete();

        $testimonials = [
            [
                'avatar' => null,
                'is_active' => true,
                'rating' => 5,
                'translations' => [
                    'ar' => [
                        'name' => 'أحمد محمد',
                        'title' => 'عميل دائم',
                        'body' => 'خدمة ممتازة وسريعة! ملابسي عادت نظيفة ومكوية بشكل رائع. أنصح الجميع بتجربة كلين ستيشن.',
                        'location' => 'الرياض'
                    ],
                    'en' => [
                        'name' => 'Ahmed Mohammed',
                        'title' => 'Regular Customer',
                        'body' => 'Excellent and fast service! My clothes came back clean and perfectly ironed. I recommend everyone to try Clean Station.',
                        'location' => 'Riyadh'
                    ]
                ]
            ],
            [
                'avatar' => null,
                'is_active' => true,
                'rating' => 5,
                'translations' => [
                    'ar' => [
                        'name' => 'سارة العتيبي',
                        'title' => 'ربة منزل',
                        'body' => 'أفضل خدمة غسيل جربتها! التطبيق سهل الاستخدام والتوصيل في الموعد المحدد. شكراً كلين ستيشن.',
                        'location' => 'جدة'
                    ],
                    'en' => [
                        'name' => 'Sarah Al-Otaibi',
                        'title' => 'Housewife',
                        'body' => 'Best laundry service I have tried! The app is easy to use and delivery is on time. Thank you Clean Station.',
                        'location' => 'Jeddah'
                    ]
                ]
            ],
            [
                'avatar' => null,
                'is_active' => true,
                'rating' => 5,
                'translations' => [
                    'ar' => [
                        'name' => 'خالد الشمري',
                        'title' => 'رجل أعمال',
                        'body' => 'خدمة احترافية جداً. بدلاتي دائماً تعود بحالة ممتازة. أقدر الاهتمام بالتفاصيل.',
                        'location' => 'الدمام'
                    ],
                    'en' => [
                        'name' => 'Khalid Al-Shammari',
                        'title' => 'Businessman',
                        'body' => 'Very professional service. My suits always come back in excellent condition. I appreciate the attention to detail.',
                        'location' => 'Dammam'
                    ]
                ]
            ],
            [
                'avatar' => null,
                'is_active' => true,
                'rating' => 4,
                'translations' => [
                    'ar' => [
                        'name' => 'نورة السالم',
                        'title' => 'موظفة',
                        'body' => 'وفرت علي الكثير من الوقت والجهد. خدمة الاستلام والتسليم مريحة جداً خاصة مع جدولي المزدحم.',
                        'location' => 'الرياض'
                    ],
                    'en' => [
                        'name' => 'Noura Al-Salem',
                        'title' => 'Employee',
                        'body' => 'Saved me a lot of time and effort. The pickup and delivery service is very convenient especially with my busy schedule.',
                        'location' => 'Riyadh'
                    ]
                ]
            ],
            [
                'avatar' => null,
                'is_active' => true,
                'rating' => 5,
                'translations' => [
                    'ar' => [
                        'name' => 'فهد القحطاني',
                        'title' => 'عميل جديد',
                        'body' => 'جربت الخدمة لأول مرة وكانت التجربة رائعة. سأكون عميلاً دائماً بالتأكيد.',
                        'location' => 'مكة'
                    ],
                    'en' => [
                        'name' => 'Fahad Al-Qahtani',
                        'title' => 'New Customer',
                        'body' => 'Tried the service for the first time and the experience was amazing. I will definitely be a regular customer.',
                        'location' => 'Makkah'
                    ]
                ]
            ],
            [
                'avatar' => null,
                'is_active' => true,
                'rating' => 5,
                'translations' => [
                    'ar' => [
                        'name' => 'منى الحربي',
                        'title' => 'أم لثلاثة أطفال',
                        'body' => 'مع أطفالي الثلاثة، الغسيل كان كابوساً! كلين ستيشن غيرت حياتي. الآن أستمتع بوقتي مع عائلتي.',
                        'location' => 'الخبر'
                    ],
                    'en' => [
                        'name' => 'Mona Al-Harbi',
                        'title' => 'Mother of Three',
                        'body' => 'With my three kids, laundry was a nightmare! Clean Station changed my life. Now I enjoy my time with my family.',
                        'location' => 'Khobar'
                    ]
                ]
            ],
            [
                'avatar' => null,
                'is_active' => true,
                'rating' => 5,
                'translations' => [
                    'ar' => [
                        'name' => 'عبدالله الدوسري',
                        'title' => 'طالب جامعي',
                        'body' => 'أسعار معقولة وجودة عالية. كطالب، هذا بالضبط ما أحتاجه. شكراً على الخدمة الممتازة!',
                        'location' => 'الرياض'
                    ],
                    'en' => [
                        'name' => 'Abdullah Al-Dosari',
                        'title' => 'University Student',
                        'body' => 'Reasonable prices and high quality. As a student, this is exactly what I need. Thanks for the excellent service!',
                        'location' => 'Riyadh'
                    ]
                ]
            ],
            [
                'avatar' => null,
                'is_active' => true,
                'rating' => 4,
                'translations' => [
                    'ar' => [
                        'name' => 'هند المالكي',
                        'title' => 'مصممة أزياء',
                        'body' => 'يتعاملون مع الأقمشة الحساسة بعناية فائقة. أثق بهم في تنظيف قطعي الثمينة.',
                        'location' => 'جدة'
                    ],
                    'en' => [
                        'name' => 'Hind Al-Maliki',
                        'title' => 'Fashion Designer',
                        'body' => 'They handle delicate fabrics with extreme care. I trust them with my precious pieces.',
                        'location' => 'Jeddah'
                    ]
                ]
            ],
        ];

        foreach ($testimonials as $data) {
            $testimonial = Testimonial::create([
                'avatar' => $data['avatar'],
                'is_active' => $data['is_active'],
                'rating' => $data['rating'],
            ]);

            foreach ($data['translations'] as $locale => $translation) {
                $testimonial->translations()->create([
                    'locale' => $locale,
                    'name' => $translation['name'],
                    'title' => $translation['title'],
                    'body' => $translation['body'],
                    'location' => $translation['location'],
                ]);
            }
        }

        $this->command->info('Testimonials seeded successfully!');
    }
}

