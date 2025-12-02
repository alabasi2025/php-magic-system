<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migration لإدراج البيانات التجريبية لنظام العباسي
 * 
 * حسب المهمة 28 من ملف تعليمات المطور:
 * - 4 وحدات (العباسي، الحديدة، معبر، صنعاء)
 * - 3 محطات في الحديدة (الدهمية، الصبالية، غليل)
 * - 3 صناديق لكل محطة
 * - حسابات وسيطة لكل صندوق
 * 
 * ✅ يتعامل مع البيانات الموجودة مسبقاً (لا Duplicate Entry)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // التحقق من وجود البيانات - إذا كانت موجودة، لا نفعل شيء
        if (DB::table('holdings')->where('code', 'ALABBASI')->exists()) {
            echo "⚠️  البيانات التجريبية موجودة مسبقاً - تم التخطي\n";
            return;
        }

        // 1. إنشاء الشركة القابضة الرئيسية: مجموعة العباسي
        $alabbasi_holding_id = DB::table('holdings')->insertGetId([
            'code' => 'ALABBASI',
            'name' => 'مجموعة العباسي القابضة',
            'name_en' => 'Al-Abbasi Holding Group',
            'email' => 'info@alabbasi.com.sa',
            'phone' => '+966 11 234 5678',
            'fax' => '+966 11 234 5679',
            'website' => 'https://www.alabbasi.com.sa',
            'address' => 'طريق الملك فهد، حي العليا',
            'city' => 'الرياض',
            'country' => 'المملكة العربية السعودية',
            'postal_code' => '12211',
            'tax_number' => '300012345600003',
            'commercial_register' => '1010123456',
            'legal_form' => 'شركة مساهمة',
            'currency' => 'SAR',
            'fiscal_year_start' => '01-01',
            'is_active' => true,
            'notes' => 'الشركة القابضة الرئيسية لمجموعة العباسي - تأسست عام 2010',
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. إنشاء 4 وحدات رئيسية
        $units = [
            [
                'holding_id' => $alabbasi_holding_id,
                'parent_id' => null,
                'code' => 'ALABBASI-MAIN',
                'name' => 'وحدة العباسي الرئيسية',
                'name_en' => 'Al-Abbasi Main Unit',
                'type' => 'company',
                'email' => 'main@alabbasi.com.sa',
                'phone' => '+966 11 234 5678',
                'address' => 'طريق الملك فهد، حي العليا',
                'city' => 'الرياض',
                'country' => 'المملكة العربية السعودية',
                'tax_number' => '300012345600003',
                'commercial_register' => '1010123456',
                'start_date' => '2010-01-01',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holding_id' => $alabbasi_holding_id,
                'parent_id' => null,
                'code' => 'HODEIDAH',
                'name' => 'وحدة الحديدة',
                'name_en' => 'Hodeidah Unit',
                'type' => 'branch',
                'email' => 'hodeidah@alabbasi.com.sa',
                'phone' => '+967 3 234 567',
                'address' => 'شارع 26 سبتمبر',
                'city' => 'الحديدة',
                'country' => 'اليمن',
                'tax_number' => '400012345600001',
                'commercial_register' => '2020123456',
                'start_date' => '2015-01-01',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holding_id' => $alabbasi_holding_id,
                'parent_id' => null,
                'code' => 'MABAR',
                'name' => 'وحدة معبر',
                'name_en' => 'Mabar Unit',
                'type' => 'branch',
                'email' => 'mabar@alabbasi.com.sa',
                'phone' => '+967 4 234 567',
                'address' => 'شارع الستين',
                'city' => 'معبر',
                'country' => 'اليمن',
                'tax_number' => '400012345600002',
                'commercial_register' => '2020123457',
                'start_date' => '2016-01-01',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holding_id' => $alabbasi_holding_id,
                'parent_id' => null,
                'code' => 'SANAA',
                'name' => 'وحدة صنعاء',
                'name_en' => 'Sanaa Unit',
                'type' => 'branch',
                'email' => 'sanaa@alabbasi.com.sa',
                'phone' => '+967 1 234 567',
                'address' => 'شارع الزبيري',
                'city' => 'صنعاء',
                'country' => 'اليمن',
                'tax_number' => '400012345600003',
                'commercial_register' => '2020123458',
                'start_date' => '2017-01-01',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $unit_ids = [];
        foreach ($units as $unit) {
            $unit_ids[] = DB::table('units')->insertGetId($unit);
        }

        // 3. إنشاء 3 محطات في الحديدة (تابعة لوحدة الحديدة)
        $hodeidah_unit_id = $unit_ids[1]; // وحدة الحديدة
        
        $stations = [
            [
                'holding_id' => $alabbasi_holding_id,
                'parent_id' => $hodeidah_unit_id,
                'code' => 'DAHMIYA',
                'name' => 'محطة الدهمية',
                'name_en' => 'Al-Dahmiya Station',
                'type' => 'division',
                'email' => 'dahmiya@alabbasi.com.sa',
                'phone' => '+967 3 234 570',
                'address' => 'منطقة الدهمية',
                'city' => 'الحديدة',
                'country' => 'اليمن',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holding_id' => $alabbasi_holding_id,
                'parent_id' => $hodeidah_unit_id,
                'code' => 'SABALIYA',
                'name' => 'محطة الصبالية',
                'name_en' => 'Al-Sabaliya Station',
                'type' => 'division',
                'email' => 'sabaliya@alabbasi.com.sa',
                'phone' => '+967 3 234 571',
                'address' => 'منطقة الصبالية',
                'city' => 'الحديدة',
                'country' => 'اليمن',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'holding_id' => $alabbasi_holding_id,
                'parent_id' => $hodeidah_unit_id,
                'code' => 'GHALIL',
                'name' => 'محطة غليل',
                'name_en' => 'Ghalil Station',
                'type' => 'division',
                'email' => 'ghalil@alabbasi.com.sa',
                'phone' => '+967 3 234 572',
                'address' => 'منطقة غليل',
                'city' => 'الحديدة',
                'country' => 'اليمن',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $station_ids = [];
        foreach ($stations as $station) {
            $station_ids[] = DB::table('units')->insertGetId($station);
        }

        // 4. إنشاء أقسام لكل محطة
        $departments = [];
        $department_types = [
            ['name' => 'قسم المبيعات', 'name_en' => 'Sales Department', 'type' => 'sales'],
            ['name' => 'قسم المحاسبة', 'name_en' => 'Accounting Department', 'type' => 'accounting'],
            ['name' => 'قسم المخزون', 'name_en' => 'Inventory Department', 'type' => 'operations'],
        ];
        
        foreach ($station_ids as $index => $station_id) {
            $station_code = $stations[$index]['code'];
            
            for ($i = 0; $i < 3; $i++) {
                $dept_id = DB::table('departments')->insertGetId([
                    'unit_id' => $station_id,
                    'parent_id' => null,
                    'code' => $station_code . '-DEPT-' . ($i + 1),
                    'name' => $department_types[$i]['name'],
                    'name_en' => $department_types[$i]['name_en'],
                    'type' => $department_types[$i]['type'],
                    'email' => strtolower($station_code) . '-dept' . ($i + 1) . '@alabbasi.com.sa',
                    'phone' => '+967 3 234 ' . (580 + $index * 3 + $i),
                    'is_active' => true,
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $departments[] = $dept_id;
            }
        }

        // 5. إنشاء 3 صناديق لكل محطة
        $cash_boxes = [];
        foreach ($station_ids as $index => $station_id) {
            $station_code = $stations[$index]['code'];
            
            for ($i = 0; $i < 3; $i++) {
                $cash_box_id = DB::table('alabasi_cash_boxes')->insertGetId([
                    'code' => $station_code . '-CASH-' . ($i + 1),
                    'name' => 'صندوق ' . $stations[$index]['name'] . ' - ' . ($i + 1),
                    'balance' => 100000.00, // 100,000 ريال يمني
                    'is_active' => true,
                    'description' => 'صندوق نقدي رقم ' . ($i + 1) . ' في ' . $stations[$index]['name'],
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $cash_boxes[] = [
                    'id' => $cash_box_id,
                    'station_id' => $station_id,
                    'code' => $station_code . '-CASH-' . ($i + 1),
                ];
            }
        }

        // 6. إنشاء حساب وسيط لكل صندوق
        $intermediate_accounts = [];
        foreach ($cash_boxes as $index => $cash_box) {
            $account_id = DB::table('alabasi_intermediate_accounts')->insertGetId([
                'name' => 'حساب وسيط - ' . $cash_box['code'],
                'code' => 'INT-' . $cash_box['code'],
                'main_account_id' => null,
                'is_active' => true,
                'description' => 'حساب وسيط للصندوق ' . $cash_box['code'],
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $intermediate_accounts[] = [
                'id' => $account_id,
                'cash_box_id' => $cash_box['id'],
                'code' => 'INT-' . $cash_box['code'],
            ];
        }

        // 7. إنشاء عمليات تجريبية لكل حساب وسيط
        foreach ($intermediate_accounts as $index => $account) {
            // عملية إيداع
            DB::table('alabasi_intermediate_transactions')->insert([
                'intermediate_account_id' => $account['id'],
                'type' => 'receipt',
                'amount' => 65000.00,
                'description' => 'إيداع تجريبي - ' . $account['code'],
                'transaction_date' => now()->subDays(rand(1, 30)),
                'reference_number' => 'DEP-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // عملية سحب
            DB::table('alabasi_intermediate_transactions')->insert([
                'intermediate_account_id' => $account['id'],
                'type' => 'payment',
                'amount' => 30000.00,
                'description' => 'سحب تجريبي - ' . $account['code'],
                'transaction_date' => now()->subDays(rand(1, 15)),
                'reference_number' => 'WITH-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "✅ تم إدراج البيانات التجريبية بنجاح!\n";
        echo "   - 1 شركة قابضة (مجموعة العباسي)\n";
        echo "   - 4 وحدات رئيسية (العباسي، الحديدة، معبر، صنعاء)\n";
        echo "   - 3 محطات في الحديدة (الدهمية، الصبالية، غليل)\n";
        echo "   - 9 أقسام (3 لكل محطة)\n";
        echo "   - 9 صناديق (3 لكل محطة)\n";
        echo "   - 9 حسابات وسيطة (حساب لكل صندوق)\n";
        echo "   - 18 عملية تجريبية (إيداع + سحب لكل حساب)\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف البيانات التجريبية بالترتيب العكسي
        DB::table('alabasi_intermediate_transactions')->where('reference_number', 'like', 'DEP-%')->orWhere('reference_number', 'like', 'WITH-%')->delete();
        DB::table('alabasi_intermediate_accounts')->where('code', 'like', 'INT-%')->delete();
        DB::table('alabasi_cash_boxes')->where('code', 'like', '%-CASH-%')->delete();
        DB::table('departments')->where('code', 'like', '%-DEPT-%')->delete();
        DB::table('units')->where('code', 'in', ['DAHMIYA', 'SABALIYA', 'GHALIL', 'ALABBASI-MAIN', 'HODEIDAH', 'MABAR', 'SANAA'])->delete();
        DB::table('holdings')->where('code', 'ALABBASI')->delete();

        echo "✅ تم حذف البيانات التجريبية بنجاح!\n";
    }
};
