<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// تعريف الترحيل لإنشاء جدول بنود الميزانية
return new class extends Migration
{
    /**
     * تشغيل الترحيل (إنشاء الجدول).
     *
     * @return void
     */
    public function up(): void
    {
        Schema::dropIfExists('alabasi_budget_items');
        // إنشاء جدول 'alabasi_budget_items'
        Schema::create('alabasi_budget_items', function (Blueprint $table) {
            // العمود الأساسي: معرف فريد لبند الميزانية
            $table->id();

            // العمود الأجنبي: يربط البند بجدول الميزانيات (budgets)
            $table->unsignedBigInteger('budget_id')->comment('معرف الميزانية المرتبط بها البند');

            // العمود: فئة البند (مثل: رواتب، إيجار، تسويق)
            $table->string('category', 100)->comment('فئة بند الميزانية');

            // العمود: وصف تفصيلي للبند
            $table->text('description')->comment('وصف تفصيلي لبند الميزانية');

            // العمود: المبلغ المخطط له (15 رقمًا إجماليًا، 2 بعد الفاصلة)
            $table->decimal('planned_amount', 15, 2)->comment('المبلغ المخطط له');

            // العمود: المبلغ الفعلي المنفق (يمكن أن يكون فارغًا في البداية)
            $table->decimal('actual_amount', 15, 2)->nullable()->comment('المبلغ الفعلي المنفق');

            // العمود: الفرق بين المخطط والفعلي (يمكن أن يكون فارغًا، ويتم حسابه في التطبيق)
            $table->decimal('variance', 15, 2)->nullable()->comment('الفرق بين المبلغ المخطط والفعلي');

            // العمود: ملاحظات إضافية حول البند
            $table->text('notes')->nullable()->comment('ملاحظات إضافية');

            // أعمدة الطوابع الزمنية: created_at و updated_at
            $table->timestamps();
            $table->softDeletes();

            // إضافة فهرس لتحسين أداء الاستعلامات على budget_id
            $table->index('budget_id');
        });
    }

    /**
     * التراجع عن الترحيل (حذف الجدول).
     *
     * @return void
     */
    public function down(): void
    {
        // حذف جدول 'alabasi_budget_items' إذا كان موجودًا
        Schema::dropIfExists('alabasi_budget_items');
    }
};
