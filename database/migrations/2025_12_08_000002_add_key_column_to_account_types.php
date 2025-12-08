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
        if (Schema::hasTable('account_types') && !Schema::hasColumn('account_types', 'key')) {
            Schema::table('account_types', function (Blueprint $table) {
                $table->string('key')->unique()->after('id')->comment('مفتاح فريد لنوع الحساب');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('account_types', 'key')) {
            Schema::table('account_types', function (Blueprint $table) {
                $table->dropColumn('key');
            });
        }
    }
};
