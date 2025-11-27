<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Services;

use App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateAccount;
use App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateTransaction;
use App\Genes\INTERMEDIATE_ACCOUNTS\Models\TransactionLink;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Service: ReportService
 * 
 * 💡 الفكرة:
 * خدمة تولد التقارير والإحصائيات الخاصة بالحسابات الوسيطة.
 * 
 * 🎯 أنواع التقارير:
 * 1. تقرير العمليات المعلقة (Unlinked Transactions Report)
 * 2. تقرير الأرصدة (Balance Report)
 * 3. تقرير الحركة (Movement Report)
 * 4. تقرير الربط (Linking Report)
 * 5. تقرير الأداء (Performance Report)
 * 
 * @version 1.0.0
 * @since 2025-11-27
 */
class ReportService
{
    /**
     * Generate unlinked transactions report.
     *
     * @param int|null $intermediateAccountId
     * @param array $filters
     * @return array
     */
    public function getUnlinkedTransactionsReport(?int $intermediateAccountId = null, array $filters = []): array
    {
        $query = IntermediateTransaction::with(['intermediateAccount.mainAccount'])
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled');
        
        if ($intermediateAccountId) {
            $query->where('intermediate_account_id', $intermediateAccountId);
        }
        
        // Apply filters
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (isset($filters['date_from'])) {
            $query->where('transaction_date', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->where('transaction_date', '<=', $filters['date_to']);
        }
        
        $transactions = $query->orderBy('transaction_date', 'desc')->get();
        
        $summary = [
            'total_count' => $transactions->count(),
            'total_receipts_count' => $transactions->where('type', 'receipt')->count(),
            'total_payments_count' => $transactions->where('type', 'payment')->count(),
            'total_receipts_amount' => $transactions->where('type', 'receipt')->sum(function ($t) {
                return $t->getAvailableAmount();
            }),
            'total_payments_amount' => $transactions->where('type', 'payment')->sum(function ($t) {
                return $t->getAvailableAmount();
            }),
        ];
        
        return [
            'transactions' => $transactions,
            'summary' => $summary,
        ];
    }

    /**
     * Generate balance report for all intermediate accounts.
     *
     * @param array $filters
     * @return array
     */
    public function getBalanceReport(array $filters = []): array
    {
        $query = IntermediateAccount::with(['mainAccount', 'intermediateAccount1', 'transactions']);
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        $accounts = $query->get();
        
        $report = [];
        $totalBalance = 0;
        $unbalancedCount = 0;
        
        foreach ($accounts as $account) {
            $balance = $account->getCurrentBalance();
            $isBalanced = $account->isBalanced();
            
            if (!$isBalanced) {
                $unbalancedCount++;
            }
            
            $totalBalance += $balance;
            
            $report[] = [
                'account_id' => $account->id,
                'main_account_name' => $account->mainAccount->name ?? 'غير معروف',
                'intermediate_account_name' => $account->intermediateAccount1->name ?? 'غير معروف',
                'total_receipts' => $account->getTotalReceipts(),
                'total_payments' => $account->getTotalPayments(),
                'current_balance' => $balance,
                'unlinked_receipts' => $account->getUnlinkedAmount('receipt'),
                'unlinked_payments' => $account->getUnlinkedAmount('payment'),
                'unlinked_count' => $account->getUnlinkedTransactionsCount(),
                'is_balanced' => $isBalanced,
                'status' => $account->status,
            ];
        }
        
        return [
            'accounts' => $report,
            'summary' => [
                'total_accounts' => count($report),
                'balanced_accounts' => count($report) - $unbalancedCount,
                'unbalanced_accounts' => $unbalancedCount,
                'total_balance' => $totalBalance,
            ],
        ];
    }

    /**
     * Generate movement report for an intermediate account.
     *
     * @param int $intermediateAccountId
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getMovementReport(int $intermediateAccountId, string $dateFrom, string $dateTo): array
    {
        $intermediateAccount = IntermediateAccount::findOrFail($intermediateAccountId);
        
        $transactions = IntermediateTransaction::where('intermediate_account_id', $intermediateAccountId)
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->orderBy('transaction_date', 'asc')
            ->get();
        
        $movements = [];
        $balance = 0;
        
        foreach ($transactions as $transaction) {
            if ($transaction->type === 'receipt') {
                $balance += $transaction->amount;
            } else {
                $balance -= $transaction->amount;
            }
            
            $movements[] = [
                'date' => $transaction->transaction_date,
                'type' => $transaction->type,
                'amount' => $transaction->amount,
                'description' => $transaction->description,
                'reference' => $transaction->reference_number,
                'balance' => $balance,
                'status' => $transaction->status,
                'linked_amount' => $transaction->getLinkedAmount(),
                'available_amount' => $transaction->getAvailableAmount(),
            ];
        }
        
        return [
            'account' => [
                'id' => $intermediateAccount->id,
                'main_account' => $intermediateAccount->mainAccount->name ?? 'غير معروف',
                'intermediate_account' => $intermediateAccount->intermediateAccount1->name ?? 'غير معروف',
            ],
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo,
            ],
            'movements' => $movements,
            'summary' => [
                'opening_balance' => 0, // يمكن حسابه من العمليات السابقة
                'total_receipts' => $transactions->where('type', 'receipt')->sum('amount'),
                'total_payments' => $transactions->where('type', 'payment')->sum('amount'),
                'closing_balance' => $balance,
            ],
        ];
    }

    /**
     * Generate linking report.
     *
     * @param int|null $intermediateAccountId
     * @param array $filters
     * @return array
     */
    public function getLinkingReport(?int $intermediateAccountId = null, array $filters = []): array
    {
        $query = TransactionLink::with([
            'sourceTransaction.intermediateAccount.mainAccount',
            'targetTransaction',
            'linker'
        ]);
        
        if ($intermediateAccountId) {
            $query->whereHas('sourceTransaction', function ($q) use ($intermediateAccountId) {
                $q->where('intermediate_account_id', $intermediateAccountId);
            });
        }
        
        if (isset($filters['date_from'])) {
            $query->where('linked_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->where('linked_at', '<=', $filters['date_to']);
        }
        
        if (isset($filters['linked_by'])) {
            $query->where('linked_by', $filters['linked_by']);
        }
        
        $links = $query->orderBy('linked_at', 'desc')->get();
        
        $report = [];
        
        foreach ($links as $link) {
            $report[] = [
                'link_id' => $link->id,
                'linked_at' => $link->linked_at,
                'linked_by' => $link->linker->name ?? 'غير معروف',
                'source_transaction' => [
                    'id' => $link->sourceTransaction->id,
                    'type' => $link->sourceTransaction->type,
                    'amount' => $link->sourceTransaction->amount,
                    'date' => $link->sourceTransaction->transaction_date,
                    'description' => $link->sourceTransaction->description,
                ],
                'target_transaction' => [
                    'id' => $link->targetTransaction->id,
                    'type' => $link->targetTransaction->type,
                    'amount' => $link->targetTransaction->amount,
                    'date' => $link->targetTransaction->transaction_date,
                    'description' => $link->targetTransaction->description,
                ],
                'linked_amount' => $link->linked_amount,
                'direction' => $link->getDirectionDescription(),
                'notes' => $link->notes,
            ];
        }
        
        return [
            'links' => $report,
            'summary' => [
                'total_links' => count($report),
                'total_amount' => $links->sum('linked_amount'),
            ],
        ];
    }

    /**
     * Generate performance report.
     *
     * @param array $filters
     * @return array
     */
    public function getPerformanceReport(array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? Carbon::now()->subMonth()->startOfDay();
        $dateTo = $filters['date_to'] ?? Carbon::now()->endOfDay();
        
        // إحصائيات العمليات
        $totalTransactions = IntermediateTransaction::whereBetween('transaction_date', [$dateFrom, $dateTo])->count();
        $completedTransactions = IntermediateTransaction::whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->where('status', 'completed')->count();
        $pendingTransactions = IntermediateTransaction::whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->where('status', 'pending')->count();
        
        // إحصائيات الربط
        $totalLinks = TransactionLink::whereBetween('linked_at', [$dateFrom, $dateTo])->count();
        
        // متوسط وقت الربط
        $avgLinkingTime = DB::table('intermediate_transactions as t')
            ->join('transaction_links as l', function ($join) {
                $join->on('t.id', '=', 'l.source_transaction_id')
                    ->orOn('t.id', '=', 'l.target_transaction_id');
            })
            ->whereBetween('t.transaction_date', [$dateFrom, $dateTo])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, t.transaction_date, l.linked_at)) as avg_hours')
            ->value('avg_hours');
        
        // الحسابات الأكثر نشاطاً
        $mostActiveAccounts = IntermediateTransaction::with('intermediateAccount.mainAccount')
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->select('intermediate_account_id', DB::raw('COUNT(*) as transaction_count'))
            ->groupBy('intermediate_account_id')
            ->orderBy('transaction_count', 'desc')
            ->limit(10)
            ->get();
        
        return [
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo,
            ],
            'transactions' => [
                'total' => $totalTransactions,
                'completed' => $completedTransactions,
                'pending' => $pendingTransactions,
                'completion_rate' => $totalTransactions > 0 ? round(($completedTransactions / $totalTransactions) * 100, 2) : 0,
            ],
            'linking' => [
                'total_links' => $totalLinks,
                'avg_linking_time_hours' => round($avgLinkingTime ?? 0, 2),
            ],
            'most_active_accounts' => $mostActiveAccounts->map(function ($item) {
                return [
                    'account_id' => $item->intermediate_account_id,
                    'account_name' => $item->intermediateAccount->mainAccount->name ?? 'غير معروف',
                    'transaction_count' => $item->transaction_count,
                ];
            }),
        ];
    }

    /**
     * Generate aging report for unlinked transactions.
     *
     * @param int|null $intermediateAccountId
     * @return array
     */
    public function getAgingReport(?int $intermediateAccountId = null): array
    {
        $query = IntermediateTransaction::where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled');
        
        if ($intermediateAccountId) {
            $query->where('intermediate_account_id', $intermediateAccountId);
        }
        
        $transactions = $query->get();
        
        $aging = [
            '0-7' => ['count' => 0, 'amount' => 0],
            '8-30' => ['count' => 0, 'amount' => 0],
            '31-60' => ['count' => 0, 'amount' => 0],
            '61-90' => ['count' => 0, 'amount' => 0],
            '90+' => ['count' => 0, 'amount' => 0],
        ];
        
        foreach ($transactions as $transaction) {
            $days = Carbon::parse($transaction->transaction_date)->diffInDays(now());
            $amount = $transaction->getAvailableAmount();
            
            if ($days <= 7) {
                $aging['0-7']['count']++;
                $aging['0-7']['amount'] += $amount;
            } elseif ($days <= 30) {
                $aging['8-30']['count']++;
                $aging['8-30']['amount'] += $amount;
            } elseif ($days <= 60) {
                $aging['31-60']['count']++;
                $aging['31-60']['amount'] += $amount;
            } elseif ($days <= 90) {
                $aging['61-90']['count']++;
                $aging['61-90']['amount'] += $amount;
            } else {
                $aging['90+']['count']++;
                $aging['90+']['amount'] += $amount;
            }
        }
        
        return [
            'aging' => $aging,
            'total_count' => $transactions->count(),
            'total_amount' => $transactions->sum(function ($t) {
                return $t->getAvailableAmount();
            }),
        ];
    }
}
