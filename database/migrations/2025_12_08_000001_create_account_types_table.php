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
        if (!Schema::hasTable('account_types')) {
            Schema::create('account_types', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique()->comment('مفتاح فريد لنوع الحساب');
                $table->string('name_ar')->comment('الاسم بالعربية');
                $table->string('name_en')->nullable()->comment('الاسم بالإنجليزية');
                $table->string('icon')->nullable()->comment('أيقونة Font Awesome');
                $table->text('description')->nullable()->comment('وصف نوع الحساب');
                $table->boolean('is_active')->default(true)->comment('مفعل/معطل');
                $table->boolean('is_system')->default(false)->comment('نوع نظامي (لا يمكن حذفه)');
                $table->integer('sort_order')->default(0)->comment('ترتيب العرض');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_types');
    }
};
