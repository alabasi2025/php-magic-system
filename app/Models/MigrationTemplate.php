<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 🧬 Model: MigrationTemplate
 * 
 * نموذج لإدارة قوالب الـ migrations الجاهزة
 * 
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $category
 * @property string $template_content
 * @property array|null $variables
 * @property bool $is_active
 * @property int $usage_count
 * @property int|null $created_by
 * 
 * @version 1.0.0
 * @since 2025-12-03
 */
class MigrationTemplate extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'migration_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'category',
        'template_content',
        'variables',
        'is_active',
        'usage_count',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * فئات القوالب المدعومة
     */
    const CATEGORY_BASIC = 'basic';
    const CATEGORY_ACCOUNTING = 'accounting';
    const CATEGORY_ECOMMERCE = 'ecommerce';
    const CATEGORY_CRM = 'crm';
    const CATEGORY_BLOG = 'blog';
    const CATEGORY_CUSTOM = 'custom';

    /**
     * Get the user who created this template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope للحصول على القوالب النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope للحصول على القوالب حسب الفئة
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope للحصول على القوالب الأكثر استخداماً
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }

    /**
     * زيادة عداد الاستخدام
     */
    public function incrementUsage(): bool
    {
        return $this->increment('usage_count');
    }

    /**
     * تفعيل القالب
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * تعطيل القالب
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * استبدال المتغيرات في محتوى القالب
     */
    public function render(array $data): string
    {
        $content = $this->template_content;
        
        foreach ($data as $key => $value) {
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }
        
        return $content;
    }

    /**
     * الحصول على قائمة المتغيرات المطلوبة
     */
    public function getRequiredVariables(): array
    {
        return $this->variables ?? [];
    }
}
