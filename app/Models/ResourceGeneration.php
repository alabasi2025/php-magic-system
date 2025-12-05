<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ResourceGeneration Model
 * نموذج سجل توليد API Resources
 *
 * يمثل سجل توليد API Resource واحد في النظام.
 * Represents a single API Resource generation record in the system.
 *
 * @property int $id
 * @property string $name اسم الـ Resource
 * @property string $type نوع الـ Resource (single, collection, nested)
 * @property string|null $model اسم الـ Model المرتبط
 * @property array $attributes الخصائص المطلوبة
 * @property array|null $relations العلاقات
 * @property array|null $conditional_attributes الخصائص الشرطية
 * @property array|null $options خيارات إضافية
 * @property string $file_path مسار الملف
 * @property string $content محتوى الملف المولد
 * @property string $status الحالة (pending, success, failed)
 * @property string|null $error_message رسالة الخطأ
 * @property bool $ai_generated هل تم التوليد بالـ AI
 * @property string|null $ai_prompt الـ Prompt المستخدم
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @package App\Models
 * @version v3.30.0
 * @author Manus AI
 */
class ResourceGeneration extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * الجدول المرتبط بالنموذج
     *
     * @var string
     */
    protected $table = 'resource_generations';

    /**
     * The attributes that are mass assignable.
     * الخصائص القابلة للتعيين الجماعي
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'model',
        'attributes',
        'relations',
        'conditional_attributes',
        'options',
        'file_path',
        'content',
        'status',
        'error_message',
        'ai_generated',
        'ai_prompt',
    ];

    /**
     * The attributes that should be cast.
     * الخصائص التي يجب تحويلها
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attributes' => 'array',
        'relations' => 'array',
        'conditional_attributes' => 'array',
        'options' => 'array',
        'ai_generated' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all successful generations.
     * الحصول على جميع التوليدات الناجحة
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Get all failed generations.
     * الحصول على جميع التوليدات الفاشلة
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Get all pending generations.
     * الحصول على جميع التوليدات المعلقة
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get all AI-generated resources.
     * الحصول على جميع الـ Resources المولدة بالـ AI
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAiGenerated($query)
    {
        return $query->where('ai_generated', true);
    }

    /**
     * Get generations by type.
     * الحصول على التوليدات حسب النوع
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
     * Get generations by model.
     * الحصول على التوليدات حسب الـ Model
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForModel($query, string $model)
    {
        return $query->where('model', $model);
    }

    /**
     * Check if generation was successful.
     * التحقق من نجاح التوليد
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Check if generation failed.
     * التحقق من فشل التوليد
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if generation is pending.
     * التحقق من حالة الانتظار
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Mark generation as successful.
     * تحديد التوليد كناجح
     *
     * @return bool
     */
    public function markAsSuccessful(): bool
    {
        return $this->update(['status' => 'success']);
    }

    /**
     * Mark generation as failed.
     * تحديد التوليد كفاشل
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
     * Get statistics for resource generations.
     * الحصول على إحصائيات التوليد
     *
     * @return array<string, mixed>
     */
    public static function getStatistics(): array
    {
        return [
            'total' => self::count(),
            'successful' => self::successful()->count(),
            'failed' => self::failed()->count(),
            'pending' => self::pending()->count(),
            'ai_generated' => self::aiGenerated()->count(),
            'by_type' => [
                'single' => self::ofType('single')->count(),
                'collection' => self::ofType('collection')->count(),
                'nested' => self::ofType('nested')->count(),
            ],
        ];
    }
}
