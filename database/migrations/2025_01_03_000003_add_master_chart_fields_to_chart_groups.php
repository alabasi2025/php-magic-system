<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration: إضافة حقول الدليل الرئيسي والحسابات الوسيطة
 * 
 * 💡 الفكرة:
 * - كل وحدة لها دليل رئيسي (master_chart)
 * - جميع الأدلة الأخرى هي فروع من الدليل الرئيسي
 * - دليل الحسابات الوسيطة (intermediate_master) يحتوي على فروع تلقائية لكل دليل
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chart_groups', function (Blueprint $table) {
            // إضافة حقل parent_group_id للإشارة إلى الدليل الأب
            $table->unsignedBigInteger('parent_group_id')->nullable()->after('unit_id')
                ->comment('الدليل الأب (للفروع)');
            
            // إضافة حقل is_master للدليل الرئيسي
            $table->boolean('is_master')->default(false)->after('type')
                ->comment('هل هو الدليل الرئيسي للوحدة؟');
            
            // إضافة حقل source_group_id للربط مع الدليل الأصلي (للحسابات الوسيطة)
            $table->unsignedBigInteger('source_group_id')->nullable()->after('parent_group_id')
                ->comment('الدليل الأصلي (لفروع الحسابات الوسيطة)');
            
            // Indexes
            $table->index('parent_group_id');
            $table->index('source_group_id');
            $table->index('is_master');
            
            // Foreign Keys
            $table->foreign('parent_group_id')->references('id')->on('chart_groups')->onDelete('cascade');
            $table->foreign('source_group_id')->references('id')->on('chart_groups')->onDelete('cascade');
        });
        
        // إضافة نوع جديد للدليل
        DB::statement("ALTER TABLE chart_groups MODIFY COLUMN type ENUM(
            'master_chart',
            'intermediate_master',
            'payroll',
            'final_accounts',
            'assets',
            'budget',
            'projects',
            'inventory',
            'sales',
            'purchases',
            'custom'
        ) DEFAULT 'custom' COMMENT 'نوع الدليل'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_groups', function (Blueprint $table) {
            $table->dropForeign(['parent_group_id']);
            $table->dropForeign(['source_group_id']);
            $table->dropIndex(['parent_group_id']);
            $table->dropIndex(['source_group_id']);
            $table->dropIndex(['is_master']);
            $table->dropColumn(['parent_group_id', 'source_group_id', 'is_master']);
        });
        
        // إعادة تعريف enum بدون master_chart و intermediate_master
        DB::statement("ALTER TABLE chart_groups MODIFY COLUMN type ENUM(
            'payroll',
            'final_accounts',
            'assets',
            'budget',
            'projects',
            'inventory',
            'sales',
            'purchases',
            'custom'
        ) DEFAULT 'custom' COMMENT 'نوع الدليل'");
    }
};
