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
        Schema::table('cash_boxes', function (Blueprint $table) {
            // إضافة حقل الوحدة التنظيمية
            $table->unsignedBigInteger('unit_id')->nullable()->after('id')->comment('الوحدة التنظيمية التابع لها الصندوق');
            
            // إضافة حقل الحساب الوسيط
            $table->unsignedBigInteger('intermediate_account_id')->nullable()->after('unit_id')->comment('الحساب الوسيط المرتبط بالصندوق');
            
            // إضافة Foreign Keys
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('intermediate_account_id')->references('id')->on('chart_accounts')->onDelete('set null');
            
            // إضافة Indexes
            $table->index('unit_id');
            $table->index('intermediate_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_boxes', function (Blueprint $table) {
            // حذف Foreign Keys أولاً
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['intermediate_account_id']);
            
            // حذف Indexes
            $table->dropIndex(['unit_id']);
            $table->dropIndex(['intermediate_account_id']);
            
            // حذف الحقول
            $table->dropColumn(['unit_id', 'intermediate_account_id']);
        });
    }
};
