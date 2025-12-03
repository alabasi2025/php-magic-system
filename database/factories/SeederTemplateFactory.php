<?php

/**
 * 🧬 Gene: SeederTemplateFactory
 * 
 * Factory لتوليد بيانات وهمية لـ SeederTemplate
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Factories
 * @package Database\Factories
 */

namespace Database\Factories;

use App\Models\SeederTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeederTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SeederTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $tableName = $this->faker->word . 's';
        $modelName = str_replace('_', '', ucwords(Str::singular($tableName), '_'));

        return [
            'name' => 'قالب ' . $this->faker->words(2, true),
            'description' => $this->faker->sentence,
            'category' => $this->faker->randomElement(array_keys(SeederTemplate::getCategories())),
            'table_name' => $tableName,
            'model_name' => $modelName,
            'default_count' => $this->faker->randomElement([10, 20, 50, 100]),
            'schema' => [
                'columns' => [
                    'name' => ['type' => 'name'],
                    'email' => ['type' => 'email', 'unique' => true],
                ]
            ],
            'is_active' => true,
            'usage_count' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
