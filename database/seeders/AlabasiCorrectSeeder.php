<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlabasiCorrectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->command->info('🚀 بدء إنشاء بيانات العباسي حسب البنية الصحيحة...');
            $this->command->info('');
            
            // 1. إنشاء 3 Organizations (الشراكات)
            $this->command->info('📊 إنشاء Organizations (الشراكات)...');
            
            $org1Id = DB::table('organizations')->insertGetId([
                'name' => 'شراكة محطات الحديدة',
                'code' => 'HODEIDAH_PARTNERSHIP',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info("  ✅ شراكة محطات الحديدة (ID: {$org1Id})");
            
            $org2Id = DB::table('organizations')->insertGetId([
                'name' => 'شراكة محطة معبر',
                'code' => 'MAABAR_PARTNERSHIP',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info("  ✅ شراكة محطة معبر (ID: {$org2Id})");
            
            $org3Id = DB::table('organizations')->insertGetId([
                'name' => 'شراكة سوبر ماركت صنعاء',
                'code' => 'SANAA_SUPERMARKET_PARTNERSHIP',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info("  ✅ شراكة سوبر ماركت صنعاء (ID: {$org3Id})");
            $this->command->info('');
            
            // 2. إنشاء Units (المحطات والأعمال)
            $this->command->info('🏭 إنشاء Units (المحطات والأعمال)...');
            
            // محطات الحديدة (5 محطات)
            $stations = [
                ['name' => 'محطة الدهمية', 'code' => 'DAHMIYA'],
                ['name' => 'محطة الصبالية', 'code' => 'SABALIYA'],
                ['name' => 'محطة جمال', 'code' => 'JAMAL'],
                ['name' => 'محطة غليل', 'code' => 'GHALIL'],
                ['name' => 'محطة الساحل الغربي', 'code' => 'WEST_COAST'],
            ];
            
            $unitIds = [];
            foreach ($stations as $station) {
                $unitId = DB::table('units')->insertGetId([
                    'organization_id' => $org1Id,
                    'name' => $station['name'],
                    'code' => $station['code'],
                    'type' => 'power_station',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $unitIds[] = $unitId;
                $this->command->info("  ✅ {$station['name']} (ID: {$unitId})");
            }
            
            // محطة معبر
            $unitId = DB::table('units')->insertGetId([
                'organization_id' => $org2Id,
                'name' => 'محطة معبر',
                'code' => 'MAABAR_STATION',
                'type' => 'power_station',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $unitIds[] = $unitId;
            $this->command->info("  ✅ محطة معبر (ID: {$unitId})");
            
            // سوبر ماركت صنعاء
            $unitId = DB::table('units')->insertGetId([
                'organization_id' => $org3Id,
                'name' => 'سوبر ماركت صنعاء',
                'code' => 'SANAA_SUPERMARKET',
                'type' => 'retail',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $unitIds[] = $unitId;
            $this->command->info("  ✅ سوبر ماركت صنعاء (ID: {$unitId})");
            $this->command->info('');
            
            // 3. إنشاء الشركاء (في جدول partners)
            $this->command->info('👥 إنشاء الشركاء...');
            
            // استخدام أول unit_id كافتراضي
            $defaultUnitId = $unitIds[0];
            
            // إنشاء holding افتراضي إذا لم يكن موجوداً
            $holdingId = DB::table('holdings')->value('id');
            if (!$holdingId) {
                $holdingId = DB::table('holdings')->insertGetId([
                    'name' => 'مجموعة أعمال العباسي',
                    'code' => 'ALABASI_GROUP',
                    'description' => 'الشركة القابضة',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info("  ℹ️  تم إنشاء Holding افتراضي (ID: {$holdingId})");
            }
            
            // إنشاء project افتراضي
            $projectId = DB::table('projects')->insertGetId([
                'name' => 'مشاريع الشراكات',
                'code' => 'PARTNERSHIPS',
                'description' => 'مشاريع الشراكات',
                'start_date' => now(),
                'end_date' => null,
                'budget' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info("  ℹ️  تم إنشاء Project افتراضي (ID: {$projectId})");
            $this->command->info('');
            
            $partners = [
                ['name' => 'العباسي', 'code' => 'ALABASI'],
                ['name' => 'الشريك الأول', 'code' => 'PARTNER_1'],
                ['name' => 'الشريك الثاني', 'code' => 'PARTNER_2'],
                ['name' => 'الشريك الثالث', 'code' => 'PARTNER_3'],
                ['name' => 'الشريك الرابع', 'code' => 'PARTNER_4'],
                ['name' => 'الشريك الخامس', 'code' => 'PARTNER_5'],
            ];
            
            $partnerIds = [];
            foreach ($partners as $partner) {
                $partnerId = DB::table('partners')->insertGetId([
                    'name' => $partner['name'],
                    'code' => $partner['code'],
                    'holding_id' => $holdingId,
                    'unit_id' => $defaultUnitId,
                    'project_id' => $projectId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $partnerIds[$partner['code']] = $partnerId;
                $this->command->info("  ✅ {$partner['name']} (ID: {$partnerId})");
            }
            $this->command->info('');
            
            // 4. إنشاء حصص الشركاء (Partner_Shares)
            $this->command->info('💰 إنشاء حصص الشركاء...');
            
            // شراكة محطات الحديدة: 70% العباسي، 30% الشريك الأول
            DB::table('partnership_shares')->insert([
                [
                    'holding_id' => $holdingId,
                    'unit_id' => $defaultUnitId,
                    'project_id' => $projectId,
                    'share_percentage' => 70.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'holding_id' => $holdingId,
                    'unit_id' => $defaultUnitId,
                    'project_id' => $projectId,
                    'share_percentage' => 30.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
            $this->command->info("  ✅ حصص شراكة محطات الحديدة");
            
            // يمكن إضافة باقي الحصص لاحقاً
            
            $this->command->info('');
            $this->command->info('✅ تم إنشاء جميع البيانات بنجاح!');
            $this->command->info('');
            $this->command->info('📊 الملخص:');
            $this->command->info('  - 3 Organizations (شراكات)');
            $this->command->info('  - 7 Units (محطات وأعمال)');
            $this->command->info('  - 6 Partners (شركاء)');
            $this->command->info('  - 2 Partnership Shares (حصص)');
        });
    }
}
