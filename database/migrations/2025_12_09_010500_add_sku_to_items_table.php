<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if sku column doesn't exist before adding it
        if (!Schema::hasColumn('items', 'sku')) {
            Schema::table('items', function (Blueprint $table) {
                $table->string('sku', 100)->unique()->after('id')->comment('Stock Keeping Unit (unique identifier)');
                $table->index('sku');
            });
        }
        
        // Check if status column exists and change it to enum if it's not
        if (Schema::hasColumn('items', 'status')) {
            // Drop the old status column if it exists
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
        
        // Add status as enum
        Schema::table('items', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('active')->after('image_path');
            $table->index('status');
        });
        
        // Ensure is_active column exists for backward compatibility
        if (!Schema::hasColumn('items', 'is_active')) {
            Schema::table('items', function (Blueprint $table) {
                $table->boolean('is_active')->default(1)->after('status')->comment('Active status (for backward compatibility)');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'sku')) {
                $table->dropIndex(['sku']);
                $table->dropColumn('sku');
            }
            
            if (Schema::hasColumn('items', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
