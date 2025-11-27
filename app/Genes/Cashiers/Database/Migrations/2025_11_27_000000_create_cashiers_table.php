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
        // إنشاء جدول الصرافين (Cashiers)
        Schema::create('cashiers', function (Blueprint $table) {
            $table->id();
            
            // ربط الصراف بالمؤسسة (Institution)
            $table->foreignId('institution_id')
                  ->constrained('institutions') // نفترض وجود جدول institutions
                  ->comment('المؤسسة التي يتبع لها الصراف');

            // ربط الصراف بالمستخدم (User)
            $table->foreignId('user_id')
                  ->constrained('users') // نفترض وجود جدول users
                  ->comment('المستخدم المرتبط بهذا الصراف');

            // اسم الصراف (قد يكون اسم مستعار أو اسم الصندوق)
            $table->string('name')->comment('اسم الصراف/الصندوق');

            // نوع الصراف (مثل: صندوق نقدي، بنك، محفظة إلكترونية)
            $table->string('type')->default('cash')->comment('نوع الصراف (cash, bank, wallet, etc.)');

            // حالة الصراف (نشط/غير نشط)
            $table->boolean('is_active')->default(true)->comment('حالة الصراف (نشط/غير نشط)');

            // الرصيد الافتتاحي (لأغراض التتبع، قد يتم حسابه من الحركات)
            // نستخدم decimal لضمان دقة العمليات المالية
            $table->decimal('opening_balance', 15, 4)->default(0.0000)->comment('الرصيد الافتتاحي');

            // العملة الأساسية للصراف (لتقييد العمليات)
            $table->string('currency_code', 3)->default('USD')->comment('رمز العملة الأساسية للصراف (ISO 4217)');

            // حقل لربط الصراف بدليل الحسابات (Chart of Accounts)
            $table->foreignId('account_id')
                  ->nullable()
                  ->constrained('accounts') // نفترض وجود جدول accounts
                  ->comment('الحساب الفرعي المرتبط في دليل الحسابات');

            // حقل إضافي لربط الصراف بجدول الحسابات التحليلية (Analytical Accounts) إذا لزم الأمر
            $table->morphs('accountable');

            // ضمان عدم تكرار اسم الصراف داخل نفس المؤسسة
            $table->unique(['institution_id', 'name']);
            
            $table->timestamps();
            $table->softDeletes(); // لدعم الحذف الناعم
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashiers');
    }
};