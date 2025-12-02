<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: إنشاء جدول الأقسام
 * 
 * 💡 الفكرة:
 * القسم هو وحدة تنظيمية داخل الوحدة
 * مثل: قسم المحاسبة، قسم الموارد البشرية، قسم المبيعات
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('departments');
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            
            // الوحدة التابع لها
            $table->unsignedBigInteger('unit_id')->comment('الوحدة');
            
            // القسم الأم (للأقسام الفرعية)
            $table->unsignedBigInteger('parent_id')->nullable()->comment('القسم الأم');
            
            // المعلومات الأساسية
            $table->string('code', 50)->unique()->comment('كود القسم');
            $table->string('name')->comment('اسم القسم');
            $table->string('name_en')->nullable()->comment('الاسم بالإنجليزية');
            
            // نوع القسم
            $table->enum('type', [
                'accounting', 'hr', 'sales', 'purchasing', 
                'it', 'marketing', 'operations', 'admin', 'other'
            ])->default('other')->comment('نوع القسم');
            
            // المدير
            $table->unsignedBigInteger('manager_id')->nullable()->comment('مدير القسم');
            
            // معلومات الاتصال
            $table->string('email')->nullable()->comment('البريد الإلكتروني');
            $table->string('phone')->nullable()->comment('الهاتف');
            $table->string('extension', 10)->nullable()->comment('التحويلة');
            
            // الموقع
            $table->string('location')->nullable()->comment('الموقع/المبنى/الطابق');
            
            // الميزانية
            $table->decimal('budget', 15, 2)->nullable()->comment('الميزانية السنوية');
            
            // الحالة
            $table->boolean('is_active')->default(true)->comment('نشط/غير نشط');
            
            // ملاحظات
            $table->text('notes')->nullable()->comment('ملاحظات');
            
            // من أنشأ وعدّل
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('unit_id');
            $table->index('parent_id');
            $table->index('code');
            $table->index('type');
            $table->index('is_active');
            $table->index('manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
