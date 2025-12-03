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
        Schema::create('ai_rate_limits', function (Blueprint $table) {
            $table->id(); // معرف السجل الأساسي
            $table->unsignedBigInteger('user_id')->comment('معرف المستخدم الذي قام بالطلب');
            $table->string('endpoint')->comment('نقطة النهاية (API Endpoint) التي تم الوصول إليها');
            $table->integer('requests_count')->default(0)->comment('عدد الطلبات المستهلكة في النافذة الزمنية');
            $table->timestamp('window_start')->comment('بداية النافذة الزمنية لتطبيق الحد');
            $table->timestamp('window_end')->comment('نهاية النافذة الزمنية لتطبيق الحد');
            $table->timestamps(); // تاريخ الإنشاء والتحديث

            // إضافة فهارس لتحسين أداء الاستعلامات
            $table->index(['user_id', 'endpoint']);
            $table->index('window_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_rate_limits');
    }
};