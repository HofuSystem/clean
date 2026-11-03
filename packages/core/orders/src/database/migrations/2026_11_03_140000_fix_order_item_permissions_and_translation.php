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
        $roles = Role::all();
        foreach ($roles as $role) {
            $hasOrderEdit = $role->hasPermissionTo('dashboard.orders.edit', $role->guard_name)
                || $role->hasPermissionTo('orders.edit', $role->guard_name)
                || $role->hasPermissionTo('orders edit', $role->guard_name);

            $hasOrderItemDelete = $role->hasPermissionTo('dashboard.order-items.delete', $role->guard_name)
                || $role->hasPermissionTo('order-items.delete', $role->guard_name)
                || $role->hasPermissionTo('order-items delete', $role->guard_name);

            if ($hasOrderEdit || $hasOrderItemDelete) {
                $role->givePermissionTo('dashboard.orders.item.destroy');
            }

            if ($hasOrderEdit) {
                $role->givePermissionTo('dashboard.orders.item.edit');
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

