<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة (إنشاء جدول الأصناف).
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            // رمز الصنف، يجب أن يكون فريداً
            $table->string('code')->unique()->comment('رمز الصنف');
            // الباركود، يمكن أن يكون فارغاً ولكنه فريد إذا وجد
            $table->string('barcode')->unique()->nullable()->comment('الباركود');
            // اسم الصنف
            $table->string('name')->comment('اسم الصنف');
            // وصف الصنف
            $table->text('description')->nullable()->comment('وصف الصنف');

            // مفتاح خارجي للفئة
            $table->foreignId('category_id')
                  ->constrained('categories') // نفترض وجود جدول categories
                  ->onUpdate('cascade')
                  ->onDelete('restrict') // لا يمكن حذف الفئة إذا كانت مرتبطة بأصناف
                  ->comment('معرف الفئة');

            // مفتاح خارجي للوحدة
            $table->foreignId('unit_id')
                  ->constrained('units') // نفترض وجود جدول units
                  ->onUpdate('cascade')
                  ->onDelete('restrict') // لا يمكن حذف الوحدة إذا كانت مرتبطة بأصناف
                  ->comment('معرف الوحدة');

            // الحد الأدنى والأقصى للمخزون ومستوى إعادة الطلب
            $table->unsignedInteger('min_stock')->default(0)->comment('الحد الأدنى للمخزون');
            $table->unsignedInteger('max_stock')->default(0)->comment('الحد الأقصى للمخزون');
            $table->unsignedInteger('reorder_level')->default(0)->comment('مستوى إعادة الطلب');

            // أسعار التكلفة والبيع
            $table->decimal('cost_price', 10, 2)->default(0.00)->comment('سعر التكلفة');
            $table->decimal('selling_price', 10, 2)->default(0.00)->comment('سعر البيع');

            // حالة التفعيل
            $table->boolean('is_active')->default(true)->comment('حالة التفعيل');
            // مسار صورة الصنف
            $table->string('image')->nullable()->comment('مسار صورة الصنف');

            $table->timestamps();
        });
    }

    /**
     * عكس الهجرة (حذف جدول الأصناف).
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
