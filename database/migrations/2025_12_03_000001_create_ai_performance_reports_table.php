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
        // افتراض وجود جدول ai_tools لربط تقارير الأداء بالأداة المعنية
        Schema::create('ai_performance_reports', function (Blueprint $table) {
            // المفتاح الأساسي
            $table->id()->comment('المفتاح الأساسي للتقرير');

            // المفتاح الأجنبي لربط التقرير بالأداة/النموذج
            $table->foreignId('tool_id')
                  ->constrained('ai_tools') // يفترض وجود جدول ai_tools
                  ->cascadeOnDelete()
                  ->comment('معرف الأداة/النموذج الذي تم تقييم أدائه');
            
            // تاريخ التقرير
            $table->date('report_date')
                  ->index()
                  ->comment('تاريخ التقرير أو القياس');

            // اسم مقياس الأداء
            $table->string('metric_name', 100)
                  ->index()
                  ->comment('اسم مقياس الأداء (مثل الدقة، زمن الاستجابة)');

            // قيمة المقياس
            $table->decimal('metric_value', 8, 4)
                  ->comment('قيمة المقياس (مثال: 0.9523 للدقة)');

            // وحدة القياس
            $table->string('unit', 50)
                  ->comment('وحدة القياس (مثل %، مللي ثانية، درجة)');

            // حالة الأداء
            $table->enum('status', ['success', 'warning', 'critical'])
                  ->default('success')
                  ->index()
                  ->comment('حالة الأداء المبلغ عنها (نجاح، تحذير، حرج)');

            // بيانات وصفية إضافية
            $table->json('metadata')
                  ->nullable()
                  ->comment('بيانات وصفية إضافية حول ظروف الاختبار أو الإعدادات');

            // ملاحظات مفصلة
            $table->text('notes')
                  ->nullable()
                  ->comment('ملاحظات مفصلة حول التقرير أو أي انحرافات');

            // الطوابع الزمنية
            $table->timestamps();
            
            // الحذف الناعم
            $table->softDeletes()->comment('طابع زمني للحذف الناعم');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_performance_reports');
    }
};
