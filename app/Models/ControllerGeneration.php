<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Controller Generation Model
 * نموذج توليد المتحكمات
 * 
 * This model stores records of controller generation operations.
 * يخزن هذا النموذج سجلات عمليات توليد المتحكمات.
 * 
 * @package App\Models
 * @author Manus AI - Controller Generator v3.27.0
 * @generated 2025-12-03
 * 
 * @property int $id
 * @property string $name اسم المتحكم
 * @property string $type نوع المتحكم (resource, api, invokable, custom)
 * @property string|null $model_name اسم Model المرتبط
 * @property string $input_type نوع المدخل (text, json, model, ai)
 * @property array $input_data بيانات المدخل
 * @property array $generated_files الملفات المولدة
 * @property array|null $options خيارات إضافية
 * @property int|null $user_id معرف المستخدم
 * @property string $status حالة التوليد (pending, completed, failed)
 * @property string|null $error_message رسالة الخطأ إن وجدت
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class ControllerGeneration extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'controller_generations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'model_name',
        'input_type',
        'input_data',
        'generated_files',
        'options',
        'user_id',
        'status',
        'error_message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'input_data' => 'array',
        'generated_files' => 'array',
        'options' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Get the user that created this generation.
     * الحصول على المستخدم الذي أنشأ هذا التوليد
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include completed generations.
     * نطاق استعلام لتضمين التوليدات المكتملة فقط
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include failed generations.
     * نطاق استعلام لتضمين التوليدات الفاشلة فقط
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to filter by type.
     * نطاق استعلام للتصفية حسب النوع
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark the generation as completed.
     * وضع علامة على التوليد كمكتمل
     *
     * @param array $generatedFiles
     * @return bool
     */
    public function markAsCompleted(array $generatedFiles): bool
    {
        return $this->update([
            'status' => 'completed',
            'generated_files' => $generatedFiles,
            'error_message' => null,
        ]);
    }

    /**
     * Mark the generation as failed.
     * وضع علامة على التوليد كفاشل
     *
     * @param string $errorMessage
     * @return bool
     */
    public function markAsFailed(string $errorMessage): bool
    {
        return $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Get the count of generated files.
     * الحصول على عدد الملفات المولدة
     *
     * @return int
     */
    public function getGeneratedFilesCountAttribute(): int
    {
        return count($this->generated_files ?? []);
    }

    /**
     * Check if the generation was successful.
     * التحقق مما إذا كان التوليد ناجحاً
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the generation failed.
     * التحقق مما إذا كان التوليد فاشلاً
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the generation is pending.
     * التحقق مما إذا كان التوليد معلقاً
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
