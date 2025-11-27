<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Cashier; // افتراض أن النموذج موجود في هذا المسار

/**
 * @package CashiersGene
 * @subpackage Testing
 * @category Unit
 * 
 * اختبار وحدة لنموذج Cashier.
 * يهدف هذا الاختبار إلى التحقق من الخصائص الأساسية والعلاقات لنموذج الصراف (Cashier).
 */
class CashierModelTest extends TestCase
{
    use RefreshDatabase; // استخدام قاعدة بيانات مؤقتة ونظيفة لكل اختبار

    /**
     * @test
     * اختبار التحقق من وجود جدول قاعدة البيانات الصحيح.
     *
     * @return void
     */
    public function it_uses_the_correct_database_table()
    {
        $cashier = new Cashier();
        $this->assertEquals('cashiers', $cashier->getTable());
    }

    /**
     * @test
     * اختبار التحقق من الخصائص القابلة للتعبئة (fillable attributes).
     *
     * @return void
     */
    public function it_has_fillable_attributes()
    {
        $cashier = new Cashier();
        $expected = ['name', 'user_id', 'status'];
        $this->assertEquals($expected, $cashier->getFillable());
    }

    /**
     * @test
     * اختبار إنشاء نموذج Cashier بنجاح.
     *
     * @return void
     */
    public function it_can_be_created()
    {
        $cashier = Cashier::create([
            'name' => 'Cashier Test 1',
            'user_id' => 1, // افتراض وجود مستخدم
            'status' => 'active',
        ]);

        $this->assertNotNull($cashier);
        $this->assertEquals('Cashier Test 1', $cashier->name);
        $this->assertDatabaseHas('cashiers', ['name' => 'Cashier Test 1']);
    }

    // يمكن إضافة المزيد من الاختبارات هنا، مثل اختبار العلاقات (Relationships)
    // واختبار الوصول (Accessors) والمغيرات (Mutators) إذا كانت موجودة.
}
