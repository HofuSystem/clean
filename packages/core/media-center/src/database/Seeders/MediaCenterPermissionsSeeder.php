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
        foreach ([
            'dashboard.mediacenter.mymedia',
            'dashboard.media-center.list',
            'dashboard.media-center.add-new',
            'dashboard.media-center.delete',
        ] as $permission) {
            Permission::firstOrCreate(['name'=>$permission]);
        }
    }
}
