<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Core\Admin\Database\Seeders\AdminSeeder;
use Core\Admin\Database\Seeders\LangSeeder;
use Core\Admin\Database\Seeders\PermissionSeeder;
use Core\Entities\database\Seeders\PackageSeeder;
use Core\MediaCenter\Models\Media;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // MigrateDataSeeder::class,
            // NotificationsSeeder::class,
            // PackageSeeder::class,
            // EntitiesTableSeeder::class,
            // LangSeeder::class,
            PermissionSeeder::class,
            AdminSeeder::class,
            // LatestAppearSeeder::class,
            // PagesAndSectionsSeeder::class,

        ]);
    }
}
