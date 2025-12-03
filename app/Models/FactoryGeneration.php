<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 🧬 Model: FactoryGeneration
 * 
 * نموذج توليد الـ Factories
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Models
 * @package App\Models
 */
class FactoryGeneration extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * اسم الجدول
     */
    protected $table = 'factory_generations';

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'name',
        'description',
        'model_name',
        'table_name',
        'input_method',
        'input_data',
        'generated_content',
        'file_path',
        'use_ai',
        'ai_provider',
        'status',
        'error_message',
        'created_by',
        'updated_by',
    ];

    /**
     * الحقول المخفية
     */
    protected $hidden = [];

    /**
     * تحويل الحقول
     */
    protected $casts = [
        'input_data' => 'array',
        'use_ai' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * الحالات المتاحة
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_GENERATED = 'generated';
    const STATUS_SAVED = 'saved';
    const STATUS_ERROR = 'error';

    /**
     * طرق الإدخال المتاحة
     */
    const INPUT_METHOD_WEB = 'web';
    const INPUT_METHOD_JSON = 'json';
    const INPUT_METHOD_TEMPLATE = 'template';
    const INPUT_METHOD_REVERSE = 'reverse';
    const INPUT_METHOD_AI = 'ai';

    /**
     * مزودي الذكاء الاصطناعي
     */
    const AI_PROVIDER_OPENAI = 'openai';
    const AI_PROVIDER_CLAUDE = 'claude';

    /**
     * العلاقة مع المستخدم الذي أنشأ
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع المستخدم الذي عدّل
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope: حسب الحالة
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: حسب طريقة الإدخال
     */
    public function scopeByInputMethod($query, string $method)
    {
        return $query->where('input_method', $method);
    }

    /**
     * Scope: المولدة بالذكاء الاصطناعي
     */
    public function scopeAiGenerated($query)
    {
        return $query->where('use_ai', true);
    }

    /**
     * Scope: الأحدث أولاً
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * تحديد الحالة إلى "مولد"
     */
    public function markAsGenerated(): void
    {
        $this->update(['status' => self::STATUS_GENERATED]);
    }

    /**
     * تحديد الحالة إلى "محفوظ"
     */
    public function markAsSaved(string $filePath): void
    {
        $this->update([
            'status' => self::STATUS_SAVED,
            'file_path' => $filePath,
        ]);
    }

    /**
     * تحديد الحالة إلى "خطأ"
     */
    public function markAsError(string $errorMessage): void
    {
        $this->update([
            'status' => self::STATUS_ERROR,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * هل تم توليده بنجاح؟
     */
    public function isGenerated(): bool
    {
        return in_array($this->status, [self::STATUS_GENERATED, self::STATUS_SAVED]);
    }

    /**
     * هل تم حفظه كملف؟
     */
    public function isSaved(): bool
    {
        return $this->status === self::STATUS_SAVED && !empty($this->file_path);
    }

    /**
     * هل حدث خطأ؟
     */
    public function hasError(): bool
    {
        return $this->status === self::STATUS_ERROR;
    }

    /**
     * الحصول على اسم الملف
     */
    public function getFileName(): string
    {
        return $this->model_name . 'Factory.php';
    }

    /**
     * الحصول على المسار الكامل للملف
     */
    public function getFullPath(): string
    {
        return database_path('factories/' . $this->getFileName());
    }
}
