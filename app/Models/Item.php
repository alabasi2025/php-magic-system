<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $code رمز الصنف
 * @property string|null $barcode الباركود
 * @property string $name اسم الصنف
 * @property string|null $description وصف الصنف
 * @property int $category_id معرف الفئة
 * @property int $unit_id معرف الوحدة
 * @property int $min_stock الحد الأدنى للمخزون
 * @property int $max_stock الحد الأقصى للمخزون
 * @property int $reorder_level مستوى إعادة الطلب
 * @property float $cost_price سعر التكلفة
 * @property float $selling_price سعر البيع
 * @property bool $is_active حالة التفعيل
 * @property string|null $image مسار صورة الصنف
 */
class Item extends Model
{
    use HasFactory;

    // تحديد الحقول التي يمكن تعبئتها جماعياً
    protected $fillable = [
        'code',
        'barcode',
        'name',
        'description',
        'category_id',
        'unit_id',
        'min_stock',
        'max_stock',
        'reorder_level',
        'cost_price',
        'selling_price',
        'is_active',
        'image',
    ];

    // تحويل أنواع البيانات
    protected $casts = [
        'is_active' => 'boolean',
        'cost_price' => 'float',
        'selling_price' => 'float',
    ];

    /**
     * العلاقة: الصنف ينتمي إلى فئة واحدة.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        // نفترض وجود نموذج Category
        return $this->belongsTo(Category::class);
    }

    /**
     * العلاقة: الصنف يستخدم وحدة قياس واحدة.
     *
     * @return BelongsTo
     */
    public function unit(): BelongsTo
    {
        // نفترض وجود نموذج Unit
        return $this->belongsTo(Unit::class);
    }
}
