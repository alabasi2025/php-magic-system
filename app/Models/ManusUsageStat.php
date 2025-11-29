<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * @brief نموذج ManusUsageStat
 *
 * يمثل هذا النموذج إحصائيات استخدام Manus API.
 * يحتوي على طرق ثابتة لإدارة وتجميع الإحصائيات.
 *
 * @package App\Models
 */
class ManusUsageStat extends Model
{
    /**
     * @var string اسم الجدول المرتبط بالنموذج.
     */
    protected $table = 'manus_usage_stats';

    /**
     * @var array الحقول التي يمكن تعبئتها بشكل جماعي.
     */
    protected $fillable = [
        'user_id',
        'endpoint',
        'cost',
        'tokens',
        'usage_date',
    ];

    /**
     * @var array تحويلات أنواع البيانات.
     */
    protected $casts = [
        'cost' => 'float',
        'tokens' => 'integer',
        'usage_date' => 'date',
    ];

    /**
     * @var bool تعطيل الطوابع الزمنية الافتراضية (created_at, updated_at).
     */
    public $timestamps = false;

    // -------------------------------------------------------------------------
    // العلاقات (Relationships)
    // -------------------------------------------------------------------------

    /**
     * @brief العلاقة مع نموذج المستخدم (User).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // نفترض أن نموذج المستخدم هو App\Models\User
        // يجب التأكد من وجود نموذج User في مسار App\Models
        return $this->belongsTo(\App\Models\User::class);
    }

    // -------------------------------------------------------------------------
    // الطرق الثابتة (Static Methods)
    // -------------------------------------------------------------------------

    /**
     * @brief تحديث أو إنشاء إحصائية استخدام جديدة.
     *
     * تقوم هذه الطريقة بتحديث الإحصائيات الحالية لنقطة نهاية معينة وتاريخ معين،
     * أو إنشاء سجل جديد إذا لم يكن موجودًا.
     *
     * @param int $userId معرف المستخدم.
     * @param string $endpoint نقطة نهاية Manus API المستخدمة (مثل 'text_generation').
     * @param float $cost التكلفة المضافة لهذه العملية.
     * @param int $tokens عدد التوكنات المستخدمة.
     * @param \DateTimeInterface|string|null $date تاريخ الاستخدام.
     * @return self
     * @throws \RuntimeException إذا فشلت عملية التحديث.
     */
    public static function updateStats(int $userId, string $endpoint, float $cost, int $tokens, $date = null): self
    {
        try {
            // تحويل التاريخ إلى صيغة قاعدة البيانات (YYYY-MM-DD)
            $usageDate = $date ? Carbon::parse($date)->toDateString() : Carbon::today()->toDateString();

            // استخدام المعاملات لتجنب مشاكل التزامن عند التحديث
            return DB::transaction(function () use ($userId, $endpoint, $cost, $tokens, $usageDate) {
                // البحث عن سجل موجود أو إنشاء سجل جديد
                $stat = self::firstOrNew([
                    'user_id' => $userId,
                    'endpoint' => $endpoint,
                    'usage_date' => $usageDate,
                ]);

                // تحديث القيم
                $stat->cost += $cost;
                $stat->tokens += $tokens;

                // حفظ التغييرات
                $stat->save();

                return $stat;
            });
        } catch (\Exception $e) {
            // معالجة الأخطاء وتسجيلها
            \Log::error("فشل في تحديث إحصائيات Manus: " . $e->getMessage(), [
                'user_id' => $userId,
                'endpoint' => $endpoint,
                'cost' => $cost,
                'tokens' => $tokens,
                'date' => $date,
            ]);
            // رمي استثناء لتنبيه النظام بفشل العملية
            throw new \RuntimeException("تعذر تحديث إحصائيات Manus. السبب: " . $e->getMessage());
        }
    }

    /**
     * @brief جلب إحصائيات الاستخدام لفترة زمنية محددة.
     *
     * تقوم بتجميع الإحصائيات (التكلفة والتوكنات) حسب المستخدم ونقطة النهاية والتاريخ.
     *
     * @param \DateTimeInterface|string $startDate تاريخ البدء.
     * @param \DateTimeInterface|string $endDate تاريخ الانتهاء.
     * @param int|null $userId معرف المستخدم لتصفية النتائج (اختياري).
     * @return \Illuminate\Support\Collection مجموعة من الإحصائيات المجمعة.
     */
    public static function getStatsForPeriod($startDate, $endDate, ?int $userId = null)
    {
        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $query = self::query()
                ->select(
                    DB::raw('SUM(cost) as total_cost'),
                    DB::raw('SUM(tokens) as total_tokens'),
                    'user_id',
                    'endpoint',
                    DB::raw('DATE(usage_date) as date')
                )
                ->whereBetween('usage_date', [$start, $end]);

            if ($userId !== null) {
                $query->where('user_id', $userId);
            }

            // تجميع النتائج حسب المستخدم ونقطة النهاية والتاريخ
            return $query->groupBy('user_id', 'endpoint', DB::raw('DATE(usage_date)'))
                ->orderBy('date')
                ->get();
        } catch (\Exception $e) {
            \Log::error("فشل في جلب إحصائيات Manus للفترة: " . $e->getMessage(), [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_id' => $userId,
            ]);
            return collect(); // إرجاع مجموعة فارغة عند الفشل
        }
    }

    /**
     * @brief حساب إجمالي التكلفة لفترة زمنية محددة.
     *
     * @param \DateTimeInterface|string $startDate تاريخ البدء.
     * @param \DateTimeInterface|string $endDate تاريخ الانتهاء.
     * @param int|null $userId معرف المستخدم لتصفية النتائج (اختياري).
     * @return float إجمالي التكلفة.
     */
    public static function getTotalCost($startDate, $endDate, ?int $userId = null): float
    {
        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $query = self::query()
                ->whereBetween('usage_date', [$start, $end]);

            if ($userId !== null) {
                $query->where('user_id', $userId);
            }

            // استخدام sum() لجلب الإجمالي
            return (float) $query->sum('cost');
        } catch (\Exception $e) {
            \Log::error("فشل في حساب إجمالي تكلفة Manus: " . $e->getMessage(), [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_id' => $userId,
            ]);
            return 0.0;
        }
    }

    /**
     * @brief حساب إجمالي عدد التوكنات لفترة زمنية محددة.
     *
     * @param \DateTimeInterface|string $startDate تاريخ البدء.
     * @param \DateTimeInterface|string $endDate تاريخ الانتهاء.
     * @param int|null $userId معرف المستخدم لتصفية النتائج (اختياري).
     * @return int إجمالي عدد التوكنات.
     */
    public static function getTotalTokens($startDate, $endDate, ?int $userId = null): int
    {
        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $query = self::query()
                ->whereBetween('usage_date', [$start, $end]);

            if ($userId !== null) {
                $query->where('user_id', $userId);
            }

            // استخدام sum() لجلب الإجمالي
            return (int) $query->sum('tokens');
        } catch (\Exception $e) {
            \Log::error("فشل في حساب إجمالي توكنات Manus: " . $e->getMessage(), [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_id' => $userId,
            ]);
            return 0;
        }
    }
}