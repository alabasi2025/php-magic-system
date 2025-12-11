<?php

namespace App\Services\Reports;

use App\Models\ChartAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\FiscalPeriod;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GeneralLedgerService
{
    /**
     * Generate General Ledger Report (دفتر الأستاذ العام)
     *
     * @param int $accountId
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int|null $fiscalPeriodId
     * @param array $options
     * @return array
     */
    public function generate(
        int $accountId,
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

        // الحصول على معلومات الحساب
        $account = ChartAccount::with('parent')->findOrFail($accountId);

        // حساب الرصيد الافتتاحي
        $openingBalance = $this->calculateOpeningBalance($accountId, $startDate);

        // الحصول على الحركات
        $transactions = $this->getTransactions($accountId, $startDate, $endDate);

        // حساب الرصيد الختامي
        $closingBalance = $openingBalance;
        foreach ($transactions as &$transaction) {
            $closingBalance += $transaction['debit'] - $transaction['credit'];
            $transaction['balance'] = $closingBalance;
        }

        // حساب الإجماليات
        $totalDebit = collect($transactions)->sum('debit');
        $totalCredit = collect($transactions)->sum('credit');

        return [
            'account' => [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'parent_name' => $account->parent ? $account->parent->name : null,
            ],
            'opening_balance' => $openingBalance,
            'transactions' => $transactions,
            'closing_balance' => $closingBalance,
            'summary' => [
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'net_movement' => $totalDebit - $totalCredit,
                'transaction_count' => count($transactions),
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
     * Get transactions for an account
     *
     * @param int $accountId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    protected function getTransactions(int $accountId, string $startDate, string $endDate): array
    {
        $lines = JournalEntryLine::with(['journalEntry'])
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entry_lines.chart_account_id', $accountId)
            ->whereBetween('journal_entries.entry_date', [$startDate, $endDate])
            ->where('journal_entries.status', 'approved')
            ->select('journal_entry_lines.*', 'journal_entries.entry_date', 'journal_entries.entry_number', 'journal_entries.description')
            ->orderBy('journal_entries.entry_date')
            ->orderBy('journal_entries.id')
            ->get();

        $transactions = [];

        foreach ($lines as $line) {
            $transactions[] = [
                'date' => $line->entry_date,
                'entry_number' => $line->entry_number,
                'description' => $line->description ?? $line->journalEntry->description ?? '',
                'debit' => $line->debit ?? 0,
                'credit' => $line->credit ?? 0,
                'balance' => 0, // سيتم حسابه في الدالة الرئيسية
                'journal_entry_id' => $line->journal_entry_id,
            ];
        }

        return $transactions;
    }

    /**
     * Generate for multiple accounts
     *
     * @param array $accountIds
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int|null $fiscalPeriodId
     * @return array
     */
    public function generateMultiple(
        array $accountIds,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $fiscalPeriodId = null
    ): array {
        $reports = [];
        
        foreach ($accountIds as $accountId) {
            $reports[] = $this->generate($accountId, $startDate, $endDate, $fiscalPeriodId);
        }
        
        return $reports;
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
        $rows[] = ['دفتر الأستاذ العام'];
        $rows[] = ['الحساب: ' . $data['account']['code'] . ' - ' . $data['account']['name']];
        $rows[] = ['الفترة من ' . $data['period']['start_date'] . ' إلى ' . $data['period']['end_date']];
        $rows[] = [''];
        
        // Opening Balance
        $rows[] = ['', '', 'الرصيد الافتتاحي', '', '', number_format($data['opening_balance'], 2)];
        $rows[] = [''];
        
        // Transactions Header
        $rows[] = ['التاريخ', 'رقم القيد', 'الوصف', 'مدين', 'دائن', 'الرصيد'];
        
        // Transactions
        foreach ($data['transactions'] as $transaction) {
            $rows[] = [
                $transaction['date'],
                $transaction['entry_number'],
                $transaction['description'],
                number_format($transaction['debit'], 2),
                number_format($transaction['credit'], 2),
                number_format($transaction['balance'], 2),
            ];
        }
        
        // Totals
        $rows[] = [''];
        $rows[] = ['', '', 'الإجمالي', number_format($data['summary']['total_debit'], 2), number_format($data['summary']['total_credit'], 2), ''];
        $rows[] = ['', '', 'الرصيد الختامي', '', '', number_format($data['closing_balance'], 2)];
        
        return $rows;
    }
}
