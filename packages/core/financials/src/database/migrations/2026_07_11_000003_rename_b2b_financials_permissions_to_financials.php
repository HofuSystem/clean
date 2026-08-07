<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::where('name', 'like', 'dashboard.b2b-financials.%')->get()->each(function ($permission) {
            $permission->name = str_replace('dashboard.b2b-financials.', 'dashboard.financials.', $permission->name);
            $permission->save();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::where('name', 'like', 'dashboard.financials.%')->get()->each(function ($permission) {
            $permission->name = str_replace('dashboard.financials.', 'dashboard.b2b-financials.', $permission->name);
            $permission->save();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
