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
        Schema::create('ai_credits', function (Blueprint $table) {
            // المعرف الأساسي للجدول
            $table->id()->comment('المعرف الأساسي للرصيد');

            // العلاقة مع جدول المستخدمين
            $table->unsignedBigInteger('user_id')->comment('معرف المستخدم المالك لهذا الرصيد');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index('user_id'); // فهرس لسرعة البحث عن رصيد مستخدم معين

            // الرصيد الحالي
            $table->bigInteger('balance')->default(0)->comment('الرصيد الحالي من وحدات الذكاء الاصطناعي. يستخدم bigInteger لدعم أعداد كبيرة.');
            $table->index('balance'); // فهرس للبحث والترتيب حسب الرصيد

            // حالة حساب الرصيد
            $table->enum('status', ['active', 'suspended', 'expired'])->default('active')->comment('حالة حساب الرصيد (نشط، معلق، منتهي الصلاحية)');
            $table->index('status'); // فهرس للبحث حسب الحالة

            // تاريخ ووقت آخر نشاط
            $table->timestamp('last_activity_at')->nullable()->comment('تاريخ ووقت آخر نشاط أو استخدام للرصيد');

            // بيانات إضافية
            $table->json('metadata')->nullable()->comment('بيانات إضافية بصيغة JSON (مثل تفاصيل الخطة أو المصدر)');

            // أعمدة الوقت القياسية (created_at, updated_at)
            $table->timestamps();

            // الحذف الناعم (softDeletes) لأغراض التدقيق
            $table->softDeletes()->comment('تاريخ الحذف الناعم لأغراض التدقيق والسجلات التاريخية');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_credits');
    }
};
