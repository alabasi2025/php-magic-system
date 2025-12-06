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
        Schema::table('cash_boxes', function (Blueprint $table) {
            // الحساب في الدليل المحاسبي
            $table->unsignedBigInteger('chart_account_id')->nullable()->after('intermediate_account_id')->comment('الحساب في الدليل المحاسبي');
            
            // مسؤول الصندوق
            $table->unsignedBigInteger('responsible_user_id')->nullable()->after('chart_account_id')->comment('مسؤول الصندوق');
            
            // العملات المستخدمة في الصندوق (JSON)
            $table->json('currencies')->nullable()->after('responsible_user_id')->comment('العملات المستخدمة في الصندوق');
            
            // الحد الأقصى والأدنى للرصيد
            $table->decimal('max_balance', 15, 2)->nullable()->after('balance')->comment('الحد الأقصى للرصيد');
            $table->decimal('min_balance', 15, 2)->nullable()->after('max_balance')->comment('الحد الأدنى للرصيد');
            
            // موقع الصندوق
            $table->string('location', 200)->nullable()->after('description')->comment('موقع الصندوق');
            
            // Foreign Keys
            $table->foreign('chart_account_id')->references('id')->on('chart_accounts')->onDelete('set null');
            $table->foreign('responsible_user_id')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('chart_account_id');
            $table->index('responsible_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_boxes', function (Blueprint $table) {
            // حذف Foreign Keys
            $table->dropForeign(['chart_account_id']);
            $table->dropForeign(['responsible_user_id']);
            
            // حذف Indexes
            $table->dropIndex(['chart_account_id']);
            $table->dropIndex(['responsible_user_id']);
            
            // حذف الحقول
            $table->dropColumn([
                'chart_account_id',
                'responsible_user_id',
                'currencies',
                'max_balance',
                'min_balance',
                'location'
            ]);
        });
    }
};
