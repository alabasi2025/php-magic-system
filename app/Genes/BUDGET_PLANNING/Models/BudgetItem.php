<?php

declare(strict_types=1);

namespace App\Genes\BUDGET_PLANNING\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * نموذج BudgetItem (بند الميزانية).
 *
 * يمثل بنداً محدداً ضمن ميزانية معينة (Budget).
 *
 * @package App\Genes\BUDGET_PLANNING\Models
 *
 * @property int $id المعرف الفريد لبند الميزانية.
 * @property int $budget_id المعرف الفريد للميزانية الأم.
 * @property string $description وصف البند.
 * @property float $amount المبلغ المخصص للبند.
 * @property \Illuminate\Support\Carbon $created_at تاريخ الإنشاء.
 * @property \Illuminate\Support\Carbon $updated_at تاريخ آخر تحديث.
 *
 * @property-read \App\Genes\BUDGET_PLANNING\Models\Budget $budget الميزانية الأم التي ينتمي إليها البند.
 */
class BudgetItem extends Model
{
    // اسم الجدول المرتبط بالنموذج في قاعدة البيانات
    protected $table = 'budget_items';

    // الأعمدة التي يمكن تعبئتها جماعياً (Mass Assignable)
    protected $fillable = [
        'budget_id',
        'description',
        'amount',
    ];

    // تحويل أنواع الأعمدة (Casting) لضمان التعامل الصحيح مع البيانات
    protected $casts = [
        'amount' => 'float',
    ];

    /**
     * علاقة: ينتمي إلى (Budget).
     *
     * كل بند ميزانية (BudgetItem) ينتمي إلى ميزانية واحدة (Budget).
     *
     * @return BelongsTo
     */
    public function budget(): BelongsTo
    {
        // يفترض أن نموذج الميزانية (Budget) موجود في نفس المسار
        return $this->belongsTo(Budget::class);
    }

    /**
     * خاصية وصول وتعديل (Accessor/Mutator) حديثة لقيمة المبلغ.
     *
     * تستخدم Attribute::make لتعريف getter و setter بطريقة Laravel الحديثة (PHP 8.4).
     *
     * @return Attribute
     */
    protected function amount(): Attribute
    {
        return Attribute::make(
            // عند جلب القيمة من قاعدة البيانات (Getter)
            get: fn (float $value): float => $value,

            // عند تعيين القيمة قبل الحفظ في قاعدة البيانات (Setter)
            // يتم تقريب القيمة لضمان دقة عشرية مناسبة للعمليات المالية
            set: fn (float $value): float => round($value, 2),
        );
    }
}
