<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: إنشاء جدول الوحدات
 * 
 * 💡 الفكرة:
 * الوحدة هي كيان تنظيمي تابع للشركة القابضة
 * يمكن أن تكون شركة، فرع، أو مؤسسة
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('units');
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            
            // الشركة القابضة
            $table->unsignedBigInteger('holding_id')->comment('الشركة القابضة');
            
            // الوحدة الأم (للوحدات الفرعية)
            $table->unsignedBigInteger('parent_id')->nullable()->comment('الوحدة الأم');
            
            // المعلومات الأساسية
            $table->string('code', 50)->unique()->comment('كود الوحدة');
            $table->string('name')->comment('اسم الوحدة');
            $table->string('name_en')->nullable()->comment('الاسم بالإنجليزية');
            
            // نوع الوحدة
            $table->enum('type', ['company', 'branch', 'division', 'subsidiary', 'other'])
                ->default('company')
                ->comment('نوع الوحدة');
            
            // معلومات الاتصال
            $table->string('email')->nullable()->comment('البريد الإلكتروني');
            $table->string('phone')->nullable()->comment('الهاتف');
            $table->string('fax')->nullable()->comment('الفاكس');
            
            // العنوان
            $table->text('address')->nullable()->comment('العنوان');
            $table->string('city')->nullable()->comment('المدينة');
            $table->string('country')->nullable()->comment('الدولة');
            $table->string('postal_code', 20)->nullable()->comment('الرمز البريدي');
            
            // المعلومات القانونية
            $table->string('tax_number', 50)->nullable()->comment('الرقم الضريبي');
            $table->string('commercial_register', 50)->nullable()->comment('السجل التجاري');
            
            // المدير
            $table->unsignedBigInteger('manager_id')->nullable()->comment('مدير الوحدة');
            
            // الحالة
            $table->boolean('is_active')->default(true)->comment('نشط/غير نشط');
            $table->date('start_date')->nullable()->comment('تاريخ البدء');
            $table->date('end_date')->nullable()->comment('تاريخ الانتهاء');
            
            // ملاحظات
            $table->text('notes')->nullable()->comment('ملاحظات');
            
            // من أنشأ وعدّل
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('holding_id');
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
        Schema::dropIfExists('units');
    }
};
