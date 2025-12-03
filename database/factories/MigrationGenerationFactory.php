<?php

namespace Database\Factories;

use App\Models\MigrationGeneration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MigrationGenerationFactory extends Factory
{
    protected $model = MigrationGeneration::class;

    public function definition(): array
    {
        return [
            'name' => 'create_' . $this->faker->word() . '_table',
            'description' => $this->faker->sentence(),
            'table_name' => $this->faker->word(),
            'migration_type' => $this->faker->randomElement(['create', 'alter', 'drop']),
            'input_method' => $this->faker->randomElement(['web', 'api', 'cli', 'json']),
            'input_data' => [
                'columns' => [
                    ['name' => 'test_column', 'type' => 'string'],
                ],
            ],
            'generated_content' => '<?php // Test migration content',
            'status' => $this->faker->randomElement(['draft', 'generated', 'tested', 'applied']),
            'created_by' => User::factory(),
        ];
    }
}
