<?php

namespace Tests\Feature\Genes\Cashiers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User; // افتراض وجود نموذج المستخدم

/**
 * @group Cashiers
 * @group Feature
 * @group 2077
 * 
 * اختبارات الميزات لنظام الصرافين (Cashiers Gene).
 * يفترض هذا الاختبار وجود مسارات (Routes) ووحدات تحكم (Controllers)
 * للتعامل مع عمليات الصرافين الأساسية.
 */
class CashierFeatureTest extends TestCase
{
    use RefreshDatabase; // استخدام قاعدة بيانات مؤقتة ونظيفة لكل اختبار

    /**
     * اختبار الوصول إلى صفحة قائمة الصرافين للمستخدم المصرح له.
     * 
     * @return void
     */
    public function test_authorized_user_can_access_cashiers_list()
    {
        // 1. تهيئة: إنشاء مستخدم بصلاحية "cashier_manager" أو "admin"
        $user = User::factory()->create();
        // افتراض وجود طريقة لإضافة صلاحية للمستخدم
        // $user->assignRole('cashier_manager'); 

        // 2. الفعل: تسجيل الدخول كمستخدم وإرسال طلب GET
        $response = $this->actingAs($user)->get('/cashiers');

        // 3. التأكيد: يجب أن يكون الرد ناجحًا (200)
        $response->assertStatus(200);
        $response->assertViewIs('cashiers.index'); // افتراض اسم العرض
    }

    /**
     * اختبار منع المستخدم غير المصرح له من الوصول إلى صفحة قائمة الصرافين.
     * 
     * @return void
     */
    public function test_unauthorized_user_cannot_access_cashiers_list()
    {
        // 1. تهيئة: إنشاء مستخدم عادي بدون صلاحيات
        $user = User::factory()->create();

        // 2. الفعل: تسجيل الدخول كمستخدم وإرسال طلب GET
        $response = $this->actingAs($user)->get('/cashiers');

        // 3. التأكيد: يجب أن يتم منعه (403 Forbidden) أو إعادة توجيهه (302)
        // سنفترض إعادة التوجيه إلى صفحة تسجيل الدخول أو صفحة الخطأ
        $response->assertStatus(403); // افتراض استخدام سياسة (Policy) أو وسيط (Middleware) للمنع
    }

    /**
     * اختبار إنشاء صراف جديد بنجاح.
     * 
     * @return void
     */
    public function test_cashier_can_be_created_successfully()
    {
        $this->withoutExceptionHandling(); // لإظهار الأخطاء بوضوح أثناء التطوير

        // 1. تهيئة: إنشاء مستخدم مصرح له
        $manager = User::factory()->create();
        // $manager->assignRole('cashier_manager'); 

        // بيانات الصراف الجديد
        $cashierData = [
            'name' => 'New Cashier',
            'email' => 'new.cashier@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            // قد تحتاج إلى حقول إضافية مثل 'branch_id'
        ];

        // 2. الفعل: إرسال طلب POST لإنشاء صراف
        $response = $this->actingAs($manager)->post('/cashiers', $cashierData);

        // 3. التأكيد: يجب أن يتم إعادة التوجيه بنجاح (302)
        $response->assertRedirect('/cashiers'); 
        $response->assertSessionHas('success', 'Cashier created successfully.'); // افتراض رسالة نجاح

        // التأكد من وجود الصراف في قاعدة البيانات
        $this->assertDatabaseHas('users', [
            'email' => 'new.cashier@example.com',
            'name' => 'New Cashier',
        ]);
    }
}