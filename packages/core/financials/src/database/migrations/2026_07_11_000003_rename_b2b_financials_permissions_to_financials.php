<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\DB;

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
            $newName = str_replace('dashboard.b2b-financials.', 'dashboard.financials.', $permission->name);

            $existing = Permission::where('name', $newName)
                ->where('guard_name', $permission->guard_name)
                ->first();

            if ($existing) {
                // Transfer role_has_permissions to existing permission ID
                DB::table('role_has_permissions')
                    ->where('permission_id', $permission->id)
                    ->get()
                    ->each(function ($rhp) use ($existing) {
                        DB::table('role_has_permissions')->insertOrIgnore([
                            'permission_id' => $existing->id,
                            'role_id'       => $rhp->role_id,
                        ]);
                    });

                // Transfer model_has_permissions to existing permission ID
                DB::table('model_has_permissions')
                    ->where('permission_id', $permission->id)
                    ->get()
                    ->each(function ($mhp) use ($existing) {
                        DB::table('model_has_permissions')->insertOrIgnore([
                            'permission_id' => $existing->id,
                            'model_type'    => $mhp->model_type,
                            'model_id'      => $mhp->model_id,
                        ]);
                    });

                // Remove old permission translation records if any
                DB::table('permission_translations')->where('permission_id', $permission->id)->delete();

                // Delete the duplicate old permission
                $permission->delete();
            } else {
                $permission->name = $newName;
                $permission->save();
            }
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
            $oldName = str_replace('dashboard.financials.', 'dashboard.b2b-financials.', $permission->name);
            $existing = Permission::where('name', $oldName)
                ->where('guard_name', $permission->guard_name)
                ->first();

            if (!$existing) {
                $permission->name = $oldName;
                $permission->save();
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
