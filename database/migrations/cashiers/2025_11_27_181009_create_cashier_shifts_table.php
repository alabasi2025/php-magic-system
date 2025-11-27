<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration لإنشاء جدول 'cashier_shifts'
 * هذا الجدول مخصص لتسجيل فترات عمل الصرافين (Shifts) في نظام الصرافين (Cashiers Gene).
 * يتضمن معلومات عن الصراف، وقت بدء وانتهاء الوردية، ومبالغ النقدية الافتتاحية والختامية.
 */
return new class extends Migration
{
    /**
     * تشغيل الترحيل (إنشاء الجدول).
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            // المعرف الأساسي للوردية
            $table->id();

            // مفتاح خارجي يربط الوردية بالصراف (المستخدم)
            // نفترض وجود جدول 'users' يمثل الصرافين
            $table->foreignId('cashier_id')
                  ->constrained('users')
                  ->comment('معرف الصراف (المستخدم)');

            // وقت بدء الوردية
            $table->timestamp('start_time')
                  ->comment('وقت بدء الوردية');

            // وقت انتهاء الوردية (يمكن أن يكون فارغًا إذا كانت الوردية لا تزال مفتوحة)
            $table->timestamp('end_time')
                  ->nullable()
                  ->comment('وقت انتهاء الوردية');

            // المبلغ النقدي الذي بدأ به الصراف الوردية
            $table->decimal('starting_cash', 10, 2)
                  ->comment('المبلغ النقدي الافتتاحي');

            // المبلغ النقدي الذي انتهى به الصراف الوردية (يمكن أن يكون فارغًا)
            $table->decimal('ending_cash', 10, 2)
                  ->nullable()
                  ->comment('المبلغ النقدي الختامي');

            // حالة الوردية (مثل: open, closed)
            $table->string('status', 20)
                  ->default('open')
                  ->comment('حالة الوردية (open, closed)');

            // مفتاح خارجي يربط الوردية بالفرع (إذا كان النظام متعدد الفروع)
            // نفترض وجود جدول 'branches'
            $table->foreignId('branch_id')
                  ->nullable()
                  ->constrained('branches')
                  ->comment('معرف الفرع');

            // سجلات الوقت القياسية لـ Laravel
            $table->timestamps();

            // إضافة فهرس لـ cashier_id و start_time لتحسين أداء الاستعلامات
            $table->index(['cashier_id', 'start_time']);
        });
    }

    /**
     * عكس الترحيل (حذف الجدول).
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};