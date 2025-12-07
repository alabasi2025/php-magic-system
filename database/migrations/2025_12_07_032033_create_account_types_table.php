<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_types', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('المفتاح الفريد للنوع (مثل: cash_box, bank)');
            $table->string('name_ar')->comment('الاسم بالعربي');
            $table->string('name_en')->nullable()->comment('الاسم بالإنجليزي');
            $table->string('icon')->nullable()->comment('أيقونة النوع');
            $table->text('description')->nullable()->comment('وصف النوع');
            $table->boolean('is_active')->default(true)->comment('هل النوع مفعل؟');
            $table->boolean('is_system')->default(false)->comment('هل هو نوع نظام (لا يمكن حذفه)');
            $table->integer('sort_order')->default(0)->comment('ترتيب العرض');
            $table->timestamps();
        });

        // إضافة البيانات الافتراضية
        DB::table('account_types')->insert([
            [
                'key' => 'general',
                'name_ar' => 'حساب عام',
                'name_en' => 'General Account',
                'icon' => 'fas fa-file-invoice',
                'description' => 'حساب عام للاستخدامات المتنوعة',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'cash_box',
                'name_ar' => 'صندوق',
                'name_en' => 'Cash Box',
                'icon' => 'fas fa-cash-register',
                'description' => 'حساب خاص بالصناديق النقدية',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bank',
                'name_ar' => 'بنك',
                'name_en' => 'Bank',
                'icon' => 'fas fa-university',
                'description' => 'حساب خاص بالحسابات البنكية',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'wallet',
                'name_ar' => 'محفظة إلكترونية',
                'name_en' => 'E-Wallet',
                'icon' => 'fas fa-wallet',
                'description' => 'حساب خاص بالمحافظ الإلكترونية',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'atm',
                'name_ar' => 'صراف آلي',
                'name_en' => 'ATM',
                'icon' => 'fas fa-credit-card',
                'description' => 'حساب خاص بالصرافات الآلية',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'intermediate',
                'name_ar' => 'حساب وسيط',
                'name_en' => 'Intermediate Account',
                'icon' => 'fas fa-link',
                'description' => 'حساب وسيط يربط بين الكيانات والحسابات',
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 6,
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
        Schema::dropIfExists('account_types');
    }
};
