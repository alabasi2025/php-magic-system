<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // إنشاء جدول الصرافين (cashiers)
        Schema::create('cashiers', function (Blueprint $table) {
            $table->id();
            
            // ربط الصراف بمستخدم النظام (الموظف)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict')
                  ->comment('المستخدم المرتبط بهذا الصراف');

            // اسم الصراف (قد يكون اسم جهاز أو نقطة بيع)
            $table->string('cashier_name')->unique()->comment('اسم الصراف/نقطة البيع');
            
            // حالة الصراف (نشط/غير نشط/مغلق)
            $table->enum('status', ['active', 'inactive', 'closed'])->default('active')->comment('حالة الصراف');

            // تحديد الوحدة والشركة ليتوافق مع معمارية الجينات (Gene Architecture)
            // افتراضياً، يتم استخدام الأعمدة الموجودة في جداول النظام الأخرى
            $table->unsignedBigInteger('unit_id')->nullable()->comment('معرف الوحدة التنظيمية');
            $table->unsignedBigInteger('company_id')->nullable()->comment('معرف الشركة');

            // مفاتيح خارجية للوحدة والشركة (يجب التأكد من وجود جداول units و companies)
            // $table->foreign('unit_id')->references('id')->on('units')->onDelete('restrict');
            // $table->foreign('company_id')->references('id')->on('companies')->onDelete('restrict');

            $table->timestamps();
            $table->softDeletes(); // لدعم الحذف الناعم

            // إضافة فهرس لسرعة البحث
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cashiers');
    }
};