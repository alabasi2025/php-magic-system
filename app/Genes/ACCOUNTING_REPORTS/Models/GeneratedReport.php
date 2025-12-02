<?php

// app/Genes/ACCOUNTING_REPORTS/Models/GeneratedReport.php

declare(strict_types=1);

namespace App\Genes\ACCOUNTING_REPORTS\Models;

use App\Models\User; // افتراض أن موديل المستخدم في هذا المسار
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * موديل يمثل التقارير المحاسبية التي تم توليدها فعلياً.
 *
 * يتبع معايير Laravel الحديثة (v12) ويستخدم ميزات PHP 8.4.
 *
 * @property int $id
 * @property int $report_template_id معرف القالب الذي تم توليد التقرير منه
 * @property int $user_id معرف المستخدم الذي قام بتوليد التقرير
 * @property string $status حالة التقرير (مثل: pending, generating, completed, failed)
 * @property string $file_path المسار الفعلي لملف التقرير المُولد
 * @property \Illuminate\Support\Carbon|null $generated_at تاريخ ووقت الانتهاء من توليد التقرير
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read \App\Genes\ACCOUNTING_REPORTS\Models\ReportTemplate $reportTemplate
 * @property-read \App\Models\User $user
 */
class GeneratedReport extends Model
{
    use HasFactory;

    /**
     * اسم الجدول المرتبط بالموديل.
     *
     * @var string
     */
    protected $table = 'accounting_generated_reports';

    /**
     * الأعمدة التي يمكن تعبئتها جماعياً (Mass Assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'report_template_id',
        'user_id',
        'status',
        'file_path',
        'generated_at',
    ];

    /**
     * تحويل أنواع الأعمدة (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'generated_at' => 'datetime',
        'report_template_id' => 'integer',
        'user_id' => 'integer',
    ];

    // --------------------------------------------------------------------------
    // العلاقات (Relationships)
    // --------------------------------------------------------------------------

    /**
     * العلاقة: التقرير المُولد ينتمي إلى قالب تقرير واحد.
     *
     * @return BelongsTo
     */
    public function reportTemplate(): BelongsTo
    {
        // افتراض أن موديل ReportTemplate موجود في نفس المجلد
        return $this->belongsTo(ReportTemplate::class);
    }

    /**
     * العلاقة: التقرير المُولد ينتمي إلى مستخدم واحد (الذي قام بتوليده).
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        // يستخدم موديل المستخدم القياسي في Laravel
        return $this->belongsTo(User::class);
    }
}
