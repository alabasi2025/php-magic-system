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
        Schema::create('chart_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('كود النوع بالإنجليزية');
            $table->string('name')->comment('اسم النوع بالعربية');
            $table->string('name_en')->nullable()->comment('اسم النوع بالإنجليزية');
            $table->text('description')->nullable()->comment('وصف النوع');
            $table->string('icon', 50)->nullable()->comment('أيقونة Font Awesome');
            $table->string('color', 20)->default('indigo')->comment('اللون الأساسي');
            $table->boolean('is_active')->default(true)->comment('حالة التفعيل');
            $table->integer('sort_order')->default(0)->comment('ترتيب العرض');
            $table->timestamps();
        });

        // إدراج البيانات الأولية
        DB::table('chart_types')->insert([
            [
                'code' => 'employees',
                'name' => 'أعمال الموظفين',
                'name_en' => 'Employees',
                'description' => 'دليل محاسبي لإدارة حسابات الموظفين والرواتب',
                'icon' => 'fa-users',
                'color' => 'blue',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'final_accounts',
                'name' => 'الحسابات النهائية',
                'name_en' => 'Final Accounts',
                'description' => 'دليل الحسابات الختامية والميزانية',
                'icon' => 'fa-file-invoice-dollar',
                'color' => 'green',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'budget',
                'name' => 'الميزانية',
                'name_en' => 'Budget',
                'description' => 'دليل محاسبي للميزانية العمومية',
                'icon' => 'fa-balance-scale',
                'color' => 'purple',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'projects',
                'name' => 'المشاريع',
                'name_en' => 'Projects',
                'description' => 'دليل محاسبي لإدارة المشاريع',
                'icon' => 'fa-project-diagram',
                'color' => 'indigo',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'assets',
                'name' => 'الأصول',
                'name_en' => 'Assets',
                'description' => 'دليل محاسبي للأصول الثابتة والمتداولة',
                'icon' => 'fa-building',
                'color' => 'yellow',
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'inventory',
                'name' => 'المخزون',
                'name_en' => 'Inventory',
                'description' => 'دليل محاسبي لإدارة المخزون',
                'icon' => 'fa-boxes',
                'color' => 'orange',
                'is_active' => true,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'sales',
                'name' => 'المبيعات',
                'name_en' => 'Sales',
                'description' => 'دليل محاسبي للمبيعات والعملاء',
                'icon' => 'fa-shopping-cart',
                'color' => 'green',
                'is_active' => true,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'purchases',
                'name' => 'المشتريات',
                'name_en' => 'Purchases',
                'description' => 'دليل محاسبي للمشتريات والموردين',
                'icon' => 'fa-truck',
                'color' => 'red',
                'is_active' => true,
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'expenses',
                'name' => 'المصروفات',
                'name_en' => 'Expenses',
                'description' => 'دليل محاسبي للمصروفات',
                'icon' => 'fa-money-bill-wave',
                'color' => 'red',
                'is_active' => true,
                'sort_order' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'revenues',
                'name' => 'الإيرادات',
                'name_en' => 'Revenues',
                'description' => 'دليل محاسبي للإيرادات',
                'icon' => 'fa-coins',
                'color' => 'green',
                'is_active' => true,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'other',
                'name' => 'أخرى',
                'name_en' => 'Other',
                'description' => 'أنواع أخرى من الأدلة المحاسبية',
                'icon' => 'fa-ellipsis-h',
                'color' => 'gray',
                'is_active' => true,
                'sort_order' => 99,
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
        Schema::dropIfExists('chart_types');
    }
};
