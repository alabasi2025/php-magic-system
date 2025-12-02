<?php

namespace App\Genes\ACCOUNTING_REPORTS\Services;

use App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateTransaction;
use App\Genes\INTERMEDIATE_ACCOUNTS\Models\TransactionLink;
use App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateAccount;
use App\Genes\ACCOUNTING_REPORTS\Models\GeneratedReport;
use App\Genes\ACCOUNTING_REPORTS\Models\ReportTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * ReportService
 * 
 * خدمة لإدارة التقارير المحاسبية والرقابية.
 * تتضمن دوال لتوليد التقارير، الكشف عن العمليات غير المربوطة،
 * العمليات غير الموجهة، والعمليات غير المطابقة.
 * 
 * @version 1.0.0
 * @since 2025-12-02
 */
class ReportService
{
    /**
     * الحصول على العمليات غير المربوطة.
     * 
     * العمليات التي لم يتم ربطها بعمليات أخرى (قيد مزدوج).
     *
     * @param int|null $accountId معرف الحساب الوسيط (اختياري).
     * @param string|null $startDate تاريخ البداية (اختياري).
     * @param string|null $endDate تاريخ النهاية (اختياري).
     * @return Collection مجموعة من العمليات غير المربوطة.
     */
    public function getUnlinkedTransactions(
        ?int $accountId = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {
        $query = IntermediateTransaction::query()
            ->whereDoesntHave('sourceLinks')
            ->whereDoesntHave('targetLinks')
            ->where('status', '!=', 'cancelled')
            ->with('intermediateAccount');
        
        if ($accountId) {
            $query->where('intermediate_account_id', $accountId);
        }
        
        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }
        
        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * الحصول على العمليات غير الموجهة.
     * 
     * العمليات التي لم يتم تحديد حساب وسيط لها.
     *
     * @param string|null $startDate تاريخ البداية (اختياري).
     * @param string|null $endDate تاريخ النهاية (اختياري).
     * @return Collection مجموعة من العمليات غير الموجهة.
     */
    public function getUnallocatedTransactions(
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {
        $query = IntermediateTransaction::query()
            ->whereNull('intermediate_account_id')
            ->where('status', '!=', 'cancelled');
        
        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }
        
        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * الحصول على العمليات غير المطابقة.
     * 
     * العمليات التي تم ربطها لكن المبالغ غير متطابقة.
     *
     * @param int|null $accountId معرف الحساب الوسيط (اختياري).
     * @param string|null $startDate تاريخ البداية (اختياري).
     * @param string|null $endDate تاريخ النهاية (اختياري).
     * @return array مصفوفة من العمليات غير المطابقة.
     */
    public function getMismatchedTransactions(
        ?int $accountId = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $query = IntermediateTransaction::query()
            ->where(function($q) {
                $q->whereHas('sourceLinks')
                  ->orWhereHas('targetLinks');
            })
            ->where('status', '!=', 'cancelled')
            ->with(['sourceLinks', 'targetLinks', 'intermediateAccount']);
        
        if ($accountId) {
            $query->where('intermediate_account_id', $accountId);
        }
        
        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }
        
        $transactions = $query->get();
        
        $mismatched = [];
        
        foreach ($transactions as $transaction) {
            $linkedAmount = $transaction->sourceLinks->sum('amount') 
                          + $transaction->targetLinks->sum('amount');
            
            // إذا كان المبلغ المربوط لا يساوي مبلغ العملية
            if ($linkedAmount != $transaction->amount) {
                $mismatched[] = [
                    'transaction' => $transaction,
                    'transaction_amount' => $transaction->amount,
                    'linked_amount' => $linkedAmount,
                    'difference' => $transaction->amount - $linkedAmount,
                    'percentage' => ($linkedAmount / $transaction->amount) * 100,
                ];
            }
        }
        
        return $mismatched;
    }

    /**
     * الحصول على العمليات المعلقة.
     * 
     * العمليات التي حالتها pending.
     *
     * @param int|null $accountId معرف الحساب الوسيط (اختياري).
     * @return Collection مجموعة من العمليات المعلقة.
     */
    public function getPendingTransactions(?int $accountId = null): Collection
    {
        $query = IntermediateTransaction::query()
            ->where('status', 'pending')
            ->with('intermediateAccount');
        
        if ($accountId) {
            $query->where('intermediate_account_id', $accountId);
        }
        
        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * توليد تقرير شامل للحساب الوسيط.
     *
     * @param int $accountId معرف الحساب الوسيط.
     * @param string $startDate تاريخ البداية.
     * @param string $endDate تاريخ النهاية.
     * @return array التقرير الشامل.
     * @throws Exception في حالة عدم العثور على الحساب
     */
    public function generateAccountReport(
        int $accountId,
        string $startDate,
        string $endDate
    ): array {
        $account = IntermediateAccount::findOrFail($accountId);
        
        // الحصول على جميع العمليات في الفترة
        $transactions = IntermediateTransaction::where('intermediate_account_id', $accountId)
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->orderBy('transaction_date')
            ->get();
        
        // حساب الإحصائيات
        $totalReceipts = $transactions->where('type', 'receipt')
            ->where('status', 'completed')
            ->sum('amount');
        
        $totalPayments = $transactions->where('type', 'payment')
            ->where('status', 'completed')
            ->sum('amount');
        
        $pendingReceipts = $transactions->where('type', 'receipt')
            ->where('status', 'pending')
            ->sum('amount');
        
        $pendingPayments = $transactions->where('type', 'payment')
            ->where('status', 'pending')
            ->sum('amount');
        
        // حساب الرصيد الافتتاحي (قبل تاريخ البداية)
        $openingBalance = $this->calculateOpeningBalance($accountId, $startDate);
        
        // حساب الرصيد الختامي
        $closingBalance = $openingBalance + $totalReceipts - $totalPayments;
        
        return [
            'account' => $account,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'balances' => [
                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance,
                'net_change' => $totalReceipts - $totalPayments,
            ],
            'completed' => [
                'total_receipts' => $totalReceipts,
                'total_payments' => $totalPayments,
                'receipts_count' => $transactions->where('type', 'receipt')->where('status', 'completed')->count(),
                'payments_count' => $transactions->where('type', 'payment')->where('status', 'completed')->count(),
            ],
            'pending' => [
                'pending_receipts' => $pendingReceipts,
                'pending_payments' => $pendingPayments,
                'receipts_count' => $transactions->where('type', 'receipt')->where('status', 'pending')->count(),
                'payments_count' => $transactions->where('type', 'payment')->where('status', 'pending')->count(),
            ],
            'transactions' => $transactions,
        ];
    }

    /**
     * حساب الرصيد الافتتاحي للحساب.
     *
     * @param int $accountId معرف الحساب الوسيط.
     * @param string $beforeDate قبل هذا التاريخ.
     * @return float الرصيد الافتتاحي.
     */
    protected function calculateOpeningBalance(int $accountId, string $beforeDate): float
    {
        $receipts = IntermediateTransaction::where('intermediate_account_id', $accountId)
            ->where('type', 'receipt')
            ->where('status', 'completed')
            ->whereDate('transaction_date', '<', $beforeDate)
            ->sum('amount');
        
        $payments = IntermediateTransaction::where('intermediate_account_id', $accountId)
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->whereDate('transaction_date', '<', $beforeDate)
            ->sum('amount');
        
        return (float) ($receipts - $payments);
    }

    /**
     * توليد تقرير مقارنة بين حسابين.
     *
     * @param int $account1Id معرف الحساب الأول.
     * @param int $account2Id معرف الحساب الثاني.
     * @param string $startDate تاريخ البداية.
     * @param string $endDate تاريخ النهاية.
     * @return array تقرير المقارنة.
     */
    public function compareAccounts(
        int $account1Id,
        int $account2Id,
        string $startDate,
        string $endDate
    ): array {
        $account1Report = $this->generateAccountReport($account1Id, $startDate, $endDate);
        $account2Report = $this->generateAccountReport($account2Id, $startDate, $endDate);
        
        return [
            'account1' => $account1Report,
            'account2' => $account2Report,
            'comparison' => [
                'receipts_difference' => $account1Report['completed']['total_receipts'] - $account2Report['completed']['total_receipts'],
                'payments_difference' => $account1Report['completed']['total_payments'] - $account2Report['completed']['total_payments'],
                'balance_difference' => $account1Report['balances']['closing_balance'] - $account2Report['balances']['closing_balance'],
            ],
        ];
    }

    /**
     * حفظ تقرير مُنشأ.
     *
     * @param string $type نوع التقرير.
     * @param array $data بيانات التقرير.
     * @param int|null $templateId معرف قالب التقرير (اختياري).
     * @return GeneratedReport التقرير المحفوظ.
     * @throws Exception في حالة فشل الحفظ
     */
    public function saveGeneratedReport(
        string $type,
        array $data,
        ?int $templateId = null
    ): GeneratedReport {
        try {
            DB::beginTransaction();
            
            $report = GeneratedReport::create([
                'report_template_id' => $templateId,
                'type' => $type,
                'title' => $data['title'] ?? "تقرير {$type}",
                'data' => json_encode($data),
                'generated_at' => now(),
                'generated_by' => auth()->id() ?? 1,
            ]);
            
            DB::commit();
            return $report;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * الحصول على ملخص يومي للحسابات الوسيطة.
     *
     * @param string $date التاريخ.
     * @return array الملخص اليومي.
     */
    public function getDailySummary(string $date): array
    {
        $transactions = IntermediateTransaction::whereDate('transaction_date', $date)
            ->with('intermediateAccount')
            ->get();
        
        $summary = [
            'date' => $date,
            'total_receipts' => $transactions->where('type', 'receipt')->sum('amount'),
            'total_payments' => $transactions->where('type', 'payment')->sum('amount'),
            'receipts_count' => $transactions->where('type', 'receipt')->count(),
            'payments_count' => $transactions->where('type', 'payment')->count(),
            'pending_count' => $transactions->where('status', 'pending')->count(),
            'completed_count' => $transactions->where('status', 'completed')->count(),
            'cancelled_count' => $transactions->where('status', 'cancelled')->count(),
            'by_account' => [],
        ];
        
        // تجميع حسب الحساب
        $byAccount = $transactions->groupBy('intermediate_account_id');
        
        foreach ($byAccount as $accountId => $accountTransactions) {
            $account = $accountTransactions->first()->intermediateAccount;
            
            $summary['by_account'][] = [
                'account_id' => $accountId,
                'account_name' => $account->name ?? 'غير محدد',
                'receipts' => $accountTransactions->where('type', 'receipt')->sum('amount'),
                'payments' => $accountTransactions->where('type', 'payment')->sum('amount'),
                'transactions_count' => $accountTransactions->count(),
            ];
        }
        
        return $summary;
    }
}
