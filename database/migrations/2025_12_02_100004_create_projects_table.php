<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: إنشاء جدول المشاريع
 * 
 * 💡 الفكرة:
 * المشروع هو عمل محدد بفترة زمنية وميزانية
 * يمكن أن يكون تابعاً لوحدة أو قسم
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('projects');
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            
            // الوحدة التابع لها
            $table->unsignedBigInteger('unit_id')->nullable()->comment('الوحدة');
            
            // القسم التابع له
            $table->unsignedBigInteger('department_id')->nullable()->comment('القسم');
            
            // المعلومات الأساسية
            $table->string('code', 50)->unique()->comment('كود المشروع');
            $table->string('name')->comment('اسم المشروع');
            $table->string('name_en')->nullable()->comment('الاسم بالإنجليزية');
            $table->text('description')->nullable()->comment('وصف المشروع');
            
            // نوع المشروع
            $table->enum('type', [
                'construction', 'development', 'maintenance', 
                'consulting', 'research', 'internal', 'other'
            ])->default('other')->comment('نوع المشروع');
            
            // مدير المشروع
            $table->unsignedBigInteger('manager_id')->nullable()->comment('مدير المشروع');
            
            // العميل
            $table->unsignedBigInteger('client_id')->nullable()->comment('العميل');
            $table->string('client_name')->nullable()->comment('اسم العميل');
            
            // التواريخ
            $table->date('start_date')->nullable()->comment('تاريخ البدء');
            $table->date('end_date')->nullable()->comment('تاريخ الانتهاء المتوقع');
            $table->date('actual_end_date')->nullable()->comment('تاريخ الانتهاء الفعلي');
            
            // الميزانية
            $table->decimal('budget', 15, 2)->nullable()->comment('الميزانية المخططة');
            $table->decimal('actual_cost', 15, 2)->default(0)->comment('التكلفة الفعلية');
            $table->decimal('revenue', 15, 2)->default(0)->comment('الإيرادات');
            
            // نسبة الإنجاز
            $table->decimal('progress', 5, 2)->default(0)->comment('نسبة الإنجاز %');
            
            // الحالة
            $table->enum('status', [
                'planning', 'active', 'on_hold', 'completed', 'cancelled'
            ])->default('planning')->comment('حالة المشروع');
            
            // الأولوية
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                ->default('medium')
                ->comment('الأولوية');
            
            // الموقع
            $table->string('location')->nullable()->comment('موقع المشروع');
            
            // ملاحظات
            $table->text('notes')->nullable()->comment('ملاحظات');
            
            // من أنشأ وعدّل
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('unit_id');
            $table->index('department_id');
            $table->index('code');
            $table->index('type');
            $table->index('status');
            $table->index('priority');
            $table->index('manager_id');
            $table->index('client_id');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
