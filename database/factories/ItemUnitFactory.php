<?php

namespace Database\Factories;

use App\Models\ItemUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemUnitFactory extends Factory
{
    protected $model = ItemUnit::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'لتر', 'كيلوجرام', 'جرام', 'متر', 'سنتيمتر',
                'ملليلتر', 'قطعة', 'كرتون', 'حزمة', 'دزينة',
                'طقم', 'باليت'
            ]),
            'name_en' => $this->faker->randomElement([
                'Liter', 'Kilogram', 'Gram', 'Meter', 'Centimeter',
                'Milliliter', 'Piece', 'Carton', 'Pack', 'Dozen',
                'Set', 'Pallet'
            ]),
            'symbol' => $this->faker->randomElement([
                'L', 'kg', 'g', 'm', 'cm', 'ml', 'pc', 'ctn', 'pk', 'dz', 'set', 'plt'
            ]),
            'type' => $this->faker->randomElement(['weight', 'volume', 'length', 'quantity']),
            'base_unit_id' => null,
            'conversion_factor' => 1,
            'is_active' => true,
        ];
    }
}
