<?php

namespace Database\Factories;

use App\Models\ModelGeneration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModelGeneration>
 */
class ModelGenerationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ModelGeneration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->word();
        $modelName = ucfirst($name);
        $tableName = strtolower($name) . 's';

        return [
            'name' => $modelName,
            'description' => $this->faker->sentence(),
            'table_name' => $tableName,
            'namespace' => 'App\\Models',
            'extends' => 'Model',
            'input_method' => $this->faker->randomElement(['text', 'json', 'database', 'migration', 'ai']),
            'input_data' => [
                'source' => 'factory',
            ],
            'generated_content' => $this->generateSampleContent($modelName, $tableName),
            'generated_files' => [],
            'file_path' => null,
            'use_ai' => false,
            'ai_provider' => null,
            'ai_suggestions' => null,
            'ai_prompt' => null,
            'attributes' => [
                ['name' => 'name', 'type' => 'string', 'nullable' => false],
                ['name' => 'description', 'type' => 'text', 'nullable' => true],
            ],
            'fillable' => ['name', 'description'],
            'hidden' => [],
            'casts' => [],
            'dates' => [],
            'appends' => [],
            'relations' => [],
            'scopes' => [],
            'traits' => ['HasFactory'],
            'interfaces' => [],
            'accessors' => [],
            'mutators' => [],
            'has_timestamps' => true,
            'has_soft_deletes' => false,
            'has_observer' => false,
            'has_factory' => false,
            'has_seeder' => false,
            'has_policy' => false,
            'has_resource' => false,
            'is_validated' => false,
            'validation_results' => null,
            'is_tested' => false,
            'test_results' => null,
            'status' => ModelGeneration::STATUS_DRAFT,
            'error_message' => null,
            'warnings' => [],
            'template_id' => null,
            'created_by' => null,
            'updated_by' => null,
        ];
    }

    /**
     * Generate sample model content
     */
    protected function generateSampleContent(string $modelName, string $tableName): string
    {
        return <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$modelName} extends Model
{
    use HasFactory;

    protected \$table = '{$tableName}';

    protected \$fillable = [
        'name',
        'description',
    ];

    protected \$casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
PHP;
    }

    /**
     * Indicate that the model is generated.
     */
    public function generated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ModelGeneration::STATUS_GENERATED,
        ]);
    }

    /**
     * Indicate that the model is validated.
     */
    public function validated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ModelGeneration::STATUS_VALIDATED,
            'is_validated' => true,
        ]);
    }

    /**
     * Indicate that the model is deployed.
     */
    public function deployed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ModelGeneration::STATUS_DEPLOYED,
            'file_path' => app_path('Models/' . $attributes['name'] . '.php'),
        ]);
    }

    /**
     * Indicate that the model generation failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ModelGeneration::STATUS_FAILED,
            'error_message' => 'Test error message',
        ]);
    }

    /**
     * Indicate that AI was used.
     */
    public function withAI(): static
    {
        return $this->state(fn (array $attributes) => [
            'use_ai' => true,
            'ai_provider' => 'openai',
            'ai_suggestions' => [
                'relations' => [],
                'scopes' => [],
            ],
        ]);
    }

    /**
     * Add relations to the model.
     */
    public function withRelations(): static
    {
        return $this->state(fn (array $attributes) => [
            'relations' => [
                ['type' => 'belongsTo', 'model' => 'User', 'method' => 'user'],
                ['type' => 'hasMany', 'model' => 'Comment', 'method' => 'comments'],
            ],
        ]);
    }

    /**
     * Add scopes to the model.
     */
    public function withScopes(): static
    {
        return $this->state(fn (array $attributes) => [
            'scopes' => [
                ['name' => 'active', 'condition' => 'is_active = true'],
                ['name' => 'recent', 'condition' => 'created_at >= NOW() - INTERVAL 7 DAY'],
            ],
        ]);
    }
}
