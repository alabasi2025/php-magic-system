<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration to update status enum in purchase_invoices table
 * Add 'pending' status to the enum values
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
        // تعديل حقل status لإضافة pending
        DB::statement("ALTER TABLE `purchase_invoices` MODIFY COLUMN `status` ENUM('draft', 'pending', 'approved', 'cancelled') NOT NULL DEFAULT 'draft' COMMENT 'الحالة'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // إرجاع حقل status إلى القيم الأصلية
        DB::statement("ALTER TABLE `purchase_invoices` MODIFY COLUMN `status` ENUM('draft', 'approved', 'cancelled') NOT NULL DEFAULT 'draft' COMMENT 'الحالة'");
    }
};
