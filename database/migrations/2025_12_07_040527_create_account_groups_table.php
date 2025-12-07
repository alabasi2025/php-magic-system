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
        Schema::create('account_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('اسم المجموعة');
            $table->string('code')->unique()->nullable()->comment('كود المجموعة (اختياري)');
            $table->text('description')->nullable()->comment('وصف المجموعة');
            $table->boolean('is_active')->default(true)->comment('هل المجموعة مفعلة؟');
            $table->integer('sort_order')->default(0)->comment('ترتيب العرض');
            $table->timestamps();
        });

        // إضافة حقل account_group_id لجدول chart_of_accounts
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->foreignId('account_group_id')->nullable()->after('account_type')->constrained('account_groups')->onDelete('set null')->comment('مجموعة الحساب');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropForeign(['account_group_id']);
            $table->dropColumn('account_group_id');
        });
        
        Schema::dropIfExists('account_groups');
    }
};
