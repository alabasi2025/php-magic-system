<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Holding;
use Illuminate\Support\Facades\DB;

/**
 * HoldingSeeder - بيانات تجريبية للشركات القابضة
 * 
 * ينشئ 3 شركات قابضة بمجالات مختلفة
 */
class HoldingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('holdings')->delete();
        
        $holdings = [
            [
                'code' => 'HDG-001',
                'name' => 'مجموعة العباسي القابضة',
                'name_en' => 'Al-Abbasi Holding Group',
                'type' => 'holding',
                'sector' => 'diversified',
                'email' => 'info@alabasi-holding.com',
                'phone' => '+966112345678',
                'fax' => '+966112345679',
                'website' => 'https://alabasi-holding.com',
                'address' => 'طريق الملك فهد، حي العليا',
                'city' => 'الرياض',
                'country' => 'المملكة العربية السعودية',
                'postal_code' => '11564',
                'tax_number' => '300123456789003',
                'commercial_register' => '1010123456',
                'established_date' => '2010-01-15',
                'capital' => 500000000.00,
                'currency' => 'SAR',
                'fiscal_year_start' => '01-01',
                'is_active' => true,
                'notes' => 'شركة قابضة متنوعة الأنشطة في مجالات العقارات والتجارة والصناعة',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'HDG-002',
                'name' => 'مجموعة النور للاستثمار',
                'name_en' => 'Al-Noor Investment Group',
                'type' => 'investment',
                'sector' => 'financial_services',
                'email' => 'contact@alnoor-inv.com',
                'phone' => '+966126543210',
                'fax' => '+966126543211',
                'website' => 'https://alnoor-investment.com',
                'address' => 'شارع التحلية، حي السليمانية',
                'city' => 'جدة',
                'country' => 'المملكة العربية السعودية',
                'postal_code' => '23441',
                'tax_number' => '300234567890003',
                'commercial_register' => '4030234567',
                'established_date' => '2015-06-20',
                'capital' => 300000000.00,
                'currency' => 'SAR',
                'fiscal_year_start' => '01-01',
                'is_active' => true,
                'notes' => 'مجموعة استثمارية متخصصة في الخدمات المالية والاستشارات',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'HDG-003',
                'name' => 'مجموعة الفجر الصناعية',
                'name_en' => 'Al-Fajr Industrial Group',
                'type' => 'industrial',
                'sector' => 'manufacturing',
                'email' => 'info@alfajr-industrial.com',
                'phone' => '+966133456789',
                'fax' => '+966133456780',
                'website' => 'https://alfajr-industrial.com',
                'address' => 'المدينة الصناعية الثانية',
                'city' => 'الدمام',
                'country' => 'المملكة العربية السعودية',
                'postal_code' => '31421',
                'tax_number' => '300345678901003',
                'commercial_register' => '2050345678',
                'established_date' => '2008-03-10',
                'capital' => 750000000.00,
                'currency' => 'SAR',
                'fiscal_year_start' => '01-01',
                'is_active' => true,
                'notes' => 'مجموعة صناعية رائدة في مجال التصنيع والإنتاج',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($holdings as $holding) {
            Holding::create($holding);
        }

        $this->command->info('✅ تم إنشاء 3 شركات قابضة بنجاح');
    }
}
