<?php

namespace Core\Admin\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Core\Users\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::firstOrCreate(['name'=>'it']);
        $role->syncPermissions(Permission::all());

        $admin = User::updateOrCreate([
            'email'     => 'it@cleanstation.app',
        ],[
            'fullname' => 'it (admin)',
            'password' => '1005@clean',
            'is_active'=> true,
        ]);
        //create the admin role
        $admin->syncRoles($role);

        // $admin = User::updateOrCreate([
        //     'email'     => 'anas@cleanstation.app',
        // ],[
        //     'fullname' => 'anas (admin)',
        //     'password' => '1005@clean',
        //     'is_active'=> true,
        // ]);
        // //create the admin role
        // $admin->syncRoles($role);

        // $admin = User::updateOrCreate([
        //     'email'     => 'muath@cleanstation.app',
        // ],[
        //     'fullname' => 'muath (admin)',
        //     'password' => '1005@clean',
        //     'is_active'=> true,
        // ]);
        // //create the admin role
        // $admin->syncRoles($role);

        // $admin = User::updateOrCreate([
        //     'email'     => 'ayed@cleanstation.app',
        // ],[
        //     'fullname' => 'ayed (admin)',
        //     'password' => '1005@clean',
        //     'is_active'=> true,
        // ]);
        // //create the admin role
        // $admin->syncRoles($role);

    }
}
