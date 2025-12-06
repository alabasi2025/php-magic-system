<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for Suppliers Table
 * جدول الموردين
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('رمز المورد');
            $table->string('name')->comment('اسم المورد');
            $table->string('name_en')->nullable()->comment('الاسم بالإنجليزية');
            $table->string('contact_person')->nullable()->comment('الشخص المسؤول');
            $table->string('phone')->nullable()->comment('رقم الهاتف');
            $table->string('email')->nullable()->comment('البريد الإلكتروني');
            $table->text('address')->nullable()->comment('العنوان');
            $table->string('tax_number')->nullable()->comment('الرقم الضريبي');
            $table->enum('payment_terms', ['cash', 'credit'])->default('cash')->comment('شروط الدفع');
            $table->decimal('credit_limit', 15, 2)->default(0)->comment('حد الائتمان');
            $table->integer('credit_days')->default(0)->comment('مدة الائتمان بالأيام');
            $table->foreignId('account_id')->nullable()->constrained('chart_accounts')->nullOnDelete()->comment('الحساب المحاسبي');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('الحالة');
            $table->text('notes')->nullable()->comment('ملاحظات');
            
            // للتوافق مع النظام القديم
            $table->decimal('initial_balance', 15, 2)->default(0)->nullable()->comment('الرصيد الافتتاحي');
            $table->decimal('balance', 15, 2)->default(0)->nullable()->comment('الرصيد الحالي');
            $table->boolean('is_active')->default(true)->nullable()->comment('حالة النشاط');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('المستخدم المنشئ');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index('code');
            $table->index('name');
            $table->index('status');
            $table->index('payment_terms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
