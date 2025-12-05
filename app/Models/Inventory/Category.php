<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Category|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $children
 * @property-read int|null $children_count
 */
class Category extends Model
{
    use HasFactory;

    // اسم الجدول في قاعدة البيانات
    protected $table = 'categories';

    // الحقول المسموح بتعبئتها جماعياً
    protected $fillable = [
        'parent_id',
        'name',
        'description',
        'is_active',
    ];

    // تحويل أنواع الحقول
    protected $casts = [
        'is_active' => 'boolean',
        'parent_id' => 'integer',
    ];

    /**
     * علاقة الفئة الأب (Parent Category).
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        // الفئة تنتمي إلى فئة أب واحدة (أو لا تنتمي إذا كانت هي الجذر)
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * علاقة الفئات الفرعية (Child Categories).
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        // الفئة لديها العديد من الفئات الفرعية
        return $this->hasMany(Category::class, 'parent_id');
    }

    // يمكنك إضافة علاقات أخرى هنا مثل علاقة الأصناف (Items)
}
