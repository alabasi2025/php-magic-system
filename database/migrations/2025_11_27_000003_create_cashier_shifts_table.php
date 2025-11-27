<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'cashier_shifts' table.
 * This table stores information about the start and end times of cashier shifts,
 * the associated cashier, and the shift's status.
 *
 * @category Database
 * @package  CashiersGene
 * @author   Manus AI
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
        // التحقق من عدم وجود الجدول قبل الإنشاء لتجنب الأخطاء
        if (!Schema::hasTable('cashier_shifts')) {
            Schema::create('cashier_shifts', function (Blueprint $table) {
                // المعرف الأساسي للجدول
                $table->id();

                // مفتاح خارجي يربط الوردية بالصراف (المستخدم)
                // نفترض أن الصرافين هم مستخدمون في جدول 'users'
                $table->foreignId('cashier_id')
                      ->constrained('users')
                      ->comment('معرف الصراف (المستخدم) الذي قام بالوردية');

                // وقت بداية الوردية
                $table->timestamp('start_time')
                      ->comment('وقت بداية الوردية');

                // وقت نهاية الوردية (يمكن أن يكون NULL إذا كانت الوردية لا تزال نشطة)
                $table->timestamp('end_time')
                      ->nullable()
                      ->comment('وقت نهاية الوردية');

                // حالة الوردية: active, closed, suspended
                $table->enum('status', ['active', 'closed', 'suspended'])
                      ->default('active')
                      ->comment('حالة الوردية (نشطة، مغلقة، معلقة)');

                // المبلغ النقدي الأولي في بداية الوردية (للتدقيق)
                $table->decimal('starting_cash', 10, 2)
                      ->default(0.00)
                      ->comment('المبلغ النقدي الأولي في بداية الوردية');

                // المبلغ النقدي النهائي في نهاية الوردية (للتدقيق)
                $table->decimal('ending_cash', 10, 2)
                      ->nullable()
                      ->comment('المبلغ النقدي النهائي في نهاية الوردية');

                // ملاحظات إضافية حول الوردية
                $table->text('notes')
                      ->nullable()
                      ->comment('ملاحظات إضافية حول الوردية');

                // طوابع زمنية لإنشاء وتحديث السجل
                $table->timestamps();

                // إضافة فهرس لتحسين أداء الاستعلامات على المفتاح الخارجي
                $table->index('cashier_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};