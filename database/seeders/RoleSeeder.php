<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

/**
 * RoleSeeder
 *
 * Seeds the database with basic application roles: Super Admin, Admin, Manager, and User.
 * It assumes the use of Spatie's laravel-permission package.
 * The Super Admin role is granted all existing permissions.
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Disable model events and foreign key checks for faster seeding
        // and to prevent issues if other seeders are run concurrently.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::query()->delete();
        Permission::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Define the core roles
        $roles = [
            'super admin',
            'admin',
            'manager',
            'user',
        ];

        // 2. Create the roles
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 3. Assign all permissions to the 'super admin' role
        // This is a common practice for a top-level administrative role.

        // Retrieve the 'super admin' role
        $superAdminRole = Role::where('name', 'super admin')->first();

        if ($superAdminRole) {
            // Get all existing permissions.
            // Note: Permissions are typically seeded in a separate PermissionSeeder
            // or created dynamically by other seeders/migrations.
            $allPermissions = Permission::all();

            // Grant all permissions to the Super Admin role
            $superAdminRole->syncPermissions($allPermissions);

            // Optional: Log the action for clarity during seeding
            $this->command->info('Super Admin role granted all ' . $allPermissions->count() . ' permissions.');
        } else {
            $this->command->error('Super Admin role not found. Permissions not assigned.');
        }

        // 4. Assign specific permissions to other roles (Placeholder for future expansion)
        // For a production-ready seeder, you would define specific permissions here.
        // Example:
        // $adminRole = Role::where('name', 'admin')->first();
        // $adminRole->givePermissionTo(['manage users', 'view reports']);

        $this->command->info('Basic roles seeded successfully: ' . implode(', ', $roles));
    }
}