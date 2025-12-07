<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chart_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('chart_accounts', 'account_group_id')) {
                $table->unsignedBigInteger('account_group_id')->nullable()->after('id');
                $table->foreign('account_group_id')->references('id')->on('account_groups')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chart_accounts', function (Blueprint $table) {
            $table->dropForeign(['account_group_id']);
            $table->dropColumn('account_group_id');
        });
    }
};
