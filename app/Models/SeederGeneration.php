<?php

/**
 * 🧬 Gene: SeederGeneration Model
 * 
 * Model لتخزين وإدارة الـ Seeders المولدة
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Models
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeederGeneration extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * اسم الجدول
     */
    protected $table = 'seeder_generations';

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'name',
        'description',
        'table_name',
        'model_name',
        'count',
        'input_method',
        'input_data',
        'generated_content',
        'use_ai',
        'ai_provider',
        'ai_suggestions',
        'status',
        'execution_time',
        'records_created',
        'error_message',
        'created_by',
    ];

    /**
     * الحقول المخفية
     */
    protected $hidden = [];

    /**
     * تحويل الأنواع
     */
    protected $casts = [
        'input_data' => 'array',
        'ai_suggestions' => 'array',
        'use_ai' => 'boolean',
        'count' => 'integer',
        'execution_time' => 'float',
        'records_created' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * الحالات الممكنة
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_GENERATED = 'generated';
    const STATUS_TESTED = 'tested';
    const STATUS_EXECUTED = 'executed';
    const STATUS_FAILED = 'failed';

    /**
     * طرق الإدخال الممكنة
     */
    const INPUT_WEB = 'web';
    const INPUT_API = 'api';
    const INPUT_CLI = 'cli';
    const INPUT_JSON = 'json';
    const INPUT_TEMPLATE = 'template';
    const INPUT_REVERSE = 'reverse';

    /**
     * مزودي الذكاء الاصطناعي
     */
    const AI_OPENAI = 'openai';
    const AI_CLAUDE = 'claude';
    const AI_GEMINI = 'gemini';

    /**
     * العلاقة: المستخدم المنشئ
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
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
     * Scope: التي تستخدم AI
     */
    public function scopeUsingAI($query)
    {
        return $query->where('use_ai', true);
    }

    /**
     * Scope: الأحدث
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Scope: المنفذة بنجاح
     */
    public function scopeExecuted($query)
    {
        return $query->where('status', self::STATUS_EXECUTED);
    }

    /**
     * Scope: الفاشلة
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * التحقق: هل تم التوليد؟
     */
    public function isGenerated(): bool
    {
        return in_array($this->status, [
            self::STATUS_GENERATED,
            self::STATUS_TESTED,
            self::STATUS_EXECUTED
        ]);
    }

    /**
     * التحقق: هل هو مسودة؟
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * التحقق: هل تم التنفيذ؟
     */
    public function isExecuted(): bool
    {
        return $this->status === self::STATUS_EXECUTED;
    }

    /**
     * التحقق: هل فشل؟
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * التحقق: هل يستخدم AI؟
     */
    public function usesAI(): bool
    {
        return $this->use_ai === true;
    }

    /**
     * الحصول على اسم ملف الـ Seeder
     */
    public function getSeederFileName(): string
    {
        $tableName = str_replace('_', '', ucwords($this->table_name, '_'));
        return "{$tableName}Seeder.php";
    }

    /**
     * الحصول على اسم كلاس الـ Seeder
     */
    public function getSeederClassName(): string
    {
        $tableName = str_replace('_', '', ucwords($this->table_name, '_'));
        return "{$tableName}Seeder";
    }

    /**
     * الحصول على المسار الكامل لملف الـ Seeder
     */
    public function getSeederFilePath(): string
    {
        return database_path('seeders/' . $this->getSeederFileName());
    }

    /**
     * تحديث الحالة
     */
    public function updateStatus(string $status, ?string $errorMessage = null): bool
    {
        $data = ['status' => $status];
        
        if ($errorMessage) {
            $data['error_message'] = $errorMessage;
        }
        
        return $this->update($data);
    }

    /**
     * تسجيل نتيجة التنفيذ
     */
    public function recordExecution(float $executionTime, int $recordsCreated): bool
    {
        return $this->update([
            'status' => self::STATUS_EXECUTED,
            'execution_time' => $executionTime,
            'records_created' => $recordsCreated,
            'error_message' => null,
        ]);
    }

    /**
     * تسجيل فشل التنفيذ
     */
    public function recordFailure(string $errorMessage, ?float $executionTime = null): bool
    {
        $data = [
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
        ];
        
        if ($executionTime !== null) {
            $data['execution_time'] = $executionTime;
        }
        
        return $this->update($data);
    }

    /**
     * الحصول على قائمة الحالات
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_GENERATED => 'تم التوليد',
            self::STATUS_TESTED => 'تم الاختبار',
            self::STATUS_EXECUTED => 'تم التنفيذ',
            self::STATUS_FAILED => 'فشل',
        ];
    }

    /**
     * الحصول على قائمة طرق الإدخال
     */
    public static function getInputMethods(): array
    {
        return [
            self::INPUT_WEB => 'واجهة الويب',
            self::INPUT_API => 'API',
            self::INPUT_CLI => 'سطر الأوامر',
            self::INPUT_JSON => 'JSON Schema',
            self::INPUT_TEMPLATE => 'قالب جاهز',
            self::INPUT_REVERSE => 'من جدول موجود',
        ];
    }

    /**
     * الحصول على قائمة مزودي AI
     */
    public static function getAIProviders(): array
    {
        return [
            self::AI_OPENAI => 'OpenAI',
            self::AI_CLAUDE => 'Claude',
            self::AI_GEMINI => 'Gemini',
        ];
    }
}
