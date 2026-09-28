<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define all module permissions
        $permissions = [
            // Categories
            'categories.read',
            'categories.create',
            'categories.update',
            'categories.delete',

            // Products
            'products.read',
            'products.create',
            'products.update',
            'products.delete',

            // Orders
            'orders.read',
            'orders.update',

            // Users & Roles Management
            'users.read',
            'users.create',
            'users.update',
            'users.delete',

            // Brand Settings
            'brand_settings.read',
            'brand_settings.update',
        ];

        $guards = ['web', 'tenant_api'];

        foreach ($permissions as $permissionName) {
            foreach ($guards as $guard) {
                Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => $guard]);
            }
        }

        // 2. Roles
        foreach ($guards as $guard) {
            $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            $adminRole->syncPermissions(Permission::where('guard_name', $guard)->get());

            $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => $guard]);
            $managerRole->syncPermissions(
                Permission::where('guard_name', $guard)
                    ->whereIn('name', [
                        'categories.read',
                        'categories.create',
                        'categories.update',
                        'products.read',
                        'products.create',
                        'products.update',
                        'orders.read',
                        'orders.update',
                    ])->get()
            );

            $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => $guard]);
            $userRole->syncPermissions(
                Permission::where('guard_name', $guard)
                    ->whereIn('name', [
                        'categories.read',
                        'products.read',
                    ])->get()
            );
        }

        // 3. Create Tenant Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@tenant.com'],
            [
                'name' => 'Tenant Admin',
                'password' => 'password123',
            ]
        );

        $adminRoles = Role::where('name', 'admin')->get();
        foreach ($adminRoles as $role) {
            if (! $adminUser->hasRole($role)) {
                $adminUser->assignRole($role);
            }
        }
    }
}
