<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

/**
 * DepartmentSeeder - بيانات تجريبية للأقسام
 * 
 * ينشئ 18 قسم تابع للوحدات المختلفة
 */
class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->delete();
        
        $units = Unit::all();
        
        if ($units->count() < 10) {
            $this->command->error('⚠️ يجب تشغيل UnitSeeder أولاً');
            return;
        }

        $departments = [
            // أقسام شركة العباسي العقارية
            [
                'unit_id' => 1,
                'parent_id' => null,
                'code' => 'DEPT-001',
                'name' => 'قسم التطوير العقاري',
                'name_en' => 'Real Estate Development Department',
                'type' => 'operational',
                'email' => 'development@alabasi-re.com',
                'phone' => '+966112345684',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم مسؤول عن تطوير المشاريع العقارية الجديدة',
                'created_by' => 1,
            ],
            [
                'unit_id' => 1,
                'parent_id' => null,
                'code' => 'DEPT-002',
                'name' => 'قسم المبيعات والتسويق',
                'name_en' => 'Sales & Marketing Department',
                'type' => 'sales',
                'email' => 'sales@alabasi-re.com',
                'phone' => '+966112345685',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم مسؤول عن تسويق وبيع المشاريع العقارية',
                'created_by' => 1,
            ],
            [
                'unit_id' => 1,
                'parent_id' => null,
                'code' => 'DEPT-003',
                'name' => 'قسم إدارة الممتلكات',
                'name_en' => 'Property Management Department',
                'type' => 'operational',
                'email' => 'property@alabasi-re.com',
                'phone' => '+966112345686',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم مسؤول عن إدارة وصيانة الممتلكات العقارية',
                'created_by' => 1,
            ],
            
            // أقسام شركة العباسي للتجارة
            [
                'unit_id' => 2,
                'parent_id' => null,
                'code' => 'DEPT-004',
                'name' => 'قسم المشتريات',
                'name_en' => 'Procurement Department',
                'type' => 'support',
                'email' => 'procurement@alabasi-trading.com',
                'phone' => '+966112345687',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم مسؤول عن المشتريات والتوريد',
                'created_by' => 1,
            ],
            [
                'unit_id' => 2,
                'parent_id' => null,
                'code' => 'DEPT-005',
                'name' => 'قسم المبيعات',
                'name_en' => 'Sales Department',
                'type' => 'sales',
                'email' => 'sales@alabasi-trading.com',
                'phone' => '+966112345688',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم مسؤول عن المبيعات والتوزيع',
                'created_by' => 1,
            ],
            [
                'unit_id' => 2,
                'parent_id' => null,
                'code' => 'DEPT-006',
                'name' => 'قسم المستودعات',
                'name_en' => 'Warehousing Department',
                'type' => 'operational',
                'email' => 'warehouse@alabasi-trading.com',
                'phone' => '+966112345689',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم مسؤول عن إدارة المستودعات والمخزون',
                'created_by' => 1,
            ],
            
            // أقسام شركة النور للاستشارات
            [
                'unit_id' => 5,
                'parent_id' => null,
                'code' => 'DEPT-007',
                'name' => 'قسم الاستشارات الاستثمارية',
                'name_en' => 'Investment Consulting Department',
                'type' => 'consulting',
                'email' => 'investment@alnoor-consulting.com',
                'phone' => '+966126543233',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم متخصص في الاستشارات الاستثمارية',
                'created_by' => 1,
            ],
            [
                'unit_id' => 5,
                'parent_id' => null,
                'code' => 'DEPT-008',
                'name' => 'قسم التخطيط المالي',
                'name_en' => 'Financial Planning Department',
                'type' => 'consulting',
                'email' => 'planning@alnoor-consulting.com',
                'phone' => '+966126543234',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم متخصص في التخطيط المالي الاستراتيجي',
                'created_by' => 1,
            ],
            [
                'unit_id' => 5,
                'parent_id' => null,
                'code' => 'DEPT-009',
                'name' => 'قسم دراسات الجدوى',
                'name_en' => 'Feasibility Studies Department',
                'type' => 'research',
                'email' => 'feasibility@alnoor-consulting.com',
                'phone' => '+966126543235',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم متخصص في إعداد دراسات الجدوى الاقتصادية',
                'created_by' => 1,
            ],
            
            // أقسام شركة النور لإدارة الأصول
            [
                'unit_id' => 6,
                'parent_id' => null,
                'code' => 'DEPT-010',
                'name' => 'قسم إدارة المحافظ',
                'name_en' => 'Portfolio Management Department',
                'type' => 'operational',
                'email' => 'portfolio@alnoor-assets.com',
                'phone' => '+966126543242',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم مسؤول عن إدارة المحافظ الاستثمارية',
                'created_by' => 1,
            ],
            [
                'unit_id' => 6,
                'parent_id' => null,
                'code' => 'DEPT-011',
                'name' => 'قسم التحليل المالي',
                'name_en' => 'Financial Analysis Department',
                'type' => 'research',
                'email' => 'analysis@alnoor-assets.com',
                'phone' => '+966126543243',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم متخصص في التحليل المالي وتقييم الأصول',
                'created_by' => 1,
            ],
            
            // أقسام مصنع الفجر للبلاستيك
            [
                'unit_id' => 8,
                'parent_id' => null,
                'code' => 'DEPT-012',
                'name' => 'قسم الإنتاج',
                'name_en' => 'Production Department',
                'type' => 'production',
                'email' => 'production@alfajr-plastic.com',
                'phone' => '+966133456804',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم مسؤول عن عمليات الإنتاج',
                'created_by' => 1,
            ],
            [
                'unit_id' => 8,
                'parent_id' => null,
                'code' => 'DEPT-013',
                'name' => 'قسم مراقبة الجودة',
                'name_en' => 'Quality Control Department',
                'type' => 'quality',
                'email' => 'qc@alfajr-plastic.com',
                'phone' => '+966133456805',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم مسؤول عن مراقبة جودة المنتجات',
                'created_by' => 1,
            ],
            [
                'unit_id' => 8,
                'parent_id' => null,
                'code' => 'DEPT-014',
                'name' => 'قسم الصيانة',
                'name_en' => 'Maintenance Department',
                'type' => 'support',
                'email' => 'maintenance@alfajr-plastic.com',
                'phone' => '+966133456806',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم مسؤول عن صيانة المعدات والآلات',
                'created_by' => 1,
            ],
            
            // أقسام مصنع الفجر للكيماويات
            [
                'unit_id' => 9,
                'parent_id' => null,
                'code' => 'DEPT-015',
                'name' => 'قسم الإنتاج الكيماوي',
                'name_en' => 'Chemical Production Department',
                'type' => 'production',
                'email' => 'production@alfajr-chemical.com',
                'phone' => '+966133456812',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم مسؤول عن إنتاج المواد الكيماوية',
                'created_by' => 1,
            ],
            [
                'unit_id' => 9,
                'parent_id' => null,
                'code' => 'DEPT-016',
                'name' => 'قسم السلامة والبيئة',
                'name_en' => 'Safety & Environment Department',
                'type' => 'safety',
                'email' => 'safety@alfajr-chemical.com',
                'phone' => '+966133456813',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم مسؤول عن السلامة المهنية والبيئية',
                'created_by' => 1,
            ],
            [
                'unit_id' => 9,
                'parent_id' => null,
                'code' => 'DEPT-017',
                'name' => 'قسم المختبرات',
                'name_en' => 'Laboratory Department',
                'type' => 'research',
                'email' => 'lab@alfajr-chemical.com',
                'phone' => '+966133456814',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم مسؤول عن الاختبارات والتحاليل الكيماوية',
                'created_by' => 1,
            ],
            
            // أقسام مركز الأبحاث
            [
                'unit_id' => 12,
                'parent_id' => null,
                'code' => 'DEPT-018',
                'name' => 'قسم الأبحاث التطبيقية',
                'name_en' => 'Applied Research Department',
                'type' => 'research',
                'email' => 'research@alfajr-rd.com',
                'phone' => '+966133456822',
                'manager_id' => null,
                'is_active' => true,
                'notes' => 'قسم متخصص في الأبحاث التطبيقية والتطوير',
                'created_by' => 1,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }

        $this->command->info('✅ تم إنشاء 18 قسم بنجاح');
    }
}
