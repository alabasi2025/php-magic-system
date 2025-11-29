<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'permission_role' pivot table.
 * This table establishes a many-to-many relationship between permissions and roles.
 * It uses composite primary keys and foreign key constraints for data integrity.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Create the pivot table 'permission_role'
        Schema::create('permission_role', function (Blueprint $table) {
            // Define the foreign key for the 'permissions' table.
            // Using 'constrained()' assumes the 'permissions' table exists and is named 'permissions'.
            // 'cascadeOnDelete()' ensures that if a permission is deleted, the corresponding pivot entries are also deleted.
            $table->foreignId('permission_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Define the foreign key for the 'roles' table.
            // Using 'constrained()' assumes the 'roles' table exists and is named 'roles'.
            // 'cascadeOnDelete()' ensures that if a role is deleted, the corresponding pivot entries are also deleted.
            $table->foreignId('role_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Define the composite primary key to ensure a unique combination of permission and role.
            $table->primary(['permission_id', 'role_id']);

            // Add standard Laravel timestamps (created_at and updated_at).
            $table->timestamps();

            // Add an index for faster lookups, although the primary key already serves this purpose,
            // this can be useful for specific queries that only filter by one of the IDs.
            $table->index(['role_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};