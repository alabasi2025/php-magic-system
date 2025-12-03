<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 🧬 Gene: FACTORY_TEMPLATES
 * Migration: إنشاء جدول factory_templates
 * 
 * @version 1.0.0
 * @since 2025-12-03
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("factory_templates", function (Blueprint $table) {
            $table->id();

            // الأعمدة الأساسية
            $table->string("name");
            $table->text("description")->nullable();
            $table->string("category")->default("general");
            $table->string("model_name");
            $table->string("table_name")->nullable();

            // بيانات القالب
            $table->json("schema");

            // الخصائص
            $table->boolean("is_public")->default(true);
            $table->unsignedInteger("usage_count")->default(0);
            $table->decimal("rating", 3, 2)->default(0.00);

            // من أنشأ وعدّل
            $table->foreignId("created_by")->nullable()->constrained("users");
            $table->foreignId("updated_by")->nullable()->constrained("users");

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("factory_templates");
    }
};
