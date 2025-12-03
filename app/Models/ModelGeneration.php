<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\File;

/**
 * ModelGeneration Model
 * سجل توليد الـ Models
 * 
 * @package App\Models
 * @version 1.0.0
 * @since 2025-12-03
 * 
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $table_name
 * @property string $namespace
 * @property string $extends
 * @property string $input_method
 * @property array|null $input_data
 * @property string|null $generated_content
 * @property array|null $generated_files
 * @property string|null $file_path
 * @property bool $use_ai
 * @property string|null $ai_provider
 * @property array|null $ai_suggestions
 * @property string|null $ai_prompt
 * @property array|null $attributes
 * @property array|null $fillable
 * @property array|null $hidden
 * @property array|null $casts
 * @property array|null $dates
 * @property array|null $appends
 * @property array|null $relations
 * @property array|null $scopes
 * @property array|null $traits
 * @property array|null $interfaces
 * @property array|null $accessors
 * @property array|null $mutators
 * @property bool $has_timestamps
 * @property bool $has_soft_deletes
 * @property bool $has_observer
 * @property bool $has_factory
 * @property bool $has_seeder
 * @property bool $has_policy
 * @property bool $has_resource
 * @property bool $is_validated
 * @property array|null $validation_results
 * @property bool $is_tested
 * @property array|null $test_results
 * @property string $status
 * @property string|null $error_message
 * @property array|null $warnings
 * @property int|null $template_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class ModelGeneration extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'model_generations';

    /**
     * Status Constants
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_GENERATED = 'generated';
    const STATUS_VALIDATED = 'validated';
    const STATUS_DEPLOYED = 'deployed';
    const STATUS_FAILED = 'failed';

    /**
     * Input Method Constants
     */
    const INPUT_TEXT = 'text';
    const INPUT_JSON = 'json';
    const INPUT_DATABASE = 'database';
    const INPUT_MIGRATION = 'migration';
    const INPUT_AI = 'ai';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'table_name',
        'namespace',
        'extends',
        'input_method',
        'input_data',
        'generated_content',
        'generated_files',
        'file_path',
        'use_ai',
        'ai_provider',
        'ai_suggestions',
        'ai_prompt',
        'attributes',
        'fillable',
        'hidden',
        'casts',
        'dates',
        'appends',
        'relations',
        'scopes',
        'traits',
        'interfaces',
        'accessors',
        'mutators',
        'has_timestamps',
        'has_soft_deletes',
        'has_observer',
        'has_factory',
        'has_seeder',
        'has_policy',
        'has_resource',
        'is_validated',
        'validation_results',
        'is_tested',
        'test_results',
        'status',
        'error_message',
        'warnings',
        'template_id',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'input_data' => 'array',
        'generated_files' => 'array',
        'ai_suggestions' => 'array',
        'attributes' => 'array',
        'fillable' => 'array',
        'hidden' => 'array',
        'casts' => 'array',
        'dates' => 'array',
        'appends' => 'array',
        'relations' => 'array',
        'scopes' => 'array',
        'traits' => 'array',
        'interfaces' => 'array',
        'accessors' => 'array',
        'mutators' => 'array',
        'validation_results' => 'array',
        'test_results' => 'array',
        'warnings' => 'array',
        'use_ai' => 'boolean',
        'has_timestamps' => 'boolean',
        'has_soft_deletes' => 'boolean',
        'has_observer' => 'boolean',
        'has_factory' => 'boolean',
        'has_seeder' => 'boolean',
        'has_policy' => 'boolean',
        'has_resource' => 'boolean',
        'is_validated' => 'boolean',
        'is_tested' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the template that owns the generation.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ModelTemplate::class, 'template_id');
    }

    /**
     * Get the creator of the generation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater of the generation.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include draft generations.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope a query to only include generated generations.
     */
    public function scopeGenerated($query)
    {
        return $query->where('status', self::STATUS_GENERATED);
    }

    /**
     * Scope a query to only include validated generations.
     */
    public function scopeValidated($query)
    {
        return $query->where('status', self::STATUS_VALIDATED);
    }

    /**
     * Scope a query to only include deployed generations.
     */
    public function scopeDeployed($query)
    {
        return $query->where('status', self::STATUS_DEPLOYED);
    }

    /**
     * Scope a query to only include failed generations.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope a query to filter by input method.
     */
    public function scopeInputMethod($query, string $method)
    {
        return $query->where('input_method', $method);
    }

    /**
     * Scope a query to only include AI-enhanced generations.
     */
    public function scopeWithAI($query)
    {
        return $query->where('use_ai', true);
    }

    /**
     * Get the status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_GENERATED => 'تم التوليد',
            self::STATUS_VALIDATED => 'تم التحقق',
            self::STATUS_DEPLOYED => 'تم النشر',
            self::STATUS_FAILED => 'فشل',
            default => $this->status,
        };
    }

    /**
     * Get the input method label in Arabic.
     */
    public function getInputMethodLabelAttribute(): string
    {
        return match($this->input_method) {
            self::INPUT_TEXT => 'وصف نصي',
            self::INPUT_JSON => 'JSON Schema',
            self::INPUT_DATABASE => 'قاعدة البيانات',
            self::INPUT_MIGRATION => 'Migration File',
            self::INPUT_AI => 'AI Generation',
            default => $this->input_method,
        };
    }

    /**
     * Check if the generation is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if the generation is generated.
     */
    public function isGenerated(): bool
    {
        return $this->status === self::STATUS_GENERATED;
    }

    /**
     * Check if the generation is validated.
     */
    public function isValidated(): bool
    {
        return $this->status === self::STATUS_VALIDATED;
    }

    /**
     * Check if the generation is deployed.
     */
    public function isDeployed(): bool
    {
        return $this->status === self::STATUS_DEPLOYED;
    }

    /**
     * Check if the generation is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Mark as generated.
     */
    public function markAsGenerated(): void
    {
        $this->update(['status' => self::STATUS_GENERATED]);
    }

    /**
     * Mark as validated.
     */
    public function markAsValidated(): void
    {
        $this->update(['status' => self::STATUS_VALIDATED]);
    }

    /**
     * Mark as deployed.
     */
    public function markAsDeployed(): void
    {
        $this->update(['status' => self::STATUS_DEPLOYED]);
    }

    /**
     * Mark as failed.
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Get the full model class name.
     */
    public function getFullClassNameAttribute(): string
    {
        return $this->namespace . '\\' . $this->name;
    }

    /**
     * Get the file path for the model.
     */
    public function getDefaultFilePathAttribute(): string
    {
        $namespace = str_replace('App\\', '', $this->namespace);
        $path = str_replace('\\', '/', $namespace);
        return app_path($path . '/' . $this->name . '.php');
    }

    /**
     * Deploy the generated model to file system.
     */
    public function deploy(): bool
    {
        if (!$this->generated_content) {
            return false;
        }

        try {
            $filePath = $this->file_path ?? $this->default_file_path;
            $directory = dirname($filePath);

            // Create directory if not exists
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Write the file
            File::put($filePath, $this->generated_content);

            // Update status
            $this->update([
                'file_path' => $filePath,
                'status' => self::STATUS_DEPLOYED,
            ]);

            return true;
        } catch (\Exception $e) {
            $this->markAsFailed($e->getMessage());
            return false;
        }
    }

    /**
     * Get the traits list as array.
     */
    public function getTraitsListAttribute(): array
    {
        $traits = $this->traits ?? [];
        
        // Add default traits
        if ($this->has_soft_deletes && !in_array('SoftDeletes', $traits)) {
            $traits[] = 'SoftDeletes';
        }
        
        if ($this->has_factory && !in_array('HasFactory', $traits)) {
            $traits[] = 'HasFactory';
        }

        return $traits;
    }

    /**
     * Get the relations count.
     */
    public function getRelationsCountAttribute(): int
    {
        return count($this->relations ?? []);
    }

    /**
     * Get the scopes count.
     */
    public function getScopesCountAttribute(): int
    {
        return count($this->scopes ?? []);
    }

    /**
     * Check if has warnings.
     */
    public function hasWarnings(): bool
    {
        return !empty($this->warnings);
    }

    /**
     * Get warnings count.
     */
    public function getWarningsCountAttribute(): int
    {
        return count($this->warnings ?? []);
    }
}
