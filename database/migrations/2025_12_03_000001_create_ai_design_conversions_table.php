<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرات (Migrations).
     */
    public function up(): void
    {
        Schema::create('ai_design_conversions', function (Blueprint $table) {
            // المعرف الأساسي للتحويل
            $table->id();

            // معرف المستخدم الذي أجرى التحويل، مفتاح خارجي يربط بجدول 'users'
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->comment('معرف المستخدم');

            // نوع التصميم الذي تم تحويله (مثل صورة، فيديو، نموذج ثلاثي الأبعاد)
            $table->string('design_type', 50)->index()->comment('نوع التصميم');

            // بيانات الإدخال أو إعدادات التحويل بتنسيق JSON
            $table->json('source_data')->nullable()->comment('بيانات الإدخال');

            // بيانات الإخراج أو مرجع لملف النتيجة بتنسيق JSON
            $table->json('result_data')->nullable()->comment('بيانات الإخراج');

            // حالة التحويل (قيد الانتظار، مكتمل، فاشل)
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])
                  ->default('pending')
                  ->index()
                  ->comment('حالة التحويل');

            // مدة التحويل بالمللي ثانية
            $table->unsignedInteger('duration_ms')->nullable()->comment('مدة التحويل بالمللي ثانية');

            // التكلفة المرتبطة بالتحويل (باستخدام 8 أرقام إجمالية و 4 أرقام عشرية)
            $table->decimal('cost', 8, 4)->default(0.0000)->comment('التكلفة');

            // تاريخ ووقت الإنشاء والتحديث
            $table->timestamps();

            // الحذف الناعم (Soft Deletes)
            $table->softDeletes()->comment('تاريخ الحذف الناعم');
        });
    }

    /**
     * عكس الهجرات (Revert Migrations).
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_design_conversions');
    }
};
