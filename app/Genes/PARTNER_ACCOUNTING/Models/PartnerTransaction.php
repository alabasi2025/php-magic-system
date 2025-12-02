<?php

namespace App\Genes\PARTNER_ACCOUNTING\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Partner; // افتراض مسار عام لنموذج الشريك

class PartnerTransaction extends Model
{
    /**
     * العلاقة: ينتمي إلى شريك.
     *
     * @return BelongsTo
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * نطاق الاستعلام للمعاملات التي تمثل إيداعات.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeDeposits(Builder $query): Builder
    {
        // افتراض أن هناك عمود 'type' وأن 'deposit' يمثل الإيداع
        return $query->where('type', 'deposit');
    }

    /**
     * نطاق الاستعلام للمعاملات التي تمثل سحوبات.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithdrawals(Builder $query): Builder
    {
        // افتراض أن 'withdrawal' يمثل السحب
        return $query->where('type', 'withdrawal');
    }

    /**
     * نطاق الاستعلام للمعاملات التي تمثل أرباحًا.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeProfits(Builder $query): Builder
    {
        // افتراض أن 'profit' يمثل الربح
        return $query->where('type', 'profit');
    }

    /**
     * نطاق الاستعلام للمعاملات التي تمثل خسائر.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeLosses(Builder $query): Builder
    {
        // افتراض أن 'loss' يمثل الخسارة
        return $query->where('type', 'loss');
    }

    /**
     * نطاق الاستعلام للمعاملات بين تاريخين محددين.
     *
     * @param Builder $query
     * @param string $startDate
     * @param string $endDate
     * @return Builder
     */
    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
