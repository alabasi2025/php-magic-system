<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرات (Migrations).
     */
    public function up(): void
    {
        // إنشاء جدول رسائل الدردشة بالذكاء الاصطناعي
        Schema::create('ai_chat_messages', function (Blueprint $table) {
            $table->id(); // المعرف الأساسي للرسالة (unsigned big integer auto-increment)
            
            // معرف المحادثة التي تنتمي إليها الرسالة
            $table->unsignedBigInteger('conversation_id'); 
            
            // دور المرسل (مثل: مستخدم، مساعد، نظام)
            $table->enum('role', ['user', 'assistant', 'system']); 
            
            // محتوى الرسالة النصي
            $table->text('content'); 
            
            // عدد التوكنات المستخدمة في هذه الرسالة
            $table->unsignedInteger('tokens_used')->default(0); 
            
            // طوابع الوقت (created_at و updated_at)
            $table->timestamps(); 
            
            // إضافة فهارس للأعمدة المهمة لتحسين أداء الاستعلامات
            $table->index('conversation_id');
            $table->index('role');
        });
    }

    /**
     * عكس الهجرات (Rollback).
     */
    public function down(): void
    {
        // حذف جدول رسائل الدردشة بالذكاء الاصطناعي إذا كان موجوداً
        Schema::dropIfExists('ai_chat_messages');
    }
};
