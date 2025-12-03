<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * ModelTemplate Model
 * قوالب الـ Models
 * 
 * @package App\Models
 * @version 1.0.0
 * @since 2025-12-03
 * 
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $category
 * @property string|null $icon
 * @property string $template_content
 * @property array|null $template_variables
 * @property array|null $placeholders
 * @property array|null $features
 * @property array|null $default_traits
 * @property array|null $default_casts
 * @property array|null $default_relations
 * @property array|null $default_scopes
 * @property bool $has_timestamps
 * @property bool $has_soft_deletes
 * @property bool $generate_observer
 * @property bool $generate_factory
 * @property bool $generate_seeder
 * @property bool $generate_policy
 * @property bool $is_active
 * @property bool $is_default
 * @property bool $is_system
 * @property int $usage_count
 * @property int $success_count
 * @property int $failure_count
 * @property float $success_rate
 * @property float $rating
 * @property int $rating_count
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class ModelTemplate extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'model_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'icon',
        'template_content',
        'template_variables',
        'placeholders',
        'features',
        'default_traits',
        'default_casts',
        'default_relations',
        'default_scopes',
        'has_timestamps',
        'has_soft_deletes',
        'generate_observer',
        'generate_factory',
        'generate_seeder',
        'generate_policy',
        'is_active',
        'is_default',
        'is_system',
        'usage_count',
        'success_count',
        'failure_count',
        'success_rate',
        'rating',
        'rating_count',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'template_variables' => 'array',
        'placeholders' => 'array',
        'features' => 'array',
        'default_traits' => 'array',
        'default_casts' => 'array',
        'default_relations' => 'array',
        'default_scopes' => 'array',
        'has_timestamps' => 'boolean',
        'has_soft_deletes' => 'boolean',
        'generate_observer' => 'boolean',
        'generate_factory' => 'boolean',
        'generate_seeder' => 'boolean',
        'generate_policy' => 'boolean',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'is_system' => 'boolean',
        'usage_count' => 'integer',
        'success_count' => 'integer',
        'failure_count' => 'integer',
        'success_rate' => 'decimal:2',
        'rating' => 'decimal:2',
        'rating_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });

        static::updating(function ($template) {
            if ($template->isDirty('name') && empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });
    }

    /**
     * Get the generations for the template.
     */
    public function generations(): HasMany
    {
        return $this->hasMany(ModelGeneration::class, 'template_id');
    }

    /**
     * Get the creator of the template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater of the template.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include default templates.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include system templates.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to order by usage count.
     */
    public function scopePopular($query)
    {
        return $query->orderBy('usage_count', 'desc');
    }

    /**
     * Scope a query to order by rating.
     */
    public function scopeTopRated($query)
    {
        return $query->orderBy('rating', 'desc');
    }

    /**
     * Increment usage count.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Increment success count.
     */
    public function incrementSuccess(): void
    {
        $this->increment('success_count');
        $this->updateSuccessRate();
    }

    /**
     * Increment failure count.
     */
    public function incrementFailure(): void
    {
        $this->increment('failure_count');
        $this->updateSuccessRate();
    }

    /**
     * Update success rate.
     */
    protected function updateSuccessRate(): void
    {
        $total = $this->success_count + $this->failure_count;
        if ($total > 0) {
            $this->update([
                'success_rate' => ($this->success_count / $total) * 100
            ]);
        }
    }

    /**
     * Add rating.
     */
    public function addRating(float $rating): void
    {
        $totalRating = $this->rating * $this->rating_count;
        $newRatingCount = $this->rating_count + 1;
        $newRating = ($totalRating + $rating) / $newRatingCount;

        $this->update([
            'rating' => $newRating,
            'rating_count' => $newRatingCount,
        ]);
    }

    /**
     * Get the template content with replaced variables.
     */
    public function getProcessedContent(array $variables = []): string
    {
        $content = $this->template_content;
        
        foreach ($variables as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $content = str_replace($placeholder, $value, $content);
        }

        return $content;
    }

    /**
     * Check if template has feature.
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * Get the category label in Arabic.
     */
    public function getCategoryLabelAttribute(): ?string
    {
        if (!$this->category) {
            return null;
        }

        return match($this->category) {
            'basic' => 'أساسي',
            'advanced' => 'متقدم',
            'api' => 'API',
            'ecommerce' => 'تجارة إلكترونية',
            'accounting' => 'محاسبة',
            'crm' => 'CRM',
            'hrm' => 'HRM',
            default => $this->category,
        };
    }

    /**
     * Get the success rate formatted.
     */
    public function getSuccessRateFormattedAttribute(): string
    {
        return number_format($this->success_rate, 2) . '%';
    }

    /**
     * Get the rating formatted.
     */
    public function getRatingFormattedAttribute(): string
    {
        return number_format($this->rating, 2) . ' / 5.00';
    }
}
