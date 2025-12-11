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
        // حذف الجدول القديم إذا كان موجوداً
        Schema::dropIfExists('audit_logs');
        
        // إنشاء جدول جديد محسّن
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            // معلومات المستخدم
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // معلومات الكيان المتأثر
            $table->string('auditable_type'); // App\Models\JournalEntry
            $table->unsignedBigInteger('auditable_id');
            $table->index(['auditable_type', 'auditable_id']);
            
            // نوع الحدث
            $table->string('event'); // created, updated, deleted, approved, posted, etc.
            
            // القيم القديمة والجديدة
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            // معلومات إضافية
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            
            // وصف اختياري
            $table->text('description')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
