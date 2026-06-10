<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Core\Products\Models\Product;
use Core\Products\Models\ProductSetting;

class ProductSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Gift Wrapping Setting
        $wrappingSetting = ProductSetting::updateOrCreate(
            ['slug' => 'gift-wrapping'],
            [
                'status' => 'active',
                'en' => ['name' => 'Gift Wrapping', 'description' => 'Choose a wrapping style for your product'],
                'ar' => ['name' => 'تغليف الهدايا', 'description' => 'اختر أسلوب التغليف المناسب لمنتجك']
            ]
        );

        $classicWrap = ProductSetting::updateOrCreate(
            ['slug' => 'classic-wrapping'],
            [
                'parent_id' => $wrappingSetting->id,
                'addon_price' => 10,
                'cost' => 5,
                'status' => 'active',
                'en' => ['name' => 'Classic Wrapping', 'description' => 'Simple and clean wrapping paper'],
                'ar' => ['name' => 'تغليف كلاسيكي', 'description' => 'ورق تغليف بسيط وأنيق']
            ]
        );

        $premiumWrap = ProductSetting::updateOrCreate(
            ['slug' => 'premium-wrapping'],
            [
                'parent_id' => $wrappingSetting->id,
                'addon_price' => 25,
                'cost' => 12,
                'status' => 'active',
                'en' => ['name' => 'Premium Wrapping', 'description' => 'Luxurious velvet paper with ribbon details'],
                'ar' => ['name' => 'تغليف فاخر', 'description' => 'ورق مخملي فاخر مع تفاصيل شريط أنيقة']
            ]
        );

        // 2. Card Type Setting
        $cardSetting = ProductSetting::updateOrCreate(
            ['slug' => 'card-type'],
            [
                'status' => 'active',
                'en' => ['name' => 'Greeting Card', 'description' => 'Add a card with a custom message'],
                'ar' => ['name' => 'بطاقة تهنئة', 'description' => 'أضف بطاقة تحتوي على رسالة مخصصة']
            ]
        );

        $standardCard = ProductSetting::updateOrCreate(
            ['slug' => 'standard-card'],
            [
                'parent_id' => $cardSetting->id,
                'addon_price' => 5,
                'cost' => 2,
                'status' => 'active',
                'en' => ['name' => 'Standard Card', 'description' => 'Printed greeting card with your message'],
                'ar' => ['name' => 'بطاقة عادية', 'description' => 'بطاقة تهنئة مطبوعة مع رسالتك']
            ]
        );

        $deluxeCard = ProductSetting::updateOrCreate(
            ['slug' => 'deluxe-wax-sealed-card'],
            [
                'parent_id' => $cardSetting->id,
                'addon_price' => 20,
                'cost' => 10,
                'status' => 'active',
                'en' => ['name' => 'Deluxe Wax Sealed Card', 'description' => 'Premium card hand-sealed with classic wax'],
                'ar' => ['name' => 'بطاقة فاخرة بختم شمعي', 'description' => 'بطاقة ممتازة مختومة يدوياً بالشمع الكلاسيكي']
            ]
        );

        // 3. Size Options Setting
        $sizeSetting = ProductSetting::updateOrCreate(
            ['slug' => 'size-options'],
            [
                'status' => 'active',
                'en' => ['name' => 'Size Options', 'description' => 'Select the size for the product arrangement'],
                'ar' => ['name' => 'خيارات الحجم', 'description' => 'حدد حجم تنسيق المنتج']
            ]
        );

        $smallSize = ProductSetting::updateOrCreate(
            ['slug' => 'small-size'],
            [
                'parent_id' => $sizeSetting->id,
                'addon_price' => 0,
                'cost' => 0,
                'status' => 'active',
                'en' => ['name' => 'Small Size', 'description' => 'Default compact option'],
                'ar' => ['name' => 'حجم صغير', 'description' => 'الخيار الصغير الافتراضي']
            ]
        );

        $mediumSize = ProductSetting::updateOrCreate(
            ['slug' => 'medium-size'],
            [
                'parent_id' => $sizeSetting->id,
                'addon_price' => 30,
                'cost' => 15,
                'status' => 'active',
                'en' => ['name' => 'Medium Size', 'description' => 'Standard arrangement size'],
                'ar' => ['name' => 'حجم متوسط', 'description' => 'الحجم القياسي للتنسيق']
            ]
        );

        $largeSize = ProductSetting::updateOrCreate(
            ['slug' => 'large-size'],
            [
                'parent_id' => $sizeSetting->id,
                'addon_price' => 60,
                'cost' => 30,
                'status' => 'active',
                'en' => ['name' => 'Large Size', 'description' => 'Premium extra-full arrangement'],
                'ar' => ['name' => 'حجم كبير', 'description' => 'حجم كبير فاخر وممتلئ بالكامل']
            ]
        );

        // 4. Associate to all existing products
        $allProducts = Product::all();
        $allSettingIds = [
            $wrappingSetting->id, $classicWrap->id, $premiumWrap->id,
            $cardSetting->id, $standardCard->id, $deluxeCard->id,
            $sizeSetting->id, $smallSize->id, $mediumSize->id, $largeSize->id
        ];

        foreach ($allProducts as $product) {
            $product->productSettings()->sync($allSettingIds);
        }
    }
}
