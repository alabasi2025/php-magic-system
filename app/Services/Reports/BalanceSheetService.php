<?php

namespace App\Services\Reports;

use App\Models\ChartAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\FiscalPeriod;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BalanceSheetService
{
    /**
     * Generate Balance Sheet Report (الميزانية العمومية)
     *
     * @param string|null $asOfDate
     * @param int|null $fiscalPeriodId
     * @param array $options
     * @return array
     */
    public function generate(
        ?string $asOfDate = null,
        ?int $fiscalPeriodId = null,
        array $options = []
    ): array {
        // تحديد التاريخ
        if ($fiscalPeriodId) {
            $period = FiscalPeriod::findOrFail($fiscalPeriodId);
            $asOfDate = $period->end_date;
        } else {
            $asOfDate = $asOfDate ?? Carbon::now()->toDateString();
        }

        // الحصول على الأصول
        $assets = $this->getAccountsByType('asset', $asOfDate);
        
        // الحصول على الخصوم
        $liabilities = $this->getAccountsByType('liability', $asOfDate);
        
        // الحصول على حقوق الملكية
        $equity = $this->getAccountsByType('equity', $asOfDate);

        // حساب صافي الدخل من قائمة الدخل
        $incomeStatementService = new IncomeStatementService();
        $incomeStatement = $incomeStatementService->generate(
            Carbon::parse($asOfDate)->startOfYear()->toDateString(),
            $asOfDate
        );
        $netIncome = $incomeStatement['summary']['net_income'];

        // حساب الإجماليات
        $totalAssets = collect($assets)->sum('amount');
        $totalLiabilities = collect($liabilities)->sum('amount');
        $totalEquity = collect($equity)->sum('amount') + $netIncome;

        // تصنيف الأصول
        $assetCategories = $this->categorizeAssets($assets);
        
        // تصنيف الخصوم
        $liabilityCategories = $this->categorizeLiabilities($liabilities);
        
        // تصنيف حقوق الملكية
        $equityCategories = $this->categorizeEquity($equity, $netIncome);

        return [
            'assets' => [
                'accounts' => $assets,
                'categories' => $assetCategories,
                'total' => $totalAssets,
            ],
            'liabilities' => [
                'accounts' => $liabilities,
                'categories' => $liabilityCategories,
                'total' => $totalLiabilities,
            ],
            'equity' => [
                'accounts' => $equity,
                'categories' => $equityCategories,
                'total' => $totalEquity,
                'net_income' => $netIncome,
            ],
            'summary' => [
                'total_assets' => $totalAssets,
                'total_liabilities' => $totalLiabilities,
                'total_equity' => $totalEquity,
                'total_liabilities_and_equity' => $totalLiabilities + $totalEquity,
                'balance_check' => abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01,
            ],
            'as_of_date' => $asOfDate,
            'fiscal_period_id' => $fiscalPeriodId,
            'generated_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    /**
     * Get accounts by type with their balances
     *
     * @param string $type
     * @param string $asOfDate
     * @return array
     */
    protected function getAccountsByType(string $type, string $asOfDate): array
    {
        $accounts = ChartAccount::where('type', $type)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $accountsData = [];

        foreach ($accounts as $account) {
            $balance = $this->calculateAccountBalance($account->id, $asOfDate);
            
            if ($balance != 0) {
                $accountsData[] = [
                    'account_id' => $account->id,
                    'account_code' => $account->code,
                    'account_name' => $account->name,
                    'parent_id' => $account->parent_id,
                    'parent_name' => $account->parent ? $account->parent->name : null,
                    'amount' => abs($balance),
                    'is_current' => $this->isCurrentAccount($account),
                ];
            }
        }

        return $accountsData;
    }

    /**
     * Calculate account balance up to a specific date
     *
     * @param int $accountId
     * @param string $asOfDate
     * @return float
     */
    protected function calculateAccountBalance(int $accountId, string $asOfDate): float
    {
        $result = JournalEntryLine::join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entry_lines.chart_account_id', $accountId)
            ->where('journal_entries.entry_date', '<=', $asOfDate)
            ->where('journal_entries.status', 'approved')
            ->select(
                DB::raw('COALESCE(SUM(journal_entry_lines.debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(journal_entry_lines.credit), 0) as total_credit')
            )
            ->first();

        return ($result->total_debit ?? 0) - ($result->total_credit ?? 0);
    }

    /**
     * Check if account is current (متداول)
     *
     * @param ChartAccount $account
     * @return bool
     */
    protected function isCurrentAccount(ChartAccount $account): bool
    {
        // يمكن تحسين هذا بناءً على تصنيف الحسابات
        $currentKeywords = ['متداول', 'current', 'cash', 'bank', 'receivable', 'payable', 'inventory'];
        
        foreach ($currentKeywords as $keyword) {
            if (stripos($account->name, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Categorize assets (current vs non-current)
     *
     * @param array $assets
     * @return array
     */
    protected function categorizeAssets(array $assets): array
    {
        $currentAssets = [];
        $nonCurrentAssets = [];
        
        foreach ($assets as $asset) {
            if ($asset['is_current']) {
                $currentAssets[] = $asset;
            } else {
                $nonCurrentAssets[] = $asset;
            }
        }
        
        return [
            'current' => [
                'name' => 'الأصول المتداولة',
                'accounts' => $currentAssets,
                'total' => collect($currentAssets)->sum('amount'),
            ],
            'non_current' => [
                'name' => 'الأصول غير المتداولة',
                'accounts' => $nonCurrentAssets,
                'total' => collect($nonCurrentAssets)->sum('amount'),
            ],
        ];
    }

    /**
     * Categorize liabilities (current vs non-current)
     *
     * @param array $liabilities
     * @return array
     */
    protected function categorizeLiabilities(array $liabilities): array
    {
        $currentLiabilities = [];
        $nonCurrentLiabilities = [];
        
        foreach ($liabilities as $liability) {
            if ($liability['is_current']) {
                $currentLiabilities[] = $liability;
            } else {
                $nonCurrentLiabilities[] = $liability;
            }
        }
        
        return [
            'current' => [
                'name' => 'الخصوم المتداولة',
                'accounts' => $currentLiabilities,
                'total' => collect($currentLiabilities)->sum('amount'),
            ],
            'non_current' => [
                'name' => 'الخصوم غير المتداولة',
                'accounts' => $nonCurrentLiabilities,
                'total' => collect($nonCurrentLiabilities)->sum('amount'),
            ],
        ];
    }

    /**
     * Categorize equity
     *
     * @param array $equity
     * @param float $netIncome
     * @return array
     */
    protected function categorizeEquity(array $equity, float $netIncome): array
    {
        return [
            'capital' => [
                'name' => 'رأس المال',
                'accounts' => $equity,
                'total' => collect($equity)->sum('amount'),
            ],
            'retained_earnings' => [
                'name' => 'الأرباح المحتجزة',
                'accounts' => [
                    [
                        'account_name' => 'صافي الدخل للفترة',
                        'amount' => $netIncome,
                    ],
                ],
                'total' => $netIncome,
            ],
        ];
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
        $rows[] = ['الميزانية العمومية'];
        $rows[] = ['كما في ' . $data['as_of_date']];
        $rows[] = [''];
        
        // Assets
        $rows[] = ['الأصول'];
        $rows[] = ['الأصول المتداولة'];
        foreach ($data['assets']['categories']['current']['accounts'] as $account) {
            $rows[] = ['', $account['account_name'], number_format($account['amount'], 2)];
        }
        $rows[] = ['', 'إجمالي الأصول المتداولة', number_format($data['assets']['categories']['current']['total'], 2)];
        $rows[] = [''];
        $rows[] = ['الأصول غير المتداولة'];
        foreach ($data['assets']['categories']['non_current']['accounts'] as $account) {
            $rows[] = ['', $account['account_name'], number_format($account['amount'], 2)];
        }
        $rows[] = ['', 'إجمالي الأصول غير المتداولة', number_format($data['assets']['categories']['non_current']['total'], 2)];
        $rows[] = ['', 'إجمالي الأصول', number_format($data['summary']['total_assets'], 2)];
        $rows[] = [''];
        
        // Liabilities
        $rows[] = ['الخصوم'];
        $rows[] = ['الخصوم المتداولة'];
        foreach ($data['liabilities']['categories']['current']['accounts'] as $account) {
            $rows[] = ['', $account['account_name'], number_format($account['amount'], 2)];
        }
        $rows[] = ['', 'إجمالي الخصوم المتداولة', number_format($data['liabilities']['categories']['current']['total'], 2)];
        $rows[] = [''];
        $rows[] = ['الخصوم غير المتداولة'];
        foreach ($data['liabilities']['categories']['non_current']['accounts'] as $account) {
            $rows[] = ['', $account['account_name'], number_format($account['amount'], 2)];
        }
        $rows[] = ['', 'إجمالي الخصوم غير المتداولة', number_format($data['liabilities']['categories']['non_current']['total'], 2)];
        $rows[] = ['', 'إجمالي الخصوم', number_format($data['summary']['total_liabilities'], 2)];
        $rows[] = [''];
        
        // Equity
        $rows[] = ['حقوق الملكية'];
        foreach ($data['equity']['accounts'] as $account) {
            $rows[] = ['', $account['account_name'], number_format($account['amount'], 2)];
        }
        $rows[] = ['', 'صافي الدخل', number_format($data['equity']['net_income'], 2)];
        $rows[] = ['', 'إجمالي حقوق الملكية', number_format($data['summary']['total_equity'], 2)];
        $rows[] = [''];
        $rows[] = ['', 'إجمالي الخصوم وحقوق الملكية', number_format($data['summary']['total_liabilities_and_equity'], 2)];
        
        return $rows;
    }
}
