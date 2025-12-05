<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class GeneratedRequest
 * @package App\Models
 *
 * @brief نموذج Form Request المولد.
 *
 * يمثل هذا النموذج Form Request المولد في قاعدة البيانات،
 * مع جميع المعلومات والإعدادات المتعلقة به.
 *
 * Model for Generated Form Request.
 *
 * This model represents a generated Form Request in the database,
 * with all related information and configurations.
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string|null $description
 * @property array $config
 * @property string $code
 * @property string|null $file_path
 * @property int|null $file_size
 * @property bool $is_saved
 * @property bool $is_active
 * @property int|null $user_id
 * @property int $fields_count
 * @property bool $has_authorization
 * @property bool $has_custom_messages
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @version 3.29.0
 * @author Manus AI
 */
class GeneratedRequest extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string $table اسم الجدول.
     * The table associated with the model.
     */
    protected $table = 'generated_requests';

    /**
     * @var array $fillable الحقول القابلة للتعبئة.
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'config',
        'code',
        'file_path',
        'file_size',
        'is_saved',
        'is_active',
        'user_id',
        'fields_count',
        'has_authorization',
        'has_custom_messages',
    ];

    /**
     * @var array $casts تحويل أنواع البيانات.
     * The attributes that should be cast.
     */
    protected $casts = [
        'config' => 'array',
        'is_saved' => 'boolean',
        'is_active' => 'boolean',
        'has_authorization' => 'boolean',
        'has_custom_messages' => 'boolean',
        'fields_count' => 'integer',
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @brief العلاقة مع المستخدم.
     *
     * Relationship with User.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @brief الحصول على حجم الملف بصيغة قابلة للقراءة.
     *
     * Get file size in human-readable format.
     *
     * @return string
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * @brief الحصول على عدد الحقول من الإعدادات.
     *
     * Get fields count from configuration.
     *
     * @return int
     */
    public function getFieldsCountFromConfig(): int
    {
        if (!isset($this->config['fields'])) {
            return 0;
        }

        return count($this->config['fields']);
    }

    /**
     * @brief التحقق من وجود Authorization.
     *
     * Check if has authorization.
     *
     * @return bool
     */
    public function hasAuthorization(): bool
    {
        return $this->has_authorization || ($this->config['authorization'] ?? false);
    }

    /**
     * @brief التحقق من وجود رسائل مخصصة.
     *
     * Check if has custom messages.
     *
     * @return bool
     */
    public function hasCustomMessages(): bool
    {
        return $this->has_custom_messages || ($this->config['custom_messages'] ?? false);
    }

    /**
     * @brief Scope للـ Requests النشطة فقط.
     *
     * Scope for active requests only.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * @brief Scope للـ Requests المحفوظة فقط.
     *
     * Scope for saved requests only.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSaved($query)
    {
        return $query->where('is_saved', true);
    }

    /**
     * @brief Scope حسب النوع.
     *
     * Scope by type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
