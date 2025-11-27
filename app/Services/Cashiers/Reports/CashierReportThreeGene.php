<?php

namespace App\Services\Cashiers\Reports;

use App\Services\BaseGene;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class CashierReportThreeGene
 * @package App\Services\Cashiers\Reports
 *
 * Task 2093: [نظام الصرافين (Cashiers)] Reports - نظام الصرافين (Cashiers) - Reports - Task 3
 *
 * هذا الجين مسؤول عن إنشاء التقرير الثالث لنظام الصرافين.
 * يفترض أن هذا التقرير يعرض إجمالي المبيعات أو المعاملات لكل صراف خلال فترة زمنية محددة،
 * مع تفصيل حسب نوع المعاملة أو حالة الدفع.
 */
class CashierReportThreeGene extends BaseGene
{
    /**
     * تنفيذ منطق التقرير.
     *
     * @param array $filters مصفوفة الفلاتر (مثل 'start_date', 'end_date', 'cashier_id', 'transaction_type').
     * @return array بيانات التقرير.
     */
    public function execute(array $filters): array
    {
        // تحديد التواريخ الافتراضية إذا لم يتم تمريرها
        $startDate = Carbon::parse($filters['start_date'] ?? Carbon::now()->startOfMonth())->startOfDay();
        $endDate = Carbon::parse($filters['end_date'] ?? Carbon::now()->endOfMonth())->endOfDay();

        // استعلام قاعدة البيانات لجمع بيانات التقرير
        $reportData = DB::table('cashier_transactions') // يفترض وجود جدول باسم cashier_transactions
            ->select(
                'cashier_id',
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(amount) as total_amount'),
                'transaction_type' // يفترض وجود عمود لتحديد نوع المعاملة
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when(isset($filters['cashier_id']), function ($query) use ($filters) {
                return $query->where('cashier_id', $filters['cashier_id']);
            })
            ->when(isset($filters['transaction_type']), function ($query) use ($filters) {
                return $query->where('transaction_type', $filters['transaction_type']);
            })
            ->groupBy('cashier_id', 'transaction_type')
            ->orderBy('cashier_id')
            ->get();

        // تجميع البيانات في هيكل تقرير منظم
        $groupedData = $reportData->groupBy('cashier_id')->map(function ($transactions, $cashierId) {
            $cashierName = DB::table('users')->where('id', $cashierId)->value('name') ?? 'Cashier ' . $cashierId; // جلب اسم الصراف
            $summary = [
                'cashier_id' => $cashierId,
                'cashier_name' => $cashierName,
                'total_transactions_all' => $transactions->sum('total_transactions'),
                'total_amount_all' => $transactions->sum('total_amount'),
                'details' => $transactions->map(function ($transaction) {
                    return [
                        'type' => $transaction->transaction_type,
                        'count' => $transaction->total_transactions,
                        'amount' => $transaction->total_amount,
                    ];
                })->values()->toArray(),
            ];
            return $summary;
        })->values()->toArray();

        return [
            'report_title' => 'تقرير إجمالي المعاملات حسب الصراف ونوع المعاملة (التقرير الثالث)',
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
            'data' => $groupedData,
            'metadata' => [
                'generated_at' => Carbon::now()->toDateTimeString(),
                'filters_applied' => $filters,
            ]
        ];
    }
}