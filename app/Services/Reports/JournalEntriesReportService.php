<?php

namespace App\Services\Reports;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\FiscalPeriod;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JournalEntriesReportService
{
    /**
     * Generate Journal Entries Report (تقرير القيود اليومية)
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

        // الفلاتر الإضافية
        $status = $options['status'] ?? null;
        $entryType = $options['entry_type'] ?? null;
        $createdBy = $options['created_by'] ?? null;

        // الحصول على القيود
        $query = JournalEntry::with(['lines.chartAccount', 'createdBy', 'fiscalPeriod'])
            ->whereBetween('entry_date', [$startDate, $endDate]);

        if ($status) {
            $query->where('status', $status);
        }

        if ($entryType) {
            $query->where('entry_type', $entryType);
        }

        if ($createdBy) {
            $query->where('created_by', $createdBy);
        }

        $entries = $query->orderBy('entry_date')
            ->orderBy('entry_number')
            ->get();

        // معالجة البيانات
        $entriesData = [];
        $totalDebit = 0;
        $totalCredit = 0;
        $entryCount = 0;

        foreach ($entries as $entry) {
            $entryDebit = $entry->lines->sum('debit');
            $entryCredit = $entry->lines->sum('credit');
            
            $entriesData[] = [
                'id' => $entry->id,
                'entry_number' => $entry->entry_number,
                'entry_date' => $entry->entry_date,
                'entry_type' => $entry->entry_type,
                'description' => $entry->description,
                'status' => $entry->status,
                'fiscal_period' => $entry->fiscalPeriod ? $entry->fiscalPeriod->name : null,
                'created_by' => $entry->createdBy ? $entry->createdBy->name : null,
                'created_at' => $entry->created_at->toDateTimeString(),
                'lines' => $entry->lines->map(function ($line) {
                    return [
                        'account_code' => $line->chartAccount->code,
                        'account_name' => $line->chartAccount->name,
                        'description' => $line->description,
                        'debit' => $line->debit ?? 0,
                        'credit' => $line->credit ?? 0,
                    ];
                })->toArray(),
                'total_debit' => $entryDebit,
                'total_credit' => $entryCredit,
                'is_balanced' => abs($entryDebit - $entryCredit) < 0.01,
            ];

            $totalDebit += $entryDebit;
            $totalCredit += $entryCredit;
            $entryCount++;
        }

        // إحصائيات حسب النوع
        $byType = $this->getStatisticsByType($entries);
        
        // إحصائيات حسب الحالة
        $byStatus = $this->getStatisticsByStatus($entries);

        return [
            'entries' => $entriesData,
            'summary' => [
                'total_entries' => $entryCount,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'difference' => $totalDebit - $totalCredit,
                'is_balanced' => abs($totalDebit - $totalCredit) < 0.01,
            ],
            'statistics' => [
                'by_type' => $byType,
                'by_status' => $byStatus,
            ],
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'fiscal_period_id' => $fiscalPeriodId,
            ],
            'filters' => [
                'status' => $status,
                'entry_type' => $entryType,
                'created_by' => $createdBy,
            ],
            'generated_at' => Carbon::now()->toDateTimeString(),
        ];
    }

    /**
     * Get statistics by entry type
     *
     * @param \Illuminate\Database\Eloquent\Collection $entries
     * @return array
     */
    protected function getStatisticsByType($entries): array
    {
        $byType = [];
        
        foreach ($entries->groupBy('entry_type') as $type => $typeEntries) {
            $debit = $typeEntries->sum(function ($entry) {
                return $entry->lines->sum('debit');
            });
            
            $credit = $typeEntries->sum(function ($entry) {
                return $entry->lines->sum('credit');
            });
            
            $byType[$type] = [
                'count' => $typeEntries->count(),
                'total_debit' => $debit,
                'total_credit' => $credit,
            ];
        }
        
        return $byType;
    }

    /**
     * Get statistics by status
     *
     * @param \Illuminate\Database\Eloquent\Collection $entries
     * @return array
     */
    protected function getStatisticsByStatus($entries): array
    {
        $byStatus = [];
        
        foreach ($entries->groupBy('status') as $status => $statusEntries) {
            $debit = $statusEntries->sum(function ($entry) {
                return $entry->lines->sum('debit');
            });
            
            $credit = $statusEntries->sum(function ($entry) {
                return $entry->lines->sum('credit');
            });
            
            $byStatus[$status] = [
                'count' => $statusEntries->count(),
                'total_debit' => $debit,
                'total_credit' => $credit,
            ];
        }
        
        return $byStatus;
    }

    /**
     * Get single journal entry details
     *
     * @param int $entryId
     * @return array
     */
    public function getEntryDetails(int $entryId): array
    {
        $entry = JournalEntry::with(['lines.chartAccount', 'createdBy', 'approvedBy', 'fiscalPeriod'])
            ->findOrFail($entryId);

        return [
            'id' => $entry->id,
            'entry_number' => $entry->entry_number,
            'entry_date' => $entry->entry_date,
            'entry_type' => $entry->entry_type,
            'description' => $entry->description,
            'status' => $entry->status,
            'reference_number' => $entry->reference_number,
            'fiscal_period' => $entry->fiscalPeriod ? [
                'id' => $entry->fiscalPeriod->id,
                'name' => $entry->fiscalPeriod->name,
            ] : null,
            'created_by' => $entry->createdBy ? [
                'id' => $entry->createdBy->id,
                'name' => $entry->createdBy->name,
            ] : null,
            'approved_by' => $entry->approvedBy ? [
                'id' => $entry->approvedBy->id,
                'name' => $entry->approvedBy->name,
            ] : null,
            'created_at' => $entry->created_at->toDateTimeString(),
            'updated_at' => $entry->updated_at->toDateTimeString(),
            'lines' => $entry->lines->map(function ($line) {
                return [
                    'id' => $line->id,
                    'account_code' => $line->chartAccount->code,
                    'account_name' => $line->chartAccount->name,
                    'description' => $line->description,
                    'debit' => $line->debit ?? 0,
                    'credit' => $line->credit ?? 0,
                ];
            })->toArray(),
            'total_debit' => $entry->lines->sum('debit'),
            'total_credit' => $entry->lines->sum('credit'),
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
        $rows[] = ['تقرير القيود اليومية'];
        $rows[] = ['الفترة من ' . $data['period']['start_date'] . ' إلى ' . $data['period']['end_date']];
        $rows[] = [''];
        
        // Entries
        foreach ($data['entries'] as $entry) {
            $rows[] = ['رقم القيد: ' . $entry['entry_number'], 'التاريخ: ' . $entry['entry_date'], 'الحالة: ' . $entry['status']];
            $rows[] = ['الوصف: ' . $entry['description']];
            $rows[] = ['كود الحساب', 'اسم الحساب', 'البيان', 'مدين', 'دائن'];
            
            foreach ($entry['lines'] as $line) {
                $rows[] = [
                    $line['account_code'],
                    $line['account_name'],
                    $line['description'],
                    number_format($line['debit'], 2),
                    number_format($line['credit'], 2),
                ];
            }
            
            $rows[] = ['', '', 'الإجمالي', number_format($entry['total_debit'], 2), number_format($entry['total_credit'], 2)];
            $rows[] = [''];
        }
        
        // Summary
        $rows[] = ['الإجمالي العام'];
        $rows[] = ['عدد القيود', $data['summary']['total_entries']];
        $rows[] = ['إجمالي المدين', number_format($data['summary']['total_debit'], 2)];
        $rows[] = ['إجمالي الدائن', number_format($data['summary']['total_credit'], 2)];
        $rows[] = ['الفرق', number_format($data['summary']['difference'], 2)];
        
        return $rows;
    }
}
