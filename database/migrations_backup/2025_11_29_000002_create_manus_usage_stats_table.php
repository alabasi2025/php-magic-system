<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ترحيل لإنشاء جدول إحصائيات استخدام Manus API.
 *
 * هذا الجدول يخزن بيانات الاستخدام المجمعة (يومية، أسبوعية، شهرية)
 * لكل مشروع ومحطة ومستخدم.
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
        // التحقق أولاً من عدم وجود الجدول لتجنب الأخطاء
        if (!Schema::hasTable('manus_usage_stats')) {
            Schema::create('manus_usage_stats', function (Blueprint $table) {
                // الحقل الأساسي
                $table->id();

                // نوع الفترة الإحصائية (يومي، أسبوعي، شهري)
                $table->enum('period_type', ['daily', 'weekly', 'monthly'])
                      ->comment('نوع الفترة الإحصائية: يومي، أسبوعي، شهري');

                // بداية ونهاية الفترة الإحصائية
                $table->date('period_start')->comment('تاريخ بداية الفترة الإحصائية');
                $table->date('period_end')->comment('تاريخ نهاية الفترة الإحصائية');

                // مفاتيح خارجية للربط بالكيانات الأساسية
                // نفترض وجود جداول 'projects', 'stations', 'users'
                $table->unsignedBigInteger('project_id')->index()->comment('معرف المشروع');
                $table->unsignedBigInteger('station_id')->index()->comment('معرف المحطة');
                $table->unsignedBigInteger('user_id')->index()->comment('معرف المستخدم');

                // إحصائيات الطلبات
                $table->unsignedInteger('total_requests')->default(0)->comment('إجمالي عدد الطلبات');
                $table->unsignedInteger('successful_requests')->default(0)->comment('عدد الطلبات الناجحة');
                $table->unsignedInteger('failed_requests')->default(0)->comment('عدد الطلبات الفاشلة');

                // إحصائيات الرموز والتكلفة
                $table->unsignedBigInteger('total_tokens')->default(0)->comment('إجمالي عدد الرموز (Tokens) المستخدمة');
                // استخدام decimal للتكلفة لضمان الدقة المالية
                $table->decimal('total_cost', 10, 4)->default(0.0000)->comment('إجمالي التكلفة المقدرة');

                // إحصائيات الأداء
                // استخدام float أو decimal لمتوسط المدة لضمان الدقة
                $table->float('avg_duration_ms')->default(0.0)->comment('متوسط مدة الاستجابة بالمللي ثانية');

                // تفصيل أنواع الطلبات
                $table->unsignedInteger('chat_requests')->default(0)->comment('عدد طلبات الدردشة');
                $table->unsignedInteger('completion_requests')->default(0)->comment('عدد طلبات الإكمال');

                // فهرس مركب لضمان عدم تكرار الإحصائيات لنفس الفترة والكيانات
                $table->unique(['period_type', 'period_start', 'project_id', 'station_id', 'user_id'], 'manus_stats_unique');

                // الطوابع الزمنية
                $table->timestamps();

                // تعريف المفاتيح الخارجية (اختياري، ولكن يفضل لسلامة البيانات)
                // $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
                // $table->foreign('station_id')->references('id')->on('stations')->onDelete('cascade');
                // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * عكس الترحيل (حذف الجدول).
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('manus_usage_stats');
    }
};
