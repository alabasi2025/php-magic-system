<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: إنشاء جدول الحسابات المحاسبية
 * 
 * 💡 الفكرة:
 * الحسابات المحاسبية منظمة على شكل شجرة هرمية
 * كل حساب ينتمي لمجموعة دليل محددة
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chart_accounts', function (Blueprint $table) {
            $table->id();
            
            // المجموعة المرتبطة
            $table->unsignedBigInteger('chart_group_id')->comment('مجموعة الدليل');
            
            // البنية الشجرية
            $table->unsignedBigInteger('parent_id')->nullable()->comment('الحساب الأب');
            $table->integer('level')->default(1)->comment('المستوى في الشجرة');
            $table->boolean('is_parent')->default(false)->comment('هل هو حساب رئيسي؟');
            
            // المعلومات الأساسية
            $table->string('code', 50)->comment('كود الحساب');
            $table->string('name')->comment('اسم الحساب');
            $table->string('name_en')->nullable()->comment('الاسم بالإنجليزية');
            
            // نوع الحساب
            $table->enum('account_type', [
                'asset',        // أصول
                'liability',    // خصوم
                'equity',       // حقوق ملكية
                'revenue',      // إيرادات
                'expense'       // مصروفات
            ])->comment('نوع الحساب');
            
            // الوصف
            $table->text('description')->nullable()->comment('وصف الحساب');
            
            // الأرصدة
            $table->decimal('balance', 15, 2)->default(0)->comment('الرصيد الحالي');
            $table->decimal('debit_balance', 15, 2)->default(0)->comment('رصيد مدين');
            $table->decimal('credit_balance', 15, 2)->default(0)->comment('رصيد دائن');
            
            // الحالة
            $table->boolean('is_active')->default(true)->comment('نشط؟');
            
            // من أنشأ وعدّل
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('chart_group_id');
            $table->index('parent_id');
            $table->index('code');
            $table->index('account_type');
            $table->index('level');
            $table->index('is_parent');
            $table->index('is_active');
            
            // Unique constraint: code must be unique within the same chart_group
            $table->unique(['chart_group_id', 'code']);
            
            // Foreign Keys
            $table->foreign('chart_group_id')->references('id')->on('chart_groups')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('chart_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_accounts');
    }
};
