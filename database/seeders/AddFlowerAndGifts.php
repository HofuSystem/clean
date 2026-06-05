<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Core\Categories\Models\Category;
use Core\Categories\Models\CategorySetting;
use Core\Products\Models\Product;

class AddFlowerAndGifts extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Categories
        $parent = Category::updateOrCreate(
            ['slug' => 'gifts-and-flowers'],
            [
                'image' => 'images/Hw2hRvG40kKtSepsrWAj8LhuUsoqtzEY9VRycmCG.jpg',
                'type' => 'sales',
                'is_package' => 0,
                'status' => 'active',
                'for_all_cities' => 0,
                'en' => ['name' => 'gifts and flowers', 'desc' => '<p><br></p>'],
                'ar' => ['name' => 'الزهور والهدايا', 'desc' => '<p><br></p>']
            ]
        );

        $flowers = Category::updateOrCreate(
            ['slug' => 'flowers'],
            [
                'image' => 'images/Hw2hRvG40kKtSepsrWAj8LhuUsoqtzEY9VRycmCG.jpg',
                'type' => 'sales',
                'is_package' => 0,
                'status' => 'active',
                'parent_id' => $parent->id,
                'for_all_cities' => 0,
                'en' => ['name' => 'flowers', 'desc' => '<p><br></p>'],
                'ar' => ['name' => 'الزهور', 'desc' => '<p><br></p>']
            ]
        );

        $vase = Category::updateOrCreate(
            ['slug' => 'vase'],
            [
                'image' => 'images/Hw2hRvG40kKtSepsrWAj8LhuUsoqtzEY9VRycmCG.jpg',
                'type' => 'sales',
                'is_package' => 0,
                'status' => 'active',
                'parent_id' => $parent->id,
                'for_all_cities' => 0,
                'en' => ['name' => 'vase', 'desc' => '<p><br></p>'],
                'ar' => ['name' => 'فازات', 'desc' => '<p><br></p>']
            ]
        );

        // 2. Category Settings (Customizations)
        $wrapping = CategorySetting::updateOrCreate(
            ['slug' => 'wraping', 'category_id' => $flowers->id],
            [
                'status' => 'active',
                'en' => ['name' => 'Gift wrapping'],
                'ar' => ['name' => 'تغليف الهدية']
            ]
        );

        CategorySetting::updateOrCreate(
            ['slug' => 'luxurious-velvet', 'category_id' => $flowers->id],
            [
                'addon_price' => 25,
                'parent_id' => $wrapping->id,
                'status' => 'active',
                'icon' => 'images/Hw2hRvG40kKtSepsrWAj8LhuUsoqtzEY9VRycmCG.jpg',
                'cost' => 15.0,
                'en' => ['name' => 'luxurious velvet'],
                'ar' => ['name' => 'مخمل فاخر']
            ]
        );

        CategorySetting::updateOrCreate(
            ['slug' => 'transparent-snow', 'category_id' => $flowers->id],
            [
                'addon_price' => 15,
                'parent_id' => $wrapping->id,
                'status' => 'active',
                'cost' => 10.0,
                'en' => ['name' => 'transparent snow'],
                'ar' => ['name' => 'شفاف ثلجى']
            ]
        );

        $colors = CategorySetting::updateOrCreate(
            ['slug' => 'colors', 'category_id' => $flowers->id],
            [
                'status' => 'active',
                'en' => ['name' => 'colors'],
                'ar' => ['name' => 'الألوان']
            ]
        );

        CategorySetting::updateOrCreate(
            ['slug' => 'red', 'category_id' => $flowers->id],
            [
                'addon_price' => 0,
                'parent_id' => $colors->id,
                'status' => 'active',
                'color' => '#f50000',
                'cost' => 0.0,
                'en' => ['name' => 'Red'],
                'ar' => ['name' => 'احمر']
            ]
        );

        CategorySetting::updateOrCreate(
            ['slug' => 'blue-1', 'category_id' => $flowers->id],
            [
                'addon_price' => 0,
                'parent_id' => $colors->id,
                'status' => 'active',
                'color' => '#003beb',
                'cost' => 0.0,
                'en' => ['name' => 'blue'],
                'ar' => ['name' => 'أزرق']
            ]
        );

        CategorySetting::updateOrCreate(
            ['slug' => 'white', 'category_id' => $flowers->id],
            [
                'addon_price' => 0,
                'parent_id' => $colors->id,
                'status' => 'active',
                'color' => '#ffffff',
                'cost' => 0.0,
                'en' => ['name' => 'white'],
                'ar' => ['name' => 'أبيض']
            ]
        );

        CategorySetting::updateOrCreate(
            ['slug' => 'light-blue', 'category_id' => $flowers->id],
            [
                'addon_price' => 0,
                'parent_id' => $colors->id,
                'status' => 'active',
                'color' => '#00d8f5',
                'cost' => 0.0,
                'en' => ['name' => 'light blue'],
                'ar' => ['name' => 'أزرق فاتح']
            ]
        );

        $occasion = CategorySetting::updateOrCreate(
            ['slug' => 'the-occasion', 'category_id' => $flowers->id],
            [
                'status' => 'active',
                'en' => ['name' => 'The occasion'],
                'ar' => ['name' => 'المناسبة']
            ]
        );

        CategorySetting::updateOrCreate(
            ['slug' => 'birth-day', 'category_id' => $flowers->id],
            [
                'addon_price' => 0,
                'parent_id' => $occasion->id,
                'status' => 'active',
                'cost' => 0.0,
                'en' => ['name' => 'birth day'],
                'ar' => ['name' => 'عيد ميلاد']
            ]
        );

        CategorySetting::updateOrCreate(
            ['slug' => 'wedding-anniversary', 'category_id' => $flowers->id],
            [
                'addon_price' => 0,
                'parent_id' => $occasion->id,
                'status' => 'active',
                'cost' => 0.0,
                'en' => ['name' => 'wedding anniversary'],
                'ar' => ['name' => 'ذكرى زواج']
            ]
        );

        CategorySetting::updateOrCreate(
            ['slug' => 'graduation', 'category_id' => $flowers->id],
            [
                'addon_price' => 0,
                'parent_id' => $occasion->id,
                'status' => 'active',
                'cost' => 0.0,
                'en' => ['name' => 'graduation'],
                'ar' => ['name' => 'تخرج']
            ]
        );

        $upgradeCard = CategorySetting::updateOrCreate(
            ['slug' => 'upgrade-card', 'category_id' => $flowers->id],
            [
                'status' => 'active',
                'en' => ['name' => 'upgrade card'],
                'ar' => ['name' => 'ترقية بطاقة']
            ]
        );

        CategorySetting::updateOrCreate(
            ['slug' => 'premium-card-upgrade', 'category_id' => $flowers->id],
            [
                'addon_price' => 0,
                'parent_id' => $upgradeCard->id,
                'status' => 'active',
                'cost' => 0.0,
                'en' => ['name' => 'Premium card upgrade', 'description' => 'Leather texture with a wax seal'],
                'ar' => ['name' => 'ترقية كرت فاخر', 'description' => 'ملمس جلدى مع ختم شمعى']
            ]
        );

        // 3. Products
        $product1 = Product::whereHas('translations', function($q) {
            $q->where('name', 'Luxury peony bouquet')->orWhere('name', 'باقة الفاونيا الفاخرة');
        })->first();

        if ($product1) {
            $product1->update([
                'category_id' => $parent->id,
                'sub_category_id' => $flowers->id,
                'price' => 520.0,
                'cost' => 300.0,
                'display_as' => 'main',
                'quantity' => -1,
                'status' => 'active',
                'type' => 'sales'
            ]);
        } else {
            Product::create([
                'type' => 'sales',
                'category_id' => $parent->id,
                'sub_category_id' => $flowers->id,
                'price' => 520.0,
                'cost' => 300.0,
                'display_as' => 'main',
                'quantity' => -1,
                'status' => 'active',
                'en' => ['name' => 'Luxury peony bouquet', 'desc' => 'Royal floral arrangement'],
                'ar' => ['name' => 'باقة الفاونيا الفاخرة', 'desc' => 'تنسيق ملكى من الزهور']
            ]);
        }

        $product2 = Product::whereHas('translations', function($q) {
            $q->where('name', 'Bostani Chocolate')->orWhere('name', 'شوكولاتة بستانى');
        })->first();

        if ($product2) {
            $product2->update([
                'category_id' => $parent->id,
                'sub_category_id' => $flowers->id,
                'price' => 25.0,
                'cost' => 10.0,
                'display_as' => 'addon',
                'quantity' => null,
                'status' => 'active',
                'type' => 'sales'
            ]);
        } else {
            Product::create([
                'type' => 'sales',
                'category_id' => $parent->id,
                'sub_category_id' => $flowers->id,
                'price' => 25.0,
                'cost' => 10.0,
                'display_as' => 'addon',
                'quantity' => null,
                'status' => 'active',
                'en' => ['name' => 'Bostani Chocolate', 'desc' => 'Bostani Chocolate'],
                'ar' => ['name' => 'شوكولاتة بستانى', 'desc' => 'شوكولاتة بستانى']
            ]);
        }
    }
}
