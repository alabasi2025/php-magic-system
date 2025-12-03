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
        Schema::table('chart_accounts', function (Blueprint $table) {
            // فحص وإضافة الأعمدة فقط إذا لم تكن موجودة
            if (!Schema::hasColumn('chart_accounts', 'is_parent')) {
                $table->boolean('is_parent')->default(false)->after('parent_id')->comment('true = حساب رئيسي للترتيب، false = حساب فرعي نهائي');
            }
            
            // تعديل نوع العمود account_type إذا كان موجوداً
            if (Schema::hasColumn('chart_accounts', 'account_type')) {
                // حذف العمود القديم وإعادة إنشائه بالقيم الجديدة
                $table->dropColumn('account_type');
            }
            
            $table->enum('account_type', [
                'general',           // عام (افتراضي)
                'cash_box',          // صندوق
                'bank',              // بنك
                'wallet',            // محفظة
                'atm',               // صراف
                'intermediate',      // حساب وسيط
                'customer',          // عميل
                'supplier',          // مورد
                'employee',          // موظف
                'expense',           // مصروف
                'revenue',           // إيراد
                'asset',             // أصل
                'liability',         // التزام
                'equity'             // حقوق ملكية
            ])->default('general')->after('is_parent')->comment('نوع الحساب');
            
            if (!Schema::hasColumn('chart_accounts', 'intermediate_for')) {
                $table->enum('intermediate_for', [
                    'cash_boxes',        // للصناديق
                    'banks',             // للبنوك
                    'wallets',           // للمحافظ
                    'atms',              // للصرافات
                    'customers',         // للعملاء
                    'suppliers',         // للموردين
                    'employees'          // للموظفين
                ])->nullable()->after('account_type')->comment('إذا كان حساب وسيط، لأي فئة؟');
            }
            
            if (!Schema::hasColumn('chart_accounts', 'is_linked')) {
                $table->boolean('is_linked')->default(false)->after('intermediate_for')->comment('هل تم ربط هذا الحساب الوسيط بكيان؟');
            }
            
            if (!Schema::hasColumn('chart_accounts', 'linked_entity_id')) {
                $table->unsignedBigInteger('linked_entity_id')->nullable()->after('is_linked')->comment('معرف الكيان المرتبط');
            }
            
            if (!Schema::hasColumn('chart_accounts', 'linked_entity_type')) {
                $table->string('linked_entity_type')->nullable()->after('linked_entity_id')->comment('نوع الكيان المرتبط (CashBox, Bank, etc.)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'is_parent',
                'account_type',
                'intermediate_for',
                'is_linked',
                'linked_entity_id',
                'linked_entity_type'
            ]);
        });
    }
};
