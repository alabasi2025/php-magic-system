<?php

namespace Tests\Feature\Cashiers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group cashiers
 * @group feature
 * @group gene
 *
 * اختبارات الميزات الخاصة بجين الصرافين (Cashier Gene).
 * يفترض هذا الاختبار وجود مسارات (Routes) أساسية لإدارة الصرافين.
 */
class CashierGeneTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * إعداد البيئة قبل كل اختبار.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        // إنشاء مستخدم مسؤول (Admin) لتنفيذ الاختبارات التي تتطلب صلاحيات
        $this->actingAs(User::factory()->create(['is_admin' => true]));
    }

    /**
     * اختبار إمكانية الوصول إلى صفحة قائمة الصرافين.
     *
     * @return void
     */
    public function test_cashiers_index_page_can_be_rendered(): void
    {
        // افتراض وجود مسار 'cashiers.index'
        $response = $this->get(route('cashiers.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cashiers.index');
    }

    /**
     * اختبار إمكانية إنشاء صراف جديد (عرض نموذج الإنشاء).
     *
     * @return void
     */
    public function test_cashier_create_page_can_be_rendered(): void
    {
        // افتراض وجود مسار 'cashiers.create'
        $response = $this->get(route('cashiers.create'));

        $response->assertStatus(200);
        $response->assertViewIs('cashiers.create');
    }

    /**
     * اختبار عملية تخزين صراف جديد بنجاح.
     *
     * @return void
     */
    public function test_new_cashier_can_be_stored(): void
    {
        $cashierData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
            'is_active' => true,
        ];

        // افتراض وجود مسار 'cashiers.store'
        $response = $this->post(route('cashiers.store'), $cashierData);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('cashiers.index'));
        $this->assertDatabaseHas('users', [
            'email' => $cashierData['email'],
            'is_cashier' => true, // افتراض أن الصراف يتميز بعمود is_cashier
        ]);
    }

    /**
     * اختبار فشل تخزين صراف جديد ببيانات غير صالحة (مثل البريد الإلكتروني المكرر).
     *
     * @return void
     */
    public function test_cashier_store_fails_with_invalid_data(): void
    {
        // إنشاء صراف موجود مسبقًا
        User::factory()->create(['email' => 'existing@example.com']);

        $invalidData = [
            'name' => 'Test Cashier',
            'email' => 'existing@example.com', // بريد إلكتروني مكرر
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post(route('cashiers.store'), $invalidData);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseMissing('users', [
            'name' => 'Test Cashier',
        ]);
    }
}