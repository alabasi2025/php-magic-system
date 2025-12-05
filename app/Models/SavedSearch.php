<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * نموذج يمثل عملية بحث محفوظة.
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property array $criteria
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class SavedSearch extends Model
{
    use HasFactory;

    // اسم الجدول في قاعدة البيانات
    protected $table = 'saved_searches';

    // الحقول التي يمكن تعبئتها جماعياً
    protected $fillable = [
        'user_id',
        'name',
        'criteria',
    ];

    // تحويل حقل المعايير (criteria) إلى مصفوفة تلقائياً
    protected $casts = [
        'criteria' => 'array',
    ];

    /**
     * علاقة البحث المحفوظ بالمستخدم الذي أنشأه.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // نفترض وجود نموذج User
        return $this->belongsTo(User::class);
    }
}
