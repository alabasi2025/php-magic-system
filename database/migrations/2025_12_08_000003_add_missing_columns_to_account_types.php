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
        if (Schema::hasTable('account_types')) {
            Schema::table('account_types', function (Blueprint $table) {
                if (!Schema::hasColumn('account_types', 'name_ar')) {
                    $table->string('name_ar')->after('key')->comment('الاسم بالعربية');
                }
                if (!Schema::hasColumn('account_types', 'name_en')) {
                    $table->string('name_en')->nullable()->after('name_ar')->comment('الاسم بالإنجليزية');
                }
                if (!Schema::hasColumn('account_types', 'icon')) {
                    $table->string('icon')->nullable()->after('name_en')->comment('أيقونة Font Awesome');
                }
                if (!Schema::hasColumn('account_types', 'description')) {
                    $table->text('description')->nullable()->after('icon')->comment('وصف نوع الحساب');
                }
                if (!Schema::hasColumn('account_types', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('description')->comment('مفعل/معطل');
                }
                if (!Schema::hasColumn('account_types', 'is_system')) {
                    $table->boolean('is_system')->default(false)->after('is_active')->comment('نوع نظامي (لا يمكن حذفه)');
                }
                if (!Schema::hasColumn('account_types', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('is_system')->comment('ترتيب العرض');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('account_types')) {
            Schema::table('account_types', function (Blueprint $table) {
                $columns = ['name_ar', 'name_en', 'icon', 'description', 'is_active', 'is_system', 'sort_order'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('account_types', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
