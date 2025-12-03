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
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, text, boolean, json
            $table->string('group')->default('general'); // general, ai, system, etc
            $table->text('description')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();
        });

        // إضافة بعض الإعدادات الافتراضية
        DB::table('ai_settings')->insert([
            [
                'key' => 'manus_api_key',
                'value' => '',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'Manus AI API Key - يستخدم للتواصل مع Manus AI',
                'is_encrypted' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'ai_agent_profile',
                'value' => 'manus-1.5',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'نموذج Manus AI المستخدم (manus-1.5 أو manus-1.5-lite)',
                'is_encrypted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'ai_task_mode',
                'value' => 'chat',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'وضع المهمة (chat, adaptive, agent)',
                'is_encrypted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};
