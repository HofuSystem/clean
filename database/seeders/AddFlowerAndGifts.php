<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Core\Categories\Models\Category;
use Core\Products\Models\ProductSetting;
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
        
        // 2. Category Settings (Customizations)
        $wrapping = ProductSetting::updateOrCreate(
            ['slug' => 'full-wraping'],
            [
                'status' => 'active',
                'en' => ['name' => 'Gift wrapping'],
                'ar' => ['name' => 'تغليف الهدية']
            ]
        );

        ProductSetting::updateOrCreate(
            ['slug' => 'luxurious-velvet'],
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

        ProductSetting::updateOrCreate(
            ['slug' => 'transparent-snow'],
            [
                'addon_price' => 15,
                'parent_id' => $wrapping->id,
                'status' => 'active',
                'cost' => 10.0,
                'en' => ['name' => 'transparent snow'],
                'ar' => ['name' => 'شفاف ثلجى']
            ]
        );

        $colors = ProductSetting::updateOrCreate(
            ['slug' => 'colors'],
            [
                'status' => 'active',
                'en' => ['name' => 'colors'],
                'ar' => ['name' => 'الألوان']
            ]
        );

        ProductSetting::updateOrCreate(
            ['slug' => 'red'],
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

        ProductSetting::updateOrCreate(
            ['slug' => 'blue-1'],
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

        ProductSetting::updateOrCreate(
            ['slug' => 'white'],
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

        ProductSetting::updateOrCreate(
            ['slug' => 'light-blue'],
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

        $occasion = ProductSetting::updateOrCreate(
            ['slug' => 'bills-occasion'],
            [
                'status' => 'active',
                'en' => ['name' => 'The occasion'],
                'ar' => ['name' => 'المناسبة']
            ]
        );

        ProductSetting::updateOrCreate(
            ['slug' => 'birth-day'],
            [
                'addon_price' => 0,
                'parent_id' => $occasion->id,
                'status' => 'active',
                'cost' => 0.0,
                'en' => ['name' => 'birth day'],
                'ar' => ['name' => 'عيد ميلاد']
            ]
        );

        ProductSetting::updateOrCreate(
            ['slug' => 'wedding-anniversary'],
            [
                'addon_price' => 0,
                'parent_id' => $occasion->id,
                'status' => 'active',
                'cost' => 0.0,
                'en' => ['name' => 'wedding anniversary'],
                'ar' => ['name' => 'ذكرى زواج']
            ]
        );

        ProductSetting::updateOrCreate(
            ['slug' => 'graduation'],
            [
                'addon_price' => 0,
                'parent_id' => $occasion->id,
                'status' => 'active',
                'cost' => 0.0,
                'en' => ['name' => 'graduation'],
                'ar' => ['name' => 'تخرج']
            ]
        );

        $upgradeCard = ProductSetting::updateOrCreate(
            ['slug' => 'upgrade-card'],
            [
                'status' => 'active',
                'en' => ['name' => 'upgrade card'],
                'ar' => ['name' => 'ترقية بطاقة']
            ]
        );

        ProductSetting::updateOrCreate(
            ['slug' => 'premium-card-upgrade'],
            [
                'addon_price' => 0,
                'parent_id' => $upgradeCard->id,
                'status' => 'active',
                'cost' => 0.0,
                'en' => ['name' => 'Premium card upgrade', 'description' => 'Leather texture with a wax seal'],
                'ar' => ['name' => 'ترقية كرت فاخر', 'description' => 'ملمس جلدى مع ختم شمعى']
            ]
        );
    }
      
}
