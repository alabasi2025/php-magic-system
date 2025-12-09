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
        Schema::table('items', function (Blueprint $table) {
            // إضافة عمود unit_price إذا لم يكن موجوداً
            if (!Schema::hasColumn('items', 'unit_price')) {
                $table->decimal('unit_price', 15, 2)->nullable()->after('unit_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'unit_price')) {
                $table->dropColumn('unit_price');
            }
        });
    }
};
