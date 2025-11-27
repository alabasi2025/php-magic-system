<?php

namespace App\Models\Cashiers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Cashier",
 *     title="Cashier",
 *     description="نموذج الصراف (Cashier) في نظام الصرافين.",
 *     @OA\Property(property="id", type="integer", readOnly="true", description="معرف الصراف"),
 *     @OA\Property(property="user_id", type="integer", description="معرف المستخدم المرتبط بالصراف"),
 *     @OA\Property(property="branch_id", type="integer", description="معرف الفرع الذي يعمل به الصراف"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive", "on_leave"}, description="حالة الصراف"),
 *     @OA\Property(property="last_shift_start", type="string", format="date-time", nullable="true", description="وقت بدء آخر وردية"),
 *     @OA\Property(property="last_shift_end", type="string", format="date-time", nullable="true", description="وقت انتهاء آخر وردية"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="تاريخ الإنشاء"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="تاريخ آخر تحديث"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable="true", description="تاريخ الحذف الناعم")
 * )
 */
class Cashier extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * اسم الجدول المرتبط بالنموذج.
     *
     * @var string
     */
    protected $table = 'cashiers';

    /**
     * السمات التي يمكن تعبئتها بشكل جماعي.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'branch_id',
        'status',
        'last_shift_start',
        'last_shift_end',
    ];

    /**
     * السمات التي يجب تحويلها إلى أنواع أصلية.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_shift_start' => 'datetime',
        'last_shift_end' => 'datetime',
    ];

    /**
     * السمات التي يجب إخفاؤها عند تحويل النموذج إلى مصفوفة.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'password',
    ];

    // --------------------------------------------------------------------------
    //  RELATIONSHIPS
    // --------------------------------------------------------------------------

    /**
     * العلاقة مع نموذج المستخدم (User).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // نفترض وجود نموذج User في المسار الافتراضي
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * العلاقة مع نموذج الفرع (Branch).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        // نفترض وجود نموذج Branch في مسار الجينات المناسب (مثال: Locations)
        // يجب تعديل المسار إذا كان مختلفًا
        return $this->belongsTo(\App\Models\Locations\Branch::class, 'branch_id');
    }

    /**
     * العلاقة مع سجلات الوردية (Shifts).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shifts()
    {
        // نفترض وجود نموذج CashierShift في نفس مسار الجينات
        return $this->hasMany(CashierShift::class, 'cashier_id');
    }

    // --------------------------------------------------------------------------
    //  SCOPES
    // --------------------------------------------------------------------------

    /**
     * نطاق لاسترداد الصرافين النشطين فقط.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // --------------------------------------------------------------------------
    //  BUSINESS LOGIC METHODS
    // --------------------------------------------------------------------------

    /**
     * التحقق مما إذا كان الصراف في وردية حالية.
     *
     * @return bool
     */
    public function isOnShift(): bool
    {
        return $this->status === 'active' && $this->last_shift_start !== null && $this->last_shift_end === null;
    }
}