<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Resource Generations Table
 * إنشاء جدول سجل توليد API Resources
 *
 * @version v3.30.0
 * @author Manus AI
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * تنفيذ الترحيل
     */
    public function up(): void
    {
        if (!Schema::hasTable('resource_generations')) {
            Schema::create('resource_generations', function (Blueprint $table) {
            $table->id();
            
            // معلومات أساسية - Basic Information
            $table->string('name'); // UserResource
            $table->enum('type', ['single', 'collection', 'nested'])->default('single');
            $table->string('model')->nullable(); // User
            
            // البيانات - Data
            $table->json('attributes'); // ['id', 'name', 'email']
            $table->json('relations')->nullable(); // ['posts', 'comments']
            $table->json('conditional_attributes')->nullable(); // Conditional fields
            $table->json('options')->nullable(); // Additional options
            
            // الملفات - Files
            $table->text('file_path'); // app/Http/Resources/UserResource.php
            $table->longText('content'); // Generated content
            
            // الحالة - Status
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            
            // AI Metadata
            $table->boolean('ai_generated')->default(false);
            $table->text('ai_prompt')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('name');
            $table->index('type');
            $table->index('model');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     * التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_generations');
    }
};
