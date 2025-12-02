<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Migration: إنشاء جدول الحسابات الوسيطة العامة
 * 
 * 💡 الفكرة:
 * جدول للحسابات الوسيطة العامة التي لا ترتبط بحساب رئيسي محدد
 * 
 * @version 1.0.0
 * @since 2025-12-02
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alabasi_general_intermediate_accounts', function (Blueprint $table) {
            $table->id();
            
            // معلومات الحساب
            $table->string('name', 255)->comment('اسم الحساب');
            $table->string('code', 50)->unique()->comment('كود الحساب');
            
            // الرصيد
            $table->decimal('balance', 15, 2)->default(0)->comment('الرصيد الحالي');
            
            // الوصف
            $table->text('description')->nullable()->comment('وصف الحساب');
            
            // من أنشأ وعدّل
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alabasi_general_intermediate_accounts');
    }
};
