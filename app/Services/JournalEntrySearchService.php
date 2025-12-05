<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\SavedSearch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * خدمة البحث المتقدم عن القيود اليومية.
 */
class JournalEntrySearchService
{
    /**
     * ينفذ عملية البحث بناءً على معايير محددة.
     *
     * @param array $criteria معايير البحث (عادةً من طلب HTTP).
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search(array $criteria)
    {
        $query = $this->buildQuery($criteria);

        // إرجاع النتائج مع ترقيم الصفحات
        return $query->paginate(20);
    }

    /**
     * يبني استعلام Eloquent بناءً على معايير البحث.
     *
     * @param array $criteria معايير البحث.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildQuery(array $criteria): Builder
    {
        // نفترض وجود نموذج JournalEntry
        $query = JournalEntry::query();

        // 1. بحث برقم القيد
        if (!empty($criteria['entry_number'])) {
            $query->where('entry_number', 'like', '%' . $criteria['entry_number'] . '%');
        }

        // 2. بحث بالتاريخ (من - إلى)
        if (!empty($criteria['date_from'])) {
            $query->whereDate('entry_date', '>=', $criteria['date_from']);
        }
        if (!empty($criteria['date_to'])) {
            $query->whereDate('entry_date', '<=', $criteria['date_to']);
        }

        // 3. بحث بالحساب (مدين أو دائن) - يتطلب علاقة مع تفاصيل القيد
        // نفترض وجود علاقة 'details' مع جدول journal_entry_details
        if (!empty($criteria['account_id'])) {
            $query->whereHas('details', function (Builder $detailQuery) use ($criteria) {
                $detailQuery->where('account_id', $criteria['account_id']);
                // يمكن إضافة فلاتر للمبلغ هنا إذا كان مطلوباً
            });
        }

        // 4. بحث بالمبلغ (من - إلى) - يتطلب تجميع أو بحث في التفاصيل
        // سنفترض هنا البحث عن القيود التي تحتوي على تفاصيل بمبالغ ضمن النطاق
        if (!empty($criteria['amount_min']) || !empty($criteria['amount_max'])) {
            $query->whereHas('details', function (Builder $detailQuery) use ($criteria) {
                if (!empty($criteria['amount_min'])) {
                    // البحث عن أي تفصيل (مدين أو دائن) أكبر من أو يساوي الحد الأدنى
                    $detailQuery->where(function ($q) use ($criteria) {
                        $q->where('debit', '>=', $criteria['amount_min'])
                          ->orWhere('credit', '>=', $criteria['amount_min']);
                    });
                }
                if (!empty($criteria['amount_max'])) {
                    // البحث عن أي تفصيل (مدين أو دائن) أصغر من أو يساوي الحد الأقصى
                    $detailQuery->where(function ($q) use ($criteria) {
                        $q->where('debit', '<=', $criteria['amount_max'])
                          ->orWhere('credit', '<=', $criteria['amount_max']);
                    });
                }
            });
        }

        // 5. بحث بالحالة (pending, approved, rejected)
        if (!empty($criteria['status']) && is_array($criteria['status'])) {
            $query->whereIn('status', $criteria['status']);
        } elseif (!empty($criteria['status']) && is_string($criteria['status'])) {
             $query->where('status', $criteria['status']);
        }

        // 6. بحث بالمستخدم (من أنشأ القيد)
        if (!empty($criteria['user_id'])) {
            $query->where('user_id', $criteria['user_id']);
        }

        // ترتيب النتائج
        $query->orderBy('entry_date', 'desc')->orderBy('entry_number', 'desc');

        return $query;
    }

    /**
     * يحفظ معايير البحث الحالية للمستخدم.
     *
     * @param string $name اسم البحث المحفوظ.
     * @param array $criteria معايير البحث.
     * @return \App\Models\SavedSearch
     */
    public function saveSearch(string $name, array $criteria): SavedSearch
    {
        // تنظيف المعايير لإزالة القيم الفارغة قبل الحفظ
        $filteredCriteria = array_filter($criteria, function ($value) {
            return !is_null($value) && $value !== '';
        });

        return SavedSearch::create([
            'user_id' => Auth::id(),
            'name' => $name,
            'criteria' => $filteredCriteria,
        ]);
    }

    /**
     * يجلب جميع عمليات البحث المحفوظة للمستخدم الحالي.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSavedSearches()
    {
        return SavedSearch::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();
    }
}
