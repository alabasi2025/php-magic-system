<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add sku column if it doesn't exist
        if (!Schema::hasColumn('items', 'sku')) {
            Schema::table('items', function (Blueprint $table) {
                $table->string('sku', 100)->unique()->nullable()->comment('Stock Keeping Unit (unique identifier)');
            });
            
            // Add index separately to avoid errors
            try {
                DB::statement('ALTER TABLE items ADD INDEX idx_items_sku (sku)');
            } catch (\Exception $e) {
                // Index might already exist, ignore
            }
        }
        
        // Add barcode column if it doesn't exist
        if (!Schema::hasColumn('items', 'barcode')) {
            Schema::table('items', function (Blueprint $table) {
                $table->string('barcode', 100)->nullable()->unique();
            });
        }
        
        // Add image_path column if it doesn't exist
        if (!Schema::hasColumn('items', 'image_path')) {
            Schema::table('items', function (Blueprint $table) {
                $table->string('image_path', 500)->nullable();
            });
        }
        
        // Handle status column - check if it exists and what type it is
        $hasStatus = Schema::hasColumn('items', 'status');
        
        if ($hasStatus) {
            // Get column type
            $columnType = DB::select("SHOW COLUMNS FROM items WHERE Field = 'status'")[0]->Type ?? '';
            
            // If it's not enum, drop and recreate
            if (strpos($columnType, 'enum') === false) {
                Schema::table('items', function (Blueprint $table) {
                    $table->dropColumn('status');
                });
                
                Schema::table('items', function (Blueprint $table) {
                    $table->enum('status', ['active', 'inactive'])->default('active');
                });
            }
        } else {
            // Add status column
            Schema::table('items', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive'])->default('active');
            });
        }
        
        // Add index for status if it doesn't exist
        try {
            DB::statement('ALTER TABLE items ADD INDEX idx_items_status (status)');
        } catch (\Exception $e) {
            // Index might already exist, ignore
        }
        
        // Ensure is_active column exists for backward compatibility
        if (!Schema::hasColumn('items', 'is_active')) {
            Schema::table('items', function (Blueprint $table) {
                $table->boolean('is_active')->default(1)->comment('Active status (for backward compatibility)');
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
                $table->dropUnique(['sku']);
                $table->dropColumn('sku');
            }
            
            if (Schema::hasColumn('items', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
