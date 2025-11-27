<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the permissions table to ensure a clean slate before seeding
        // This is safe for seeders, but should be used with caution in production.
        DB::table('permissions')->delete();

        // Define the core CRUD operations
        $crud_operations = ['view', 'create', 'edit', 'delete'];

        // Define modules and their specific permissions
        $modules = [
            'users' => $crud_operations,
            'roles' => $crud_operations,
            'projects' => array_merge($crud_operations, ['manage_tasks', 'view_reports']),
            'accounting' => array_merge($crud_operations, ['process_invoices', 'view_financials']),
            'inventory' => array_merge($crud_operations, ['adjust_stock', 'receive_goods']),
        ];

        $permissions_to_insert = [];

        // Loop through modules and operations to generate permission names
        foreach ($modules as $module => $operations) {
            foreach ($operations as $operation) {
                // Permission name format: operation_module (e.g., view_users, create_roles)
                $permission_name = $operation . '_' . $module;

                // Add the permission to the array for bulk insertion
                $permissions_to_insert[] = [
                    'name'       => $permission_name,
                    'guard_name' => 'web', // Assuming 'web' guard for standard application
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert all generated permissions into the database
        // Using the Spatie Permission model for insertion is a best practice
        // but for bulk insertion, direct DB facade is often more performant.
        // We will use the DB facade for optimization.
        DB::table('permissions')->insert($permissions_to_insert);

        // Optional: Output a message to the console (for artisan command feedback)
        $this->command->info('Permissions seeded successfully for modules: ' . implode(', ', array_keys($modules)));
    }
}
