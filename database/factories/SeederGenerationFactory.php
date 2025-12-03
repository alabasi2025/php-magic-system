<?php

/**
 * 🧬 Gene: SeederGenerationFactory
 * 
 * Factory لتوليد بيانات وهمية لـ SeederGeneration
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Factories
 * @package Database\Factories
 */

namespace Database\Factories;

use App\Models\SeederGeneration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeederGenerationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SeederGeneration::class;

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
            'name' => ucwords($this->faker->words(2, true)) . ' Seeder',
            'description' => $this->faker->sentence,
            'table_name' => $tableName,
            'model_name' => $modelName,
            'count' => $this->faker->numberBetween(10, 100),
            'input_method' => $this->faker->randomElement(SeederGeneration::getInputMethods()),
            'input_data' => ['key' => 'value'],
            'generated_content' => '<?php // Generated content',
            'use_ai' => $this->faker->boolean,
            'ai_provider' => $this->faker->randomElement(SeederGeneration::getAIProviders()),
                        'ai_suggestions' => null,            'status' => $this->faker->randomElement(SeederGeneration::getStatuses()),
            'execution_time' => null,
            'records_created' => null,
            'error_message' => null,
            'created_by' => User::factory(),
        ];
    }
}
