<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
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
        ini_set('memory_limit', '512M');

        // 1. Correct typo in permission translations ('قطة' -> 'قطعة')
        if (Schema::hasTable('permission_translations')) {
            DB::table('permission_translations')
                ->where('title', 'like', '%قطة%')
                ->update([
                    'title' => DB::raw("REPLACE(title, 'قطة', 'قطعة')")
                ]);
        }

        // 2. Ensure order item permissions exist
        $permissions = [
            'dashboard.orders.item.destroy',
            'dashboard.orders.item.edit',
        ];

        foreach ($permissions as $permName) {
            foreach (['web', 'api'] as $guard) {
                Permission::firstOrCreate([
                    'name' => $permName,
                    'guard_name' => $guard,
                ]);
            }
        }

        // 3. Ensure any role that has order edit or order-items delete permission also receives item destroy/edit
        // Use raw DB queries to avoid PermissionDoesNotExist exceptions from Spatie when permissions may not exist
        $permissionsByName = Permission::all()->keyBy('name');

        $orderEditNames    = ['dashboard.orders.edit', 'orders.edit', 'orders edit'];
        $orderDeleteNames  = ['dashboard.order-items.delete', 'order-items.delete', 'order-items delete'];

        $orderEditIds   = $permissionsByName->whereIn('name', $orderEditNames)->pluck('id')->toArray();
        $orderDeleteIds = $permissionsByName->whereIn('name', $orderDeleteNames)->pluck('id')->toArray();

        $destroyPermWeb = $permissionsByName->firstWhere('name', 'dashboard.orders.item.destroy') 
                       ?? Permission::firstOrCreate(['name' => 'dashboard.orders.item.destroy', 'guard_name' => 'web']);
        $editPermWeb    = $permissionsByName->firstWhere('name', 'dashboard.orders.item.edit')
                       ?? Permission::firstOrCreate(['name' => 'dashboard.orders.item.edit', 'guard_name' => 'web']);

        $roles = Role::all();
        foreach ($roles as $role) {
            // Get IDs of permissions already assigned to this role
            $rolePermIds = DB::table('role_has_permissions')
                ->where('role_id', $role->id)
                ->pluck('permission_id')
                ->toArray();

            $hasOrderEdit       = count(array_intersect($rolePermIds, $orderEditIds)) > 0;
            $hasOrderItemDelete = count(array_intersect($rolePermIds, $orderDeleteIds)) > 0;

            if (($hasOrderEdit || $hasOrderItemDelete) && !in_array($destroyPermWeb->id, $rolePermIds)) {
                DB::table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $destroyPermWeb->id,
                    'role_id'       => $role->id,
                ]);
            }

            if ($hasOrderEdit && !in_array($editPermWeb->id, $rolePermIds)) {
                DB::table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $editPermWeb->id,
                    'role_id'       => $role->id,
                ]);
            }
        }

        // 4. Reset cached permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No down migration needed
    }
};

