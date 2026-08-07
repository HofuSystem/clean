<?php

use Illuminate\Database\Migrations\Migration;
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

        $permissionsToEnsure = [
            // Financial Analysis
            'dashboard.financial-analysis',
            'dashboard.financial-analysis.export-monthly',
            'dashboard.financial-analysis.export-daily',
            'dashboard.financial-analysis.daily',
            'dashboard.financial-analysis.store-daily',

            // Purchase Providers
            'dashboard.purchase-providers.index',
            'dashboard.purchase-providers.create',
            'dashboard.purchase-providers.edit',
            'dashboard.purchase-providers.show',
            'dashboard.purchase-providers.delete',
            'dashboard.purchase-providers.import',
            'dashboard.purchase-providers.export',
            'dashboard.purchase-providers.restore',

            // Purchase Items
            'dashboard.purchase-items.index',
            'dashboard.purchase-items.create',
            'dashboard.purchase-items.edit',
            'dashboard.purchase-items.show',
            'dashboard.purchase-items.delete',
            'dashboard.purchase-items.import',
            'dashboard.purchase-items.export',
            'dashboard.purchase-items.restore',

            // Purchases
            'dashboard.purchases.index',
            'dashboard.purchases.create',
            'dashboard.purchases.edit',
            'dashboard.purchases.show',
            'dashboard.purchases.delete',
            'dashboard.purchases.import',
            'dashboard.purchases.export',
            'dashboard.purchases.restore',
        ];

        $targetRoleNames = ['superadmin', 'admin', 'it', 'cs'];
        $roles = Role::whereIn('name', $targetRoleNames)->get();

        foreach ($permissionsToEnsure as $permName) {
            foreach (['web', 'api'] as $guard) {
                $perm = Permission::firstOrCreate([
                    'name' => $permName,
                    'guard_name' => $guard
                ]);

                foreach ($roles as $role) {
                    if ($role->guard_name === $guard && !$role->hasPermissionTo($permName, $guard)) {
                        $role->givePermissionTo($perm);
                    }
                }
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        ini_set('memory_limit', '512M');

        $permissionsToRemove = [
            'dashboard.financial-analysis',
            'dashboard.financial-analysis.export-monthly',
            'dashboard.financial-analysis.export-daily',
            'dashboard.financial-analysis.daily',
            'dashboard.financial-analysis.store-daily',
            'dashboard.purchase-providers.index',
            'dashboard.purchase-providers.create',
            'dashboard.purchase-providers.edit',
            'dashboard.purchase-providers.show',
            'dashboard.purchase-providers.delete',
            'dashboard.purchase-providers.import',
            'dashboard.purchase-providers.export',
            'dashboard.purchase-providers.restore',
            'dashboard.purchase-items.index',
            'dashboard.purchase-items.create',
            'dashboard.purchase-items.edit',
            'dashboard.purchase-items.show',
            'dashboard.purchase-items.delete',
            'dashboard.purchase-items.import',
            'dashboard.purchase-items.export',
            'dashboard.purchase-items.restore',
            'dashboard.purchases.index',
            'dashboard.purchases.create',
            'dashboard.purchases.edit',
            'dashboard.purchases.show',
            'dashboard.purchases.delete',
            'dashboard.purchases.import',
            'dashboard.purchases.export',
            'dashboard.purchases.restore',
        ];

        Permission::whereIn('name', $permissionsToRemove)->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
