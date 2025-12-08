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
        // إضافة نوع حساب "مخزون" في جدول account_types
        DB::table('account_types')->insert([
            'key' => 'warehouse',
            'name_ar' => 'مخزون',
            'name_en' => 'Warehouse',
            'icon' => 'fas fa-warehouse',
            'is_active' => true,
            'is_system' => false,
            'sort_order' => 11,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('account_types')->where('key', 'warehouse')->delete();
    }
};
