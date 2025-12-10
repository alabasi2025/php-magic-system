<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'فاتورة مشتريات الدهمية',
                'code' => 'DAHMIYA',
                'prefix' => 'DH',
                'description' => 'فواتير مشتريات خاصة بمخزن الدهمية',
                'is_active' => true,
                'last_number' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'فاتورة مشتريات الصبالية',
                'code' => 'SABALIYA',
                'prefix' => 'SB',
                'description' => 'فواتير مشتريات خاصة بمخزن الصبالية',
                'is_active' => true,
                'last_number' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'فاتورة مشتريات غليل',
                'code' => 'GHALIL',
                'prefix' => 'GL',
                'description' => 'فواتير مشتريات خاصة بمخزن غليل',
                'is_active' => true,
                'last_number' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('purchase_invoice_types')->insert($types);
    }
}
