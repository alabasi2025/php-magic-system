<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة (إنشاء الجدول).
     */
    public function up(): void
    {
        // التأكد من وجود جداول 'warehouses', 'items', و 'users' قبل إنشاء هذا الجدول
        // في بيئة Laravel حقيقية، يجب أن تكون هذه الهجرات قد نُفذت مسبقاً.

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            // ربط الحركة بالمخزن
            $table->foreignId('warehouse_id')->constrained('warehouses')->comment('المخزن الذي تمت فيه الحركة');
            // ربط الحركة بالصنف
            $table->foreignId('item_id')->constrained('items')->comment('الصنف الذي تأثر بالحركة');
            // نوع الحركة (مثل: in, out, adjustment, transfer)
            $table->enum('movement_type', ['in', 'out', 'adjustment', 'transfer'])->comment('نوع الحركة: دخول، خروج، تسوية، نقل');
            // نوع المرجع (Polymorphic Relation)
            $table->string('reference_type')->comment('نوع المستند المرجعي (مثل فاتورة، أمر شراء)');
            // معرف المرجع
            $table->unsignedBigInteger('reference_id')->comment('معرف المستند المرجعي');
            // الكمية (يمكن أن تكون سالبة للحركات الخارجة)
            $table->decimal('quantity', 10, 2)->comment('الكمية المتأثرة بالحركة');
            // سعر الوحدة (لأغراض التكلفة)
            $table->decimal('unit_price', 10, 2)->nullable()->comment('سعر الوحدة وقت الحركة');
            // الرصيد قبل الحركة
            $table->decimal('balance_before', 10, 2)->comment('رصيد الصنف في المخزن قبل الحركة');
            // الرصيد بعد الحركة
            $table->decimal('balance_after', 10, 2)->comment('رصيد الصنف في المخزن بعد الحركة');
            // تاريخ ووقت الحركة
            $table->timestamp('date')->useCurrent()->comment('تاريخ ووقت الحركة');
            // المستخدم الذي أنشأ الحركة
            $table->foreignId('created_by')->nullable()->constrained('users')->comment('المستخدم الذي سجل الحركة');
            $table->timestamps();

            // إضافة فهرس لسرعة البحث عن المراجع
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * عكس الهجرة (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
