<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @gene CashiersGene
 * @task 2019
 * @category Backend
 * @description إنشاء جدول `cashier_shifts` لتتبع ورديات الصرافين (Cashiers) في النظام.
 *              يتضمن الجدول معلومات عن بداية ونهاية الوردية، الرصيد الافتتاحي، الرصيد الختامي، وحالة الوردية.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            
            // معلومات الصراف والوردية
            $table->foreignId('user_id')->constrained('users')->comment('معرف المستخدم (الصراف) الذي قام بفتح الوردية');
            $table->timestamp('start_time')->comment('وقت بداية الوردية');
            $table->timestamp('end_time')->nullable()->comment('وقت نهاية الوردية (يمكن أن يكون فارغاً إذا كانت الوردية مفتوحة)');
            
            // حالة الوردية
            $table->enum('status', ['open', 'closed', 'reconciled'])->default('open')->comment('حالة الوردية: مفتوحة، مغلقة، تمت تسويتها');
            
            // الأرصدة المالية
            $table->decimal('opening_balance', 15, 4)->default(0.0000)->comment('الرصيد الافتتاحي للوردية');
            $table->decimal('closing_balance', 15, 4)->nullable()->comment('الرصيد الختامي الفعلي عند إغلاق الوردية');
            $table->decimal('system_balance', 15, 4)->default(0.0000)->comment('الرصيد المحسوب بواسطة النظام (للمقارنة)');
            $table->decimal('difference', 15, 4)->default(0.0000)->comment('الفرق بين الرصيد الختامي الفعلي ورصيد النظام');
            
            // ملاحظات
            $table->text('notes')->nullable()->comment('ملاحظات حول الوردية أو عملية التسوية');
            
            // القيود الإضافية
            $table->unique(['user_id', 'start_time']); // لضمان عدم وجود ورديتين تبدآن في نفس الوقت لنفس الصراف
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};