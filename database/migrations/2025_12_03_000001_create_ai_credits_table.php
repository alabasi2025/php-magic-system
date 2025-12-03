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
            // العمود الأساسي (Primary Key)
            $table->id()->comment('المعرف الفريد لسجل الرصيد');

            // معرف المستخدم (User ID)
            // مطلوب أن يكون unsignedBigInteger وبدون foreign key
            $table->unsignedBigInteger('user_id')->index()->comment('معرف المستخدم الذي يمتلك الرصيد');

            // نوع العملية (Transaction Type)
            // يحدد ما إذا كانت العملية إضافة (credit) أو خصم (debit)
            $table->enum('transaction_type', ['credit', 'debit'])->comment('نوع العملية: إضافة رصيد (credit) أو خصم رصيد (debit)');

            // مصدر الرصيد أو سبب الاستخدام
            $table->string('source')->index()->comment('مصدر الرصيد (مثل: شراء، مكافأة، استخدام أداة) أو سبب الخصم');

            // نوع الرصيد (Credit Type)
            // لتحديد نوع الرصيد المستخدم (مثل: نص، صورة، عام)
            $table->string('credit_type')->comment('نوع الرصيد المستخدم (مثل: نص، صورة، عام)');

            // كمية الرصيد
            // يمكن أن تكون سالبة في حالة الخصم، لكننا نستخدم transaction_type لتحديد ذلك، لذا نستخدم unsignedInteger للكمية المطلقة
            $table->unsignedInteger('amount')->comment('كمية الرصيد التي تم إضافتها أو خصمها');

            // الرصيد المتبقي بعد هذه العملية (للتدقيق)
            $table->bigInteger('balance_after_transaction')->comment('رصيد المستخدم الإجمالي بعد إتمام هذه العملية');

            // تفاصيل إضافية
            $table->text('description')->nullable()->comment('وصف تفصيلي للعملية');

            // تاريخ انتهاء صلاحية الرصيد (إذا كان الرصيد له تاريخ انتهاء)
            $table->timestamp('expires_at')->nullable()->comment('تاريخ انتهاء صلاحية الرصيد');

            // أعمدة الوقت
            $table->timestamps();

            // Soft Deletes
            $table->softDeletes()->comment('تاريخ ووقت حذف السجل بشكل منطقي');
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
