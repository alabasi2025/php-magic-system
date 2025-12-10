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
        Schema::table('items', function (Blueprint $table) {
            // Make all extra fields nullable
            if (Schema::hasColumn('items', 'code')) {
                $table->string('code', 100)->nullable()->change();
            }
            if (Schema::hasColumn('items', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->change();
            }
            if (Schema::hasColumn('items', 'reorder_level')) {
                $table->decimal('reorder_level', 15, 2)->nullable()->change();
            }
            if (Schema::hasColumn('items', 'cost_price')) {
                $table->decimal('cost_price', 15, 2)->nullable()->change();
            }
            if (Schema::hasColumn('items', 'selling_price')) {
                $table->decimal('selling_price', 15, 2)->nullable()->change();
            }
            if (Schema::hasColumn('items', 'image')) {
                $table->string('image')->nullable()->change();
            }
            if (Schema::hasColumn('items', 'is_active')) {
                $table->boolean('is_active')->nullable()->default(1)->change();
            }
            if (Schema::hasColumn('items', 'deleted_at')) {
                // Already nullable
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - keeping fields nullable is safe
    }
};
