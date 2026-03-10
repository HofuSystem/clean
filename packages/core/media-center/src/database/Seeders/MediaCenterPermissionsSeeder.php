<?php

namespace Core\MediaCenter\database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MediaCenterPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(['dashboard.mediacenter.mymedia'] as $permission) {
            Permission::firstOrCreate(['name'=>$permission]);
        }
    }
}
