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
            // هل الحساب رئيسي (للترتيب الشجري) أم فرعي (نهائي)
            $table->boolean('is_parent')->default(false)->after('parent_id')->comment('true = حساب رئيسي للترتيب، false = حساب فرعي نهائي');
            
            // نوع الحساب (صندوق، بنك، محفظة، صراف، حساب وسيط، عام)
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
            
            // إذا كان حساب وسيط، لأي فئة؟
            $table->enum('intermediate_for', [
                'cash_boxes',        // للصناديق
                'banks',             // للبنوك
                'wallets',           // للمحافظ
                'atms',              // للصرافات
                'customers',         // للعملاء
                'suppliers',         // للموردين
                'employees'          // للموظفين
            ])->nullable()->after('account_type')->comment('إذا كان حساب وسيط، لأي فئة؟');
            
            // هل تم ربط هذا الحساب الوسيط؟
            $table->boolean('is_linked')->default(false)->after('intermediate_for')->comment('هل تم ربط هذا الحساب الوسيط بكيان؟');
            
            // معرف الكيان المرتبط (صندوق، بنك، إلخ)
            $table->unsignedBigInteger('linked_entity_id')->nullable()->after('is_linked')->comment('معرف الكيان المرتبط');
            
            // نوع الكيان المرتبط
            $table->string('linked_entity_type')->nullable()->after('linked_entity_id')->comment('نوع الكيان المرتبط (CashBox, Bank, etc.)');
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
