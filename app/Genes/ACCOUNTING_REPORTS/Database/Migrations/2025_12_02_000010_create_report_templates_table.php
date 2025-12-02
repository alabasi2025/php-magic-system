<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// تعريف فئة الترحيل لإنشاء جدول قوالب التقارير
return new class extends Migration
{
    /**
     * تشغيل الترحيلات (إنشاء الجدول).
     *
     * @return void
     */
    public function up(): void
    {
        // التحقق من عدم وجود الجدول قبل الإنشاء لتجنب الأخطاء
        if (!Schema::hasTable('alabasi_report_templates')) {
            Schema::create('alabasi_report_templates', function (Blueprint $table) {
                // المعرف الأساسي (Primary Key)
                $table->id();

                // اسم القالب، يجب أن يكون فريدًا
                $table->string('name')->unique()->comment('اسم قالب التقرير');

                // وصف القالب، يمكن أن يكون فارغًا
                $table->text('description')->nullable()->comment('وصف تفصيلي للقالب');

                // نوع القالب (مثل: ميزانية، كشف حساب، إلخ)، مع فهرسة لتحسين الأداء
                $table->string('template_type')->index()->comment('نوع القالب (مثال: ميزانية، كشف حساب)');

                // هيكل التقرير، يُخزن كـ JSON
                $table->json('structure')->comment('هيكل التقرير المخزن بصيغة JSON');

                // معلمات إضافية للقالب، تُخزن كـ JSON، يمكن أن تكون فارغة
                $table->json('parameters')->nullable()->comment('معلمات إضافية للقالب بصيغة JSON');

                // حالة التفعيل، افتراضيًا مفعل
                $table->boolean('is_active')->default(true)->comment('حالة تفعيل القالب');

                // مفتاح خارجي للمستخدم الذي أنشأ القالب
                // نفترض وجود جدول 'users' قياسي
                $table->foreignId('created_by')
                      ->nullable() // يمكن أن يكون فارغًا إذا كان القالب نظاميًا
                      ->constrained('users')
                      ->onUpdate('cascade')
                      ->onDelete('set null')
                      ->comment('معرف المستخدم الذي أنشأ القالب');

                // طوابع الوقت (created_at و updated_at)
                $table->timestamps();
            });
        }
    }

    /**
     * عكس الترحيلات (حذف الجدول).
     *
     * @return void
     */
    public function down(): void
    {
        // حذف الجدول في حال عكس الترحيل
        Schema::dropIfExists('alabasi_report_templates');
    }
};
