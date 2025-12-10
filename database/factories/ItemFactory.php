<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\ItemUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'sku' => 'SKU-' . $this->faker->unique()->numberBetween(1000, 9999),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->sentence(),
            'unit_id' => ItemUnit::factory(),
            'unit_price' => $this->faker->randomFloat(2, 1, 1000),
            'min_stock' => $this->faker->numberBetween(10, 100),
            'max_stock' => $this->faker->numberBetween(500, 10000),
            'barcode' => $this->faker->optional()->ean13(),
            'image_path' => null,
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }

    /**
     * Indicate that the item is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the item is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Create diesel item.
     */
    public function diesel(): static
    {
        return $this->state(fn (array $attributes) => [
            'sku' => 'DIESEL-' . $this->faker->unique()->numberBetween(100, 999),
            'name' => 'ديزل',
            'description' => 'وقود ديزل عالي الجودة',
            'unit_price' => 5.50,
            'min_stock' => 100,
            'max_stock' => 10000,
            'status' => 'active',
        ]);
    }

    /**
     * Create benzene item.
     */
    public function benzene(): static
    {
        return $this->state(fn (array $attributes) => [
            'sku' => 'BENZENE-' . $this->faker->unique()->numberBetween(100, 999),
            'name' => 'بنزين',
            'description' => 'بنزين 95 أوكتان',
            'unit_price' => 6.00,
            'min_stock' => 50,
            'max_stock' => 8000,
            'status' => 'active',
        ]);
    }
}
