<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_tools_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // معرف المستخدم
            $table->string('tool_name'); // اسم الأداة
            $table->integer('usage_count')->default(0); // عدد مرات الاستخدام
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('tool_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_tools_usage');
    }
};
