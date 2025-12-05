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
        if (!Schema::hasTable('code_metrics')) {
            Schema::create('code_metrics', function (Blueprint $table) {
            $table->id();
            
            // معلومات الإصدار
            $table->string('version', 20)->index();
            $table->timestamp('analyzed_at');
            
            // مقاييس عامة
            $table->integer('total_files')->default(0);
            $table->integer('total_lines')->default(0);
            $table->integer('logical_lines')->default(0);
            $table->integer('comment_lines')->default(0);
            $table->integer('blank_lines')->default(0);
            
            // مقاييس التعقيد
            $table->decimal('avg_cyclomatic_complexity', 10, 2)->default(0);
            $table->integer('max_cyclomatic_complexity')->default(0);
            $table->decimal('avg_cognitive_complexity', 10, 2)->default(0);
            $table->integer('max_cognitive_complexity')->default(0);
            
            // مقاييس الحجم
            $table->decimal('avg_function_size', 10, 2)->default(0);
            $table->integer('max_function_size')->default(0);
            $table->decimal('avg_class_size', 10, 2)->default(0);
            $table->integer('max_class_size')->default(0);
            
            // مقاييس الجودة (ISO 5055)
            $table->decimal('security_score', 5, 2)->default(0);
            $table->integer('security_issues')->default(0);
            $table->decimal('reliability_score', 5, 2)->default(0);
            $table->integer('reliability_issues')->default(0);
            $table->decimal('performance_score', 5, 2)->default(0);
            $table->integer('performance_issues')->default(0);
            $table->decimal('maintainability_score', 5, 2)->default(0);
            $table->integer('maintainability_issues')->default(0);
            
            // مقاييس التكرار
            $table->decimal('duplication_percentage', 5, 2)->default(0);
            $table->integer('duplicated_blocks')->default(0);
            $table->integer('duplicated_lines')->default(0);
            
            // مقاييس التوثيق
            $table->decimal('documentation_percentage', 5, 2)->default(0);
            $table->integer('documented_functions')->default(0);
            $table->integer('total_functions')->default(0);
            $table->integer('documented_classes')->default(0);
            $table->integer('total_classes')->default(0);
            
            // مقاييس الاختبار
            $table->decimal('test_coverage', 5, 2)->nullable();
            $table->decimal('branch_coverage', 5, 2)->nullable();
            $table->integer('total_tests')->default(0);
            
            // مقاييس الاعتماديات
            $table->integer('total_dependencies')->default(0);
            $table->integer('outdated_dependencies')->default(0);
            $table->integer('vulnerable_dependencies')->default(0);
            
            // النتيجة الإجمالية
            $table->decimal('overall_score', 5, 2)->default(0);
            $table->string('grade', 2)->default('F');
            
            // البيانات التفصيلية (JSON)
            $table->json('detailed_analysis')->nullable();
            $table->json('issues')->nullable();
            $table->json('recommendations')->nullable();
            $table->json('top_complex_files')->nullable();
            $table->json('top_security_issues')->nullable();
            
            // الإحصائيات الإضافية
            $table->integer('analysis_duration_seconds')->default(0);
            $table->string('analyzer_version', 20)->default('1.0.0');
            
            $table->timestamps();
            
            // Indexes
            $table->index('overall_score');
            $table->index('grade');
            $table->index('analyzed_at');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_metrics');
    }
};
