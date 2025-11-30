<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\ClientGene;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء العميل العباسي
        $client = Client::create([
            'name' => 'العباسي',
            'code' => 'ALABASI',
            'business_type' => 'محطات كهرباء + سوبر ماركت',
            'country' => 'اليمن',
            'city' => 'الحديدة',
            'contact_person' => 'العباسي',
            'phone' => null,
            'email' => null,
            'is_active' => true,
            'settings' => [
                'default_currency' => 'YER',
                'fiscal_year_start' => '01-01',
            ],
            'notes' => 'عميل يمتلك 3 شراكات: محطات الحديدة (5 محطات)، محطة معبر، سوبر ماركت صنعاء',
        ]);

        // تفعيل جين PARTNERSHIP_ACCOUNTING
        ClientGene::create([
            'client_name' => 'العباسي',
            'client_code' => 'ALABASI',
            'gene_name' => 'PARTNERSHIP_ACCOUNTING',
            'is_active' => true,
            'configuration' => [
                'enable_profit_calculation' => true,
                'enable_profit_distribution' => true,
                'enable_reports' => true,
            ],
            'notes' => 'جين محاسبة الشراكات - مفعل للعميل العباسي',
        ]);

        // تفعيل جين CLIENT_REQUIREMENTS
        ClientGene::create([
            'client_name' => 'العباسي',
            'client_code' => 'ALABASI',
            'gene_name' => 'CLIENT_REQUIREMENTS',
            'is_active' => true,
            'configuration' => null,
            'notes' => 'جين توثيق متطلبات العملاء',
        ]);

        $this->command->info('✅ تم إنشاء العميل العباسي وتفعيل الجينات');
    }
}
