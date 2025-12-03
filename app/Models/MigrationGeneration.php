<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 🧬 Model: MigrationGeneration
 * 
 * نموذج لإدارة عمليات توليد الـ migrations
 * 
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $table_name
 * @property string $migration_type
 * @property string $input_method
 * @property array $input_data
 * @property string $generated_content
 * @property string|null $file_path
 * @property string $status
 * @property array|null $ai_suggestions
 * @property array|null $validation_results
 * @property int|null $created_by
 * @property int|null $updated_by
 * 
 * @version 1.0.0
 * @since 2025-12-03
 */
class MigrationGeneration extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'migration_generations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'table_name',
        'migration_type',
        'input_method',
        'input_data',
        'generated_content',
        'file_path',
        'status',
        'ai_suggestions',
        'validation_results',
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
        'ai_suggestions' => 'array',
        'validation_results' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * أنواع الـ migrations المدعومة
     */
    const TYPE_CREATE = 'create';
    const TYPE_ALTER = 'alter';
    const TYPE_DROP = 'drop';

    /**
     * طرق الإدخال المدعومة
     */
    const INPUT_WEB = 'web';
    const INPUT_API = 'api';
    const INPUT_CLI = 'cli';
    const INPUT_JSON = 'json';

    /**
     * حالات الـ migration
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_GENERATED = 'generated';
    const STATUS_TESTED = 'tested';
    const STATUS_APPLIED = 'applied';

    /**
     * Get the user who created this migration generation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this migration generation.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope للحصول على migrations بحالة معينة
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope للحصول على migrations بنوع معين
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('migration_type', $type);
    }

    /**
     * Scope للحصول على migrations لجدول معين
     */
    public function scopeForTable($query, string $tableName)
    {
        return $query->where('table_name', $tableName);
    }

    /**
     * التحقق من أن الـ migration في حالة draft
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * التحقق من أن الـ migration تم توليده
     */
    public function isGenerated(): bool
    {
        return $this->status === self::STATUS_GENERATED;
    }

    /**
     * التحقق من أن الـ migration تم اختباره
     */
    public function isTested(): bool
    {
        return $this->status === self::STATUS_TESTED;
    }

    /**
     * التحقق من أن الـ migration تم تطبيقه
     */
    public function isApplied(): bool
    {
        return $this->status === self::STATUS_APPLIED;
    }

    /**
     * تحديث حالة الـ migration
     */
    public function updateStatus(string $status): bool
    {
        return $this->update(['status' => $status]);
    }

    /**
     * إضافة اقتراحات الذكاء الاصطناعي
     */
    public function addAiSuggestions(array $suggestions): bool
    {
        return $this->update(['ai_suggestions' => $suggestions]);
    }

    /**
     * إضافة نتائج التحقق
     */
    public function addValidationResults(array $results): bool
    {
        return $this->update(['validation_results' => $results]);
    }
}
