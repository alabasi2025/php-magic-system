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
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
                'phone' => '+966 11 234 5680',
                'address' => 'المقر الرئيسي، الرياض',
                'city' => 'الرياض',
                'country' => 'السعودية',
                'tax_number' => '300012345600004',
                'commercial_register' => '1010123457',
                'start_date' => '2010-01-01',
                'is_active' => true,
                'created_by' => 1,
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
                'address' => 'شارع الكورنيش، الحديدة',
                'city' => 'الحديدة',
                'country' => 'اليمن',
                'start_date' => '2015-06-01',
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'holding_id' => $alabbasi_holding_id,
                'parent_id' => null,
                'code' => 'MAABAR',
                'name' => 'وحدة معبر',
                'name_en' => 'Maabar Unit',
                'type' => 'branch',
                'email' => 'maabar@alabbasi.com.sa',
                'phone' => '+967 4 345 678',
                'address' => 'شارع الحرية، معبر',
                'city' => 'معبر',
                'country' => 'اليمن',
                'start_date' => '2016-03-01',
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'holding_id' => $alabbasi_holding_id,
                'parent_id' => null,
                'code' => 'SANAA',
                'name' => 'وحدة صنعاء',
                'name_en' => 'Sanaa Unit',
                'type' => 'branch',
                'email' => 'sanaa@alabbasi.com.sa',
                'phone' => '+967 1 456 789',
                'address' => 'شارع الزبيري، صنعاء',
                'city' => 'صنعاء',
                'country' => 'اليمن',
                'start_date' => '2017-01-01',
                'is_active' => true,
                'created_by' => 1,
            ],
        ];

        $unit_ids = [];
        foreach ($units as $unit) {
            $unit['created_at'] = now();
            $unit['updated_at'] = now();
            $unit_ids[] = DB::table('units')->insertGetId($unit);
        }

        // الوحدة الثانية هي الحديدة
        $hodeidah_unit_id = $unit_ids[1];

        // 3. إنشاء 3 محطات في الحديدة (كوحدات فرعية)
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
                'address' => 'منطقة الدهمية، الحديدة',
                'city' => 'الحديدة',
                'country' => 'اليمن',
                'start_date' => '2015-06-01',
                'is_active' => true,
                'created_by' => 1,
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
                'address' => 'منطقة الصبالية، الحديدة',
                'city' => 'الحديدة',
                'country' => 'اليمن',
                'start_date' => '2015-06-01',
                'is_active' => true,
                'created_by' => 1,
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
                'address' => 'منطقة غليل، الحديدة',
                'city' => 'الحديدة',
                'country' => 'اليمن',
                'start_date' => '2015-06-01',
                'is_active' => true,
                'created_by' => 1,
            ],
        ];

        $station_ids = [];
        foreach ($stations as $station) {
            $station['created_at'] = now();
            $station['updated_at'] = now();
            $station_ids[] = DB::table('units')->insertGetId($station);
        }

        // 4. إنشاء أقسام لكل محطة
        $departments = [];
        $department_types = [
            ['name' => 'قسم المبيعات', 'name_en' => 'Sales Department', 'type' => 'sales'],
            ['name' => 'قسم المحاسبة', 'name_en' => 'Accounting Department', 'type' => 'support'],
            ['name' => 'قسم المخزون', 'name_en' => 'Inventory Department', 'type' => 'operational'],
        ];
        
        foreach ($station_ids as $index => $station_id) {
            $station_code = $stations[$index]['code'];
            for ($i = 0; $i < 3; $i++) {
                $departments[] = [
                    'unit_id' => $station_id,
                    'parent_id' => null,
                    'code' => $station_code . '-DEPT-' . ($i + 1),
                    'name' => $department_types[$i]['name'],
                    'name_en' => $department_types[$i]['name_en'],
                    'type' => $department_types[$i]['type'],
                    'email' => strtolower($station_code) . '-dept' . ($i + 1) . '@alabbasi.com.sa',
                    'phone' => '+967 3 234 ' . (580 + ($index * 10) + $i),
                    'is_active' => true,
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach ($departments as $dept) {
            DB::table('departments')->insert($dept);
        }

        // 5. إنشاء 3 صناديق لكل محطة (9 صناديق إجمالاً)
        $cash_boxes = [];
        
        foreach ($station_ids as $index => $station_id) {
            $station_code = $stations[$index]['code'];
            $station_name = $stations[$index]['name'];
            
            for ($i = 1; $i <= 3; $i++) {
                // إنشاء حساب وسيط أولاً
                $intermediate_account_id = DB::table('alabasi_intermediate_accounts')->insertGetId([
                    'name' => "حساب وسيط - {$station_name} - صندوق {$i}",
                    'code' => "{$station_code}-IA-{$i}",
                    'main_account_id' => null,
                    'is_active' => true,
                    'description' => "حساب وسيط لصندوق {$i} في {$station_name}",
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // إنشاء الصندوق
                $cash_boxes[] = [
                    'code' => "{$station_code}-CB-{$i}",
                    'name' => "صندوق {$i} - {$station_name}",
                    'balance' => 100000.00,
                    'is_active' => true,
                    'description' => "صندوق رقم {$i} في محطة {$station_name} - مرتبط بالحساب الوسيط {$station_code}-IA-{$i}",
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach ($cash_boxes as $box) {
            DB::table('alabasi_cash_boxes')->insert($box);
        }

        // 6. إنشاء بعض العمليات التجريبية على الحسابات الوسيطة
        $transactions = [];
        $intermediate_account_ids = DB::table('alabasi_intermediate_accounts')
            ->where('code', 'like', '%-IA-%')
            ->pluck('id')
            ->toArray();

        foreach ($intermediate_account_ids as $index => $account_id) {
            // عملية إيداع
            $transactions[] = [
                'intermediate_account_id' => $account_id,
                'type' => 'receipt',
                'amount' => 50000.00 + ($index * 10000),
                'status' => 'completed',
                'description' => 'إيداع افتتاحي',
                'reference_number' => 'REC-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'transaction_date' => now()->subDays(rand(1, 30)),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // عملية سحب
            $transactions[] = [
                'intermediate_account_id' => $account_id,
                'type' => 'payment',
                'amount' => 20000.00 + ($index * 5000),
                'status' => 'completed',
                'description' => 'مصروفات تشغيلية',
                'reference_number' => 'PAY-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'transaction_date' => now()->subDays(rand(1, 15)),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach ($transactions as $transaction) {
            DB::table('alabasi_intermediate_transactions')->insert($transaction);
        }

        echo "\n✅ تم إدراج البيانات التجريبية لنظام العباسي بنجاح:\n";
        echo "   - 1 شركة قابضة (مجموعة العباسي)\n";
        echo "   - 4 وحدات رئيسية (العباسي، الحديدة، معبر، صنعاء)\n";
        echo "   - 3 محطات في الحديدة (الدهمية، الصبالية، غليل)\n";
        echo "   - 9 أقسام (3 لكل محطة)\n";
        echo "   - 9 صناديق (3 لكل محطة)\n";
        echo "   - 9 حسابات وسيطة (حساب لكل صندوق)\n";
        echo "   - 18 عملية تجريبية (إيداع + سحب لكل حساب)\n\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف البيانات بالترتيب العكسي
        DB::table('alabasi_intermediate_transactions')->where('reference_number', 'like', 'REC-%')->delete();
        DB::table('alabasi_intermediate_transactions')->where('reference_number', 'like', 'PAY-%')->delete();
        DB::table('alabasi_cash_boxes')->where('code', 'like', '%-CB-%')->delete();
        DB::table('alabasi_intermediate_accounts')->where('code', 'like', '%-IA-%')->delete();
        DB::table('departments')->where('code', 'like', '%-DEPT-%')->delete();
        DB::table('units')->whereIn('code', ['DAHMIYA', 'SABALIYA', 'GHALIL'])->delete();
        DB::table('units')->whereIn('code', ['ALABBASI-MAIN', 'HODEIDAH', 'MAABAR', 'SANAA'])->delete();
        DB::table('holdings')->where('code', 'ALABBASI')->delete();

        echo "\n❌ تم حذف البيانات التجريبية لنظام العباسي\n\n";
    }
};
