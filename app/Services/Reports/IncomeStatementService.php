<?php

namespace App\Services\Reports;

use App\Models\ChartAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\FiscalPeriod;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncomeStatementService
{
    /**
     * Generate Income Statement Report (قائمة الدخل)
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int|null $fiscalPeriodId
     * @param array $options
     * @return array
     */
    public function generate(
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $fiscalPeriodId = null,
        array $options = []
    ): array {
        // تحديد الفترة
        if ($fiscalPeriodId) {
            $period = FiscalPeriod::findOrFail($fiscalPeriodId);
            $startDate = $period->start_date;
            $endDate = $period->end_date;
        } else {
            $startDate = $startDate ?? Carbon::now()->startOfMonth()->toDateString();
            $endDate = $endDate ?? Carbon::now()->endOfMonth()->toDateString();
        }

        // الحصول على حسابات الإيرادات
        $revenueAccounts = $this->getAccountsByType('revenue', $startDate, $endDate);
        
        // الحصول على حسابات المصروفات
        $expenseAccounts = $this->getAccountsByType('expense', $startDate, $endDate);

        // حساب الإجماليات
        $totalRevenue = collect($revenueAccounts)->sum('amount');
        $totalExpenses = collect($expenseAccounts)->sum('amount');
        $netIncome = $totalRevenue - $totalExpenses;

        // تصنيف الإيرادات
        $revenueCategories = $this->categorizeAccounts($revenueAccounts);
        
        // تصنيف المصروفات
        $expenseCategories = $this->categorizeAccounts($expenseAccounts);

        return [
            'revenues' => [
                'accounts' => $revenueAccounts,
                'categories' => $revenueCategories,
                'total' => $totalRevenue,
            ],
            'expenses' => [
                'accounts' => $expenseAccounts,
                'categories' => $expenseCategories,
                'total' => $totalExpenses,
            ],
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_expenses' => $totalExpenses,
                'net_income' => $netIncome,
                'profit_margin' => $totalRevenue > 0 ? ($netIncome / $totalRevenue) * 100 : 0,
            ],
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'fiscal_period_id' => $fiscalPeriodId,
            ],
            'generated_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    /**
     * Get accounts by type with their balances
     *
     * @param string $type
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    protected function getAccountsByType(string $type, string $startDate, string $endDate): array
    {
        $accounts = ChartAccount::where('account_type', $type)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $accountsData = [];

        foreach ($accounts as $account) {
            $balance = $this->calculateAccountBalance($account->id, $startDate, $endDate);
            
            if ($balance != 0) {
                $accountsData[] = [
                    'account_id' => $account->id,
                    'account_code' => $account->code,
                    'account_name' => $account->name,
                    'parent_id' => $account->parent_id,
                    'parent_name' => $account->parent ? $account->parent->name : null,
                    'amount' => abs($balance),
                    'percentage' => 0, // سيتم حسابه لاحقاً
                ];
            }
        }

        return $accountsData;
    }

    /**
     * Calculate account balance for the period
     *
     * @param int $accountId
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    protected function calculateAccountBalance(int $accountId, string $startDate, string $endDate): float
    {
        $result = JournalEntryLine::join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entry_lines.chart_account_id', $accountId)
            ->whereBetween('journal_entries.entry_date', [$startDate, $endDate])
            ->where('journal_entries.status', 'approved')
            ->select(
                DB::raw('COALESCE(SUM(journal_entry_lines.debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(journal_entry_lines.credit), 0) as total_credit')
            )
            ->first();

        // للإيرادات: الدائن - المدين
        // للمصروفات: المدين - الدائن
        return ($result->total_credit ?? 0) - ($result->total_debit ?? 0);
    }

    /**
     * Categorize accounts by parent
     *
     * @param array $accounts
     * @return array
     */
    protected function categorizeAccounts(array $accounts): array
    {
        $categories = [];
        
        foreach ($accounts as $account) {
            $categoryName = $account['parent_name'] ?? 'غير مصنف';
            
            if (!isset($categories[$categoryName])) {
                $categories[$categoryName] = [
                    'name' => $categoryName,
                    'accounts' => [],
                    'total' => 0,
                ];
            }
            
            $categories[$categoryName]['accounts'][] = $account;
            $categories[$categoryName]['total'] += $account['amount'];
        }
        
        return array_values($categories);
    }

    /**
     * Export to array for Excel/PDF
     *
     * @param array $data
     * @return array
     */
    public function toArray(array $data): array
    {
        $rows = [];
        
        // Header
        $rows[] = ['قائمة الدخل'];
        $rows[] = ['الفترة من ' . $data['period']['start_date'] . ' إلى ' . $data['period']['end_date']];
        $rows[] = [''];
        
        // Revenues
        $rows[] = ['الإيرادات'];
        foreach ($data['revenues']['accounts'] as $account) {
            $rows[] = [
                $account['account_code'],
                $account['account_name'],
                number_format($account['amount'], 2),
            ];
        }
        $rows[] = ['', 'إجمالي الإيرادات', number_format($data['summary']['total_revenue'], 2)];
        $rows[] = [''];
        
        // Expenses
        $rows[] = ['المصروفات'];
        foreach ($data['expenses']['accounts'] as $account) {
            $rows[] = [
                $account['account_code'],
                $account['account_name'],
                number_format($account['amount'], 2),
            ];
        }
        $rows[] = ['', 'إجمالي المصروفات', number_format($data['summary']['total_expenses'], 2)];
        $rows[] = [''];
        
        // Net Income
        $rows[] = ['', 'صافي الربح/الخسارة', number_format($data['summary']['net_income'], 2)];
        $rows[] = ['', 'هامش الربح %', number_format($data['summary']['profit_margin'], 2) . '%'];
        
        return $rows;
    }
}
