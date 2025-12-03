<?php

namespace Database\Factories;

use App\Models\MigrationTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MigrationTemplateFactory extends Factory
{
    protected $model = MigrationTemplate::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'category' => $this->faker->randomElement(['basic', 'accounting', 'ecommerce']),
            'template_content' => '<?php // Template content with {{table_name}}',
            'variables' => [
                'table_name' => 'string',
                'column_name' => 'string',
            ],
            'is_active' => true,
            'usage_count' => $this->faker->numberBetween(0, 100),
            'created_by' => User::factory(),
        ];
    }
}
