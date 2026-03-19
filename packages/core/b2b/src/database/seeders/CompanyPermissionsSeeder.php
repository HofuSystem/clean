<?php

namespace Core\B2B\Database\Seeders;

use Illuminate\Database\Seeder;
use Core\B2B\Models\CompanyPermission;

class CompanyPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'slug' => 'dashboard-analytics',
                'ar' => ['name' => 'لوحة التحكم الأساسية والتحليلات'],
                'en' => ['name' => 'Dashboard and Analytics'],
            ],
            [
                'slug' => 'manage-orders',
                'ar' => ['name' => 'إدارة وإنشاء الطلبات (عقد / نزلاء)'],
                'en' => ['name' => 'Manage and Create Orders'],
            ],
            [
                'slug' => 'invoices-payments',
                'ar' => ['name' => 'الفواتير والدفع المالي'],
                'en' => ['name' => 'Invoices and Payments'],
            ],
            [
                'slug' => 'manage-scheduling-addresses',
                'ar' => ['name' => 'إدارة الجدولة والعناوين'],
                'en' => ['name' => 'Manage Scheduling and Addresses'],
            ],
            [
                'slug' => 'edit-guest-pricing',
                'ar' => ['name' => 'تعديل تسعيرة النزلاء'],
                'en' => ['name' => 'Edit Guest Pricing'],
            ],
            [
                'slug' => 'manage-user-permissions',
                'ar' => ['name' => 'إدارة صلاحيات المستخدمين'],
                'en' => ['name' => 'Manage User Permissions'],
            ],
        ];

        foreach ($permissions as $perm) {
            $companyPermission = CompanyPermission::where('slug', $perm['slug'])->first();
            if (!$companyPermission) {
                CompanyPermission::create($perm);
            } else {
                $companyPermission->update($perm);
            }
        }
    }
}
