<?php

namespace App\Services\Reports;

use App\Models\ChartAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\FiscalPeriod;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CashFlowService
{
    /**
     * Generate Cash Flow Statement (قائمة التدفقات النقدية)
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

        // الحصول على حسابات النقدية
        $cashAccounts = $this->getCashAccounts();

        // حساب الرصيد الافتتاحي
        $openingCash = $this->calculateOpeningCash($cashAccounts, $startDate);

        // التدفقات التشغيلية
        $operatingCashFlow = $this->calculateOperatingCashFlow($startDate, $endDate);

        // التدفقات الاستثمارية
        $investingCashFlow = $this->calculateInvestingCashFlow($startDate, $endDate);

        // التدفقات التمويلية
        $financingCashFlow = $this->calculateFinancingCashFlow($startDate, $endDate);

        // صافي التدفق النقدي
        $netCashFlow = $operatingCashFlow['total'] + $investingCashFlow['total'] + $financingCashFlow['total'];

        // الرصيد الختامي
        $closingCash = $openingCash + $netCashFlow;

        return [
            'operating_activities' => $operatingCashFlow,
            'investing_activities' => $investingCashFlow,
            'financing_activities' => $financingCashFlow,
            'summary' => [
                'opening_cash' => $openingCash,
                'net_operating_cash_flow' => $operatingCashFlow['total'],
                'net_investing_cash_flow' => $investingCashFlow['total'],
                'net_financing_cash_flow' => $financingCashFlow['total'],
                'net_cash_flow' => $netCashFlow,
                'closing_cash' => $closingCash,
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
     * Get cash and bank accounts
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getCashAccounts()
    {
        return ChartAccount::where('type', 'asset')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('name', 'like', '%نقد%')
                    ->orWhere('name', 'like', '%صندوق%')
                    ->orWhere('name', 'like', '%بنك%')
                    ->orWhere('name', 'like', '%cash%')
                    ->orWhere('name', 'like', '%bank%');
            })
            ->get();
    }

    /**
     * Calculate opening cash balance
     *
     * @param \Illuminate\Database\Eloquent\Collection $cashAccounts
     * @param string $beforeDate
     * @return float
     */
    protected function calculateOpeningCash($cashAccounts, string $beforeDate): float
    {
        $total = 0;
        
        foreach ($cashAccounts as $account) {
            $result = JournalEntryLine::join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                ->where('journal_entry_lines.chart_account_id', $account->id)
                ->where('journal_entries.entry_date', '<', $beforeDate)
                ->where('journal_entries.status', 'approved')
                ->select(
                    DB::raw('COALESCE(SUM(journal_entry_lines.debit), 0) as total_debit'),
                    DB::raw('COALESCE(SUM(journal_entry_lines.credit), 0) as total_credit')
                )
                ->first();
            
            $total += ($result->total_debit ?? 0) - ($result->total_credit ?? 0);
        }
        
        return $total;
    }

    /**
     * Calculate operating cash flow
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    protected function calculateOperatingCashFlow(string $startDate, string $endDate): array
    {
        // صافي الدخل
        $incomeStatementService = new IncomeStatementService();
        $incomeStatement = $incomeStatementService->generate($startDate, $endDate);
        $netIncome = $incomeStatement['summary']['net_income'];

        // التعديلات (مبسطة)
        $adjustments = [
            [
                'description' => 'صافي الدخل',
                'amount' => $netIncome,
            ],
            // يمكن إضافة تعديلات أخرى مثل الاستهلاك، المخصصات، إلخ
        ];

        return [
            'items' => $adjustments,
            'total' => $netIncome,
        ];
    }

    /**
     * Calculate investing cash flow
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    protected function calculateInvestingCashFlow(string $startDate, string $endDate): array
    {
        // الحصول على حركات الأصول الثابتة
        $fixedAssetAccounts = ChartAccount::where('type', 'asset')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('name', 'like', '%ثابت%')
                    ->orWhere('name', 'like', '%fixed%')
                    ->orWhere('name', 'like', '%equipment%')
                    ->orWhere('name', 'like', '%building%');
            })
            ->get();

        $items = [];
        $total = 0;

        foreach ($fixedAssetAccounts as $account) {
            $movement = $this->getAccountMovement($account->id, $startDate, $endDate);
            
            if ($movement != 0) {
                $items[] = [
                    'description' => $account->name,
                    'amount' => -$movement, // سالب لأنها استثمارات
                ];
                $total -= $movement;
            }
        }

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * Calculate financing cash flow
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    protected function calculateFinancingCashFlow(string $startDate, string $endDate): array
    {
        // الحصول على حركات القروض وحقوق الملكية
        $financingAccounts = ChartAccount::whereIn('type', ['liability', 'equity'])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('name', 'like', '%قرض%')
                    ->orWhere('name', 'like', '%loan%')
                    ->orWhere('name', 'like', '%رأس المال%')
                    ->orWhere('name', 'like', '%capital%');
            })
            ->get();

        $items = [];
        $total = 0;

        foreach ($financingAccounts as $account) {
            $movement = $this->getAccountMovement($account->id, $startDate, $endDate);
            
            if ($movement != 0) {
                $items[] = [
                    'description' => $account->name,
                    'amount' => $movement,
                ];
                $total += $movement;
            }
        }

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * Get account movement for the period
     *
     * @param int $accountId
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    protected function getAccountMovement(int $accountId, string $startDate, string $endDate): float
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

        return ($result->total_debit ?? 0) - ($result->total_credit ?? 0);
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
        
        $rows[] = ['قائمة التدفقات النقدية'];
        $rows[] = ['الفترة من ' . $data['period']['start_date'] . ' إلى ' . $data['period']['end_date']];
        $rows[] = [''];
        
        // Operating
        $rows[] = ['التدفقات النقدية من الأنشطة التشغيلية'];
        foreach ($data['operating_activities']['items'] as $item) {
            $rows[] = [$item['description'], number_format($item['amount'], 2)];
        }
        $rows[] = ['صافي التدفقات من الأنشطة التشغيلية', number_format($data['summary']['net_operating_cash_flow'], 2)];
        $rows[] = [''];
        
        // Investing
        $rows[] = ['التدفقات النقدية من الأنشطة الاستثمارية'];
        foreach ($data['investing_activities']['items'] as $item) {
            $rows[] = [$item['description'], number_format($item['amount'], 2)];
        }
        $rows[] = ['صافي التدفقات من الأنشطة الاستثمارية', number_format($data['summary']['net_investing_cash_flow'], 2)];
        $rows[] = [''];
        
        // Financing
        $rows[] = ['التدفقات النقدية من الأنشطة التمويلية'];
        foreach ($data['financing_activities']['items'] as $item) {
            $rows[] = [$item['description'], number_format($item['amount'], 2)];
        }
        $rows[] = ['صافي التدفقات من الأنشطة التمويلية', number_format($data['summary']['net_financing_cash_flow'], 2)];
        $rows[] = [''];
        
        // Summary
        $rows[] = ['صافي التغير في النقدية', number_format($data['summary']['net_cash_flow'], 2)];
        $rows[] = ['النقدية في بداية الفترة', number_format($data['summary']['opening_cash'], 2)];
        $rows[] = ['النقدية في نهاية الفترة', number_format($data['summary']['closing_cash'], 2)];
        
        return $rows;
    }
}
