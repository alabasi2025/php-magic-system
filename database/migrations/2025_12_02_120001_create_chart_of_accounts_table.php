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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            
            // الربط بالوحدة
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            
            // الهيكلية الهرمية
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->onDelete('cascade');
            
            // معلومات الحساب الأساسية
            $table->string('code', 50)->unique(); // رقم الحساب (قابل للتعديل)
            $table->string('name'); // اسم الحساب بالعربية
            $table->string('name_en')->nullable(); // اسم الحساب بالإنجليزية
            $table->text('description')->nullable(); // وصف الحساب
            
            // نوع الحساب في الهيكلية
            $table->enum('account_level', ['parent', 'sub'])->default('sub'); // رئيسي أو فرعي
            
            // نوع الحساب المحاسبي (للحسابات الفرعية فقط)
            $table->enum('account_type', ['asset', 'liability', 'equity', 'revenue', 'expense'])->nullable();
            
            // نوع الحساب التحليلي (للحسابات الفرعية فقط)
            $table->enum('analytical_type', [
                'cash_box',      // صندوق
                'bank',          // بنك
                'cashier',       // صراف
                'wallet',        // محفظة
                'customer',      // عميل
                'supplier',      // مورد
                'warehouse',     // مخزن
                'employee',      // موظف
                'partner',       // شريك
                'other'          // أخرى
            ])->nullable();
            
            // العملات المفضلة (JSON array)
            $table->json('preferred_currencies')->nullable(); // ['USD', 'EUR', 'SAR']
            
            // حالة الحساب
            $table->boolean('is_active')->default(true);
            $table->boolean('is_root')->default(false); // حساب جذر (لا يمكن تعديل رقمه)
            
            // معلومات إضافية
            $table->integer('level')->default(1); // مستوى الحساب في الشجرة
            $table->string('full_code')->nullable(); // الكود الكامل (مع الأب)
            
            // تتبع التعديلات
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('unit_id');
            $table->index('parent_id');
            $table->index('code');
            $table->index('account_type');
            $table->index('analytical_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
