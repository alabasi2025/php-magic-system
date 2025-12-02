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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            
            // العلاقات
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('cascade')->comment('الوحدة التنظيمية');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade')->comment('القسم');
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->onDelete('cascade')->comment('الحساب الأب');
            
            // بيانات الحساب
            $table->string('code', 50)->unique()->comment('رقم الحساب');
            $table->string('name')->comment('اسم الحساب');
            $table->string('name_en')->nullable()->comment('الاسم بالإنجليزية');
            
            // تصنيف الحساب
            $table->enum('type', [
                'asset',        // أصول
                'liability',    // خصوم
                'equity',       // حقوق الملكية
                'revenue',      // إيرادات
                'expense',      // مصروفات
            ])->comment('نوع الحساب');
            
            // الهيكل الشجري
            $table->integer('level')->default(1)->comment('المستوى في الشجرة');
            $table->boolean('is_parent')->default(false)->comment('هل يحتوي على حسابات فرعية');
            
            // معلومات إضافية
            $table->text('description')->nullable()->comment('وصف الحساب');
            $table->boolean('is_active')->default(true)->comment('نشط/معطل');
            
            // تتبع المستخدمين
            $table->foreignId('created_by')->nullable()->constrained('users')->comment('المنشئ');
            $table->foreignId('updated_by')->nullable()->constrained('users')->comment('المحدث');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('code');
            $table->index('unit_id');
            $table->index('department_id');
            $table->index('parent_id');
            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
