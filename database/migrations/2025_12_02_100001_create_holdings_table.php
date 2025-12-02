<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: إنشاء جدول الشركات القابضة
 * 
 * 💡 الفكرة:
 * الشركة القابضة هي المستوى الأعلى في الهيكل التنظيمي
 * تحتوي على عدة وحدات ومشاريع وأقسام
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('holdings');
        Schema::create('holdings', function (Blueprint $table) {
            $table->id();
            
            // المعلومات الأساسية
            $table->string('code', 50)->unique()->comment('كود الشركة القابضة');
            $table->string('name')->comment('اسم الشركة القابضة');
            $table->string('name_en')->nullable()->comment('الاسم بالإنجليزية');
            
            // معلومات الاتصال
            $table->string('email')->nullable()->comment('البريد الإلكتروني');
            $table->string('phone')->nullable()->comment('الهاتف');
            $table->string('fax')->nullable()->comment('الفاكس');
            $table->string('website')->nullable()->comment('الموقع الإلكتروني');
            
            // العنوان
            $table->text('address')->nullable()->comment('العنوان');
            $table->string('city')->nullable()->comment('المدينة');
            $table->string('country')->nullable()->comment('الدولة');
            $table->string('postal_code', 20)->nullable()->comment('الرمز البريدي');
            
            // المعلومات القانونية
            $table->string('tax_number', 50)->nullable()->comment('الرقم الضريبي');
            $table->string('commercial_register', 50)->nullable()->comment('السجل التجاري');
            $table->string('legal_form')->nullable()->comment('الشكل القانوني');
            
            // المعلومات المالية
            $table->string('currency', 3)->default('SAR')->comment('العملة الأساسية');
            $table->string('fiscal_year_start', 5)->default('01-01')->comment('بداية السنة المالية (MM-DD)');
            
            // الحالة
            $table->boolean('is_active')->default(true)->comment('نشط/غير نشط');
            
            // الشعار والصور
            $table->string('logo')->nullable()->comment('الشعار');
            
            // ملاحظات
            $table->text('notes')->nullable()->comment('ملاحظات');
            
            // من أنشأ وعدّل
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('code');
            $table->index('is_active');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holdings');
    }
};
