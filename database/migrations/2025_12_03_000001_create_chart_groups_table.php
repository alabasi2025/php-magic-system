<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: إنشاء جدول مجموعات الأدلة المحاسبية
 * 
 * 💡 الفكرة:
 * تقسيم الدليل المحاسبي إلى أدلة فرعية مبسطة حسب طبيعة العمل
 * بدلاً من دليل واحد ضخم ومعقد
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chart_groups', function (Blueprint $table) {
            $table->id();
            
            // الوحدة المرتبطة
            $table->unsignedBigInteger('unit_id')->comment('الوحدة');
            
            // المعلومات الأساسية
            $table->string('code', 50)->unique()->comment('كود المجموعة');
            $table->string('name')->comment('اسم المجموعة');
            $table->string('name_en')->nullable()->comment('الاسم بالإنجليزية');
            
            // نوع الدليل
            $table->enum('type', [
                'payroll',          // أعمال الموظفين
                'final_accounts',   // الحسابات النهائية
                'assets',           // الأصول
                'budget',           // الميزانية
                'projects',         // المشاريع
                'inventory',        // المخزون
                'sales',            // المبيعات
                'purchases',        // المشتريات
                'custom'            // مخصص
            ])->default('custom')->comment('نوع الدليل');
            
            // الوصف
            $table->text('description')->nullable()->comment('وصف المجموعة');
            
            // التخصيص
            $table->string('icon', 100)->nullable()->comment('أيقونة');
            $table->string('color', 20)->nullable()->comment('لون مميز');
            
            // الحالة والترتيب
            $table->boolean('is_active')->default(true)->comment('نشط؟');
            $table->integer('sort_order')->default(0)->comment('ترتيب العرض');
            
            // من أنشأ وعدّل
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('unit_id');
            $table->index('code');
            $table->index('type');
            $table->index('is_active');
            $table->index('sort_order');
            
            // Foreign Keys
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_groups');
    }
};
