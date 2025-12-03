<?php

namespace Database\Factories;

use App\Models\ModelTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModelTemplate>
 */
class ModelTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ModelTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(2, true);
        $slug = Str::slug($name);

        return [
            'name' => ucwords($name),
            'slug' => $slug,
            'description' => $this->faker->sentence(),
            'category' => $this->faker->randomElement(['basic', 'advanced', 'api', 'ecommerce', 'accounting']),
            'icon' => $this->faker->randomElement(['📦', '🛒', '💰', '👤', '📊']),
            'template_content' => $this->generateSampleTemplate(),
            'template_variables' => [
                'name' => 'Model Name',
                'table_name' => 'Table Name',
                'description' => 'Description',
            ],
            'placeholders' => [
                '{{name}}',
                '{{table_name}}',
                '{{description}}',
            ],
            'features' => ['timestamps', 'fillable', 'casts'],
            'default_traits' => ['HasFactory'],
            'default_casts' => [
                'created_at' => 'datetime',
                'updated_at' => 'datetime',
            ],
            'default_relations' => [],
            'default_scopes' => [],
            'has_timestamps' => true,
            'has_soft_deletes' => false,
            'generate_observer' => false,
            'generate_factory' => true,
            'generate_seeder' => false,
            'generate_policy' => false,
            'is_active' => true,
            'is_default' => false,
            'is_system' => false,
            'usage_count' => 0,
            'success_count' => 0,
            'failure_count' => 0,
            'success_rate' => 0,
            'rating' => 0,
            'rating_count' => 0,
            'created_by' => null,
            'updated_by' => null,
        ];
    }

    /**
     * Generate sample template content
     */
    protected function generateSampleTemplate(): string
    {
        return <<<'TEMPLATE'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * {{name}} Model
 * {{description}}
 */
class {{name}} extends Model
{
    use HasFactory;

    protected $table = '{{table_name}}';

    protected $fillable = [
        // Add fillable fields here
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
TEMPLATE;
    }

    /**
     * Indicate that the template is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the template is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the template is default.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    /**
     * Indicate that the template is a system template.
     */
    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_system' => true,
        ]);
    }

    /**
     * Set usage statistics.
     */
    public function withUsage(int $count = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'usage_count' => $count,
            'success_count' => (int)($count * 0.8),
            'failure_count' => (int)($count * 0.2),
            'success_rate' => 80,
        ]);
    }

    /**
     * Set rating.
     */
    public function withRating(float $rating = 4.5, int $count = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $rating,
            'rating_count' => $count,
        ]);
    }
}
