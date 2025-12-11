<?php

namespace App\Services\Reports;

use App\Models\ChartAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\FiscalPeriod;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrialBalanceService
{
    /**
     * Generate Trial Balance Report
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

        // الحصول على جميع الحسابات
        $accounts = ChartAccount::with('parent')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $trialBalanceData = [];
        $totalDebit = 0;
        $totalCredit = 0;
        $totalOpeningDebit = 0;
        $totalOpeningCredit = 0;
        $totalClosingDebit = 0;
        $totalClosingCredit = 0;

        foreach ($accounts as $account) {
            // حساب الرصيد الافتتاحي (قبل تاريخ البداية)
            $openingBalance = $this->calculateOpeningBalance($account->id, $startDate);
            
            // حساب الحركة خلال الفترة
            $periodMovement = $this->calculatePeriodMovement($account->id, $startDate, $endDate);
            
            // حساب الرصيد الختامي
            $closingBalance = $openingBalance + $periodMovement['debit'] - $periodMovement['credit'];

            // تحديد نوع الحساب (مدين/دائن)
            $isDebitAccount = in_array($account->type, ['asset', 'expense']);

            $openingDebit = 0;
            $openingCredit = 0;
            $closingDebit = 0;
            $closingCredit = 0;

            // تصنيف الرصيد الافتتاحي
            if ($openingBalance > 0) {
                if ($isDebitAccount) {
                    $openingDebit = $openingBalance;
                } else {
                    $openingCredit = $openingBalance;
                }
            } elseif ($openingBalance < 0) {
                if ($isDebitAccount) {
                    $openingCredit = abs($openingBalance);
                } else {
                    $openingDebit = abs($openingBalance);
                }
            }

            // تصنيف الرصيد الختامي
            if ($closingBalance > 0) {
                if ($isDebitAccount) {
                    $closingDebit = $closingBalance;
                } else {
                    $closingCredit = $closingBalance;
                }
            } elseif ($closingBalance < 0) {
                if ($isDebitAccount) {
                    $closingCredit = abs($closingBalance);
                } else {
                    $closingDebit = abs($closingBalance);
                }
            }

            // إضافة البيانات فقط إذا كان هناك حركة أو رصيد
            if ($openingBalance != 0 || $periodMovement['debit'] != 0 || $periodMovement['credit'] != 0) {
                $trialBalanceData[] = [
                    'account_id' => $account->id,
                    'account_code' => $account->code,
                    'account_name' => $account->name,
                    'account_type' => $account->type,
                    'parent_name' => $account->parent ? $account->parent->name : null,
                    'opening_debit' => $openingDebit,
                    'opening_credit' => $openingCredit,
                    'period_debit' => $periodMovement['debit'],
                    'period_credit' => $periodMovement['credit'],
                    'closing_debit' => $closingDebit,
                    'closing_credit' => $closingCredit,
                ];

                $totalOpeningDebit += $openingDebit;
                $totalOpeningCredit += $openingCredit;
                $totalDebit += $periodMovement['debit'];
                $totalCredit += $periodMovement['credit'];
                $totalClosingDebit += $closingDebit;
                $totalClosingCredit += $closingCredit;
            }
        }

        return [
            'data' => $trialBalanceData,
            'summary' => [
                'total_opening_debit' => $totalOpeningDebit,
                'total_opening_credit' => $totalOpeningCredit,
                'total_period_debit' => $totalDebit,
                'total_period_credit' => $totalCredit,
                'total_closing_debit' => $totalClosingDebit,
                'total_closing_credit' => $totalClosingCredit,
                'opening_balance_difference' => $totalOpeningDebit - $totalOpeningCredit,
                'period_balance_difference' => $totalDebit - $totalCredit,
                'closing_balance_difference' => $totalClosingDebit - $totalClosingCredit,
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
     * Calculate opening balance for an account
     *
     * @param int $accountId
     * @param string $beforeDate
     * @return float
     */
    protected function calculateOpeningBalance(int $accountId, string $beforeDate): float
    {
        $result = JournalEntryLine::join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entry_lines.chart_account_id', $accountId)
            ->where('journal_entries.entry_date', '<', $beforeDate)
            ->where('journal_entries.status', 'approved')
            ->select(
                DB::raw('COALESCE(SUM(journal_entry_lines.debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(journal_entry_lines.credit), 0) as total_credit')
            )
            ->first();

        return ($result->total_debit ?? 0) - ($result->total_credit ?? 0);
    }

    /**
     * Calculate period movement for an account
     *
     * @param int $accountId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    protected function calculatePeriodMovement(int $accountId, string $startDate, string $endDate): array
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

        return [
            'debit' => $result->total_debit ?? 0,
            'credit' => $result->total_credit ?? 0,
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
        $rows[] = ['كود الحساب', 'اسم الحساب', 'رصيد افتتاحي مدين', 'رصيد افتتاحي دائن', 'حركة مدينة', 'حركة دائنة', 'رصيد ختامي مدين', 'رصيد ختامي دائن'];
        
        // Data rows
        foreach ($data['data'] as $row) {
            $rows[] = [
                $row['account_code'],
                $row['account_name'],
                number_format($row['opening_debit'], 2),
                number_format($row['opening_credit'], 2),
                number_format($row['period_debit'], 2),
                number_format($row['period_credit'], 2),
                number_format($row['closing_debit'], 2),
                number_format($row['closing_credit'], 2),
            ];
        }
        
        // Totals
        $rows[] = [
            '',
            'الإجمالي',
            number_format($data['summary']['total_opening_debit'], 2),
            number_format($data['summary']['total_opening_credit'], 2),
            number_format($data['summary']['total_period_debit'], 2),
            number_format($data['summary']['total_period_credit'], 2),
            number_format($data['summary']['total_closing_debit'], 2),
            number_format($data['summary']['total_closing_credit'], 2),
        ];
        
        return $rows;
    }
}
