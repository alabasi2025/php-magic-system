<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Migration: إنشاء جدول الحسابات الوسيطة
 * 
 * 💡 الفكرة:
 * جدول يحتوي على الحسابات الوسيطة المستقلة التي يمكن ربطها بالحسابات الرئيسية
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
        Schema::create('alabasi_intermediate_accounts', function (Blueprint $table) {
            $table->id();
            
            // معلومات الحساب الوسيط
            $table->string('name', 255)->comment('اسم الحساب الوسيط');
            $table->string('code', 50)->unique()->comment('كود الحساب الوسيط');
            
            // الحساب الرئيسي المرتبط
                ->onDelete('cascade')
                ->comment('الحساب الرئيسي المرتبط');
            
            // الحالة
            $table->boolean('is_active')->default(true)->comment('هل الحساب نشط');
            
            // الوصف
            $table->text('description')->nullable()->comment('وصف الحساب');
            
            // من أنشأ وعدّل
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('main_account_id');
            $table->index('code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alabasi_intermediate_accounts');
    }
};
