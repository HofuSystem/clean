<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Preserve the six reviews previously hardcoded on the home page and make
     * them editable through the existing testimonials dashboard.
     */
    public function up(): void
    {
        if (!Schema::hasTable('testimonials') || !Schema::hasTable('testimonial_translations')) {
            return;
        }

        $reviews = [
            [
                'name' => ['ar' => 'سارة ابراهيم', 'en' => 'Sarah Ibrahim'],
                'body' => [
                    'ar' => 'نظافة الملابس روعة والريحة تجنن والتطبيق سهل جدا في الطلب وما ياخذ وقت صراحة انصح الكل يجربهم بدون تردد',
                    'en' => 'The clothes come back spotless and smell amazing, and ordering in the app is quick and easy. I recommend them to everyone',
                ],
                'label' => ['ar' => 'غسيل وكوي', 'en' => 'Wash & iron'],
            ],
            [
                'name' => ['ar' => 'د. سلطان', 'en' => 'Dr. Sultan'],
                'body' => [
                    'ar' => 'اكثر شي جذبني دقة المواعيد في الاستلام والتسليم وهذا شي نادر بصراحة والمناديب واضح انهم مدربين ومو عشوائيين ابدا في التعامل',
                    'en' => 'What won me over is how punctual pickup and delivery are, which is rare. The drivers are clearly well trained',
                ],
                'label' => ['ar' => 'الاستلام والتسليم', 'en' => 'Pickup & delivery'],
            ],
            [
                'name' => ['ar' => 'هند علي', 'en' => 'Hind Ali'],
                'body' => [
                    'ar' => 'تعامل احترافي مع الاقمشة الحساسة والقطع الراقية والكوي عندهم فنان صراحة',
                    'en' => 'Professional with delicate fabrics and premium pieces, and their ironing is an art',
                ],
                'label' => ['ar' => 'دراي كلين', 'en' => 'Dry cleaning'],
            ],
            [
                'name' => ['ar' => 'لولوة', 'en' => 'Lulwa'],
                'body' => [
                    'ar' => 'ريحة البطانيات تجنن وتفتح النفس',
                    'en' => 'The blankets smell wonderful',
                ],
                'label' => ['ar' => 'مفروشات', 'en' => 'Bedding'],
            ],
            [
                'name' => ['ar' => 'مساعد خالد', 'en' => 'Musaed Khalid'],
                'body' => [
                    'ar' => 'خدمة تبيض الوجه والمناديب مو عشوائيين في استلام الملابس وهذا اللي خلاني اثق فيهم',
                    'en' => "Excellent service, and the drivers are careful when collecting clothes. That's what made me trust them",
                ],
                'label' => ['ar' => 'غسيل وكوي', 'en' => 'Wash & iron'],
            ],
            [
                'name' => ['ar' => 'ماجد', 'en' => 'Majed'],
                'body' => [
                    'ar' => 'الشغل مرتب ونظفوا الاحذية بشكل ممتاز بس تمنيت لو كان فيه خيارات اكثر لتغليف الاحذية بدل الاكياس البلاستيك العادية لكن بشكل عام جودة التنظيف ممتازة',
                    'en' => "Neat work and the shoes came out great. I'd like more packaging options than plain plastic bags, but overall the cleaning quality is excellent",
                ],
                'label' => ['ar' => 'أحذية', 'en' => 'Shoes'],
            ],
        ];

        foreach ($reviews as $review) {
            $existingId = DB::table('testimonial_translations as translations')
                ->join('testimonials', 'testimonials.id', '=', 'translations.testimonial_id')
                ->whereNull('testimonials.deleted_at')
                ->where(function ($query) use ($review) {
                    $query->where(function ($query) use ($review) {
                        $query->where('translations.locale', 'ar')
                            ->where('translations.name', $review['name']['ar'])
                            ->where('translations.body', $review['body']['ar']);
                    })->orWhere(function ($query) use ($review) {
                        $query->where('translations.locale', 'en')
                            ->where('translations.name', $review['name']['en'])
                            ->where('translations.body', $review['body']['en']);
                    });
                })
                ->value('testimonials.id');

            $now = now();
            $testimonialId = $existingId ?: DB::table('testimonials')->insertGetId([
                'avatar' => null,
                'is_active' => true,
                'rating' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach (['ar', 'en'] as $locale) {
                $translationExists = DB::table('testimonial_translations')
                    ->where('testimonial_id', $testimonialId)
                    ->where('locale', $locale)
                    ->exists();

                if (!$translationExists) {
                    DB::table('testimonial_translations')->insert([
                        'testimonial_id' => $testimonialId,
                        'locale' => $locale,
                        'name' => $review['name'][$locale],
                        'title' => $review['label'][$locale],
                        'body' => $review['body'][$locale],
                        'location' => $review['label'][$locale],
                    ]);
                }
            }
        }
    }

    /** Keep these records intact if this data migration is rolled back. */
    public function down(): void
    {
        // Intentionally non-destructive: admins may have edited these reviews.
    }
};
