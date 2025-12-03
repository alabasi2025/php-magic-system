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
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id(); // المعرف الأساسي للمحادثة
            
            // معرف المستخدم الذي أجرى المحادثة. يجب أن يكون unsignedBigInteger بدون foreign key حسب القواعد.
            $table->unsignedBigInteger('user_id'); 
            
            // اسم الأداة أو النموذج المستخدم في المحادثة (مثل: ChatGPT, DALL-E)
            $table->string('tool_name', 100); 
            
            // محتوى الرسائل المتبادلة في المحادثة، مخزن بصيغة JSON
            $table->json('messages'); 
            
            // حالة المحادثة (مثل: completed, failed, pending)
            $table->string('status', 50)->default('completed'); 
            
            // إضافة أعمدة created_at و updated_at
            $table->timestamps(); 
            
            // إضافة فهارس للأعمدة المهمة لتحسين أداء الاستعلامات
            $table->index('user_id');
            $table->index('tool_name');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};
