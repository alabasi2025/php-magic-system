<?php

namespace App\Http\Controllers;

use App\Services\Reports\TrialBalanceService;
use App\Services\Reports\IncomeStatementService;
use App\Services\Reports\BalanceSheetService;
use App\Services\Reports\GeneralLedgerService;
use App\Services\Reports\JournalEntriesReportService;
use App\Services\Reports\CashFlowService;
use App\Services\Reports\PdfExportService;
use App\Services\Reports\ExcelExportService;
use App\Services\Reports\ReportCacheService;
use App\Observers\ReportAccessObserver;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountingReportController extends Controller
{
    protected $cacheService;
    protected $auditObserver;

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->cacheService = new ReportCacheService();
        $this->auditObserver = new ReportAccessObserver();
    }

    /**
     * Display reports dashboard
     */
    public function dashboard()
    {
        return view('reports.dashboard');
    }

    /**
     * Generate Trial Balance Report
     */
    public function trialBalance(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $fiscalPeriodId = $request->input('fiscal_period_id');

        $cacheKey = "trial_balance_{$startDate}_{$endDate}_{$fiscalPeriodId}";

        $data = $this->cacheService->remember($cacheKey, function () use ($startDate, $endDate, $fiscalPeriodId) {
            $service = new TrialBalanceService();
            return $service->generate($startDate, $endDate, $fiscalPeriodId);
        });

        $this->auditObserver->accessed('trial_balance', compact('startDate', 'endDate'));

        if ($request->input('format') === 'pdf') {
            return $this->exportPdf('trial_balance', $data, 'trial-balance');
        }

        if ($request->input('format') === 'excel') {
            return $this->exportExcel('trial_balance', $data, 'trial-balance');
        }

        return view('reports.trial-balance', compact('data'));
    }

    /**
     * Generate Income Statement Report
     */
    public function incomeStatement(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $fiscalPeriodId = $request->input('fiscal_period_id');

        $cacheKey = "income_statement_{$startDate}_{$endDate}_{$fiscalPeriodId}";

        $data = $this->cacheService->remember($cacheKey, function () use ($startDate, $endDate, $fiscalPeriodId) {
            $service = new IncomeStatementService();
            return $service->generate($startDate, $endDate, $fiscalPeriodId);
        });

        $this->auditObserver->accessed('income_statement', compact('startDate', 'endDate'));

        if ($request->input('format') === 'pdf') {
            return $this->exportPdf('income_statement', $data, 'income-statement');
        }

        if ($request->input('format') === 'excel') {
            return $this->exportExcel('income_statement', $data, 'income-statement');
        }

        return view('reports.income-statement', compact('data'));
    }

    /**
     * Generate Balance Sheet Report
     */
    public function balanceSheet(Request $request)
    {
        $asOfDate = $request->input('as_of_date', Carbon::now()->toDateString());
        $fiscalPeriodId = $request->input('fiscal_period_id');

        $cacheKey = "balance_sheet_{$asOfDate}_{$fiscalPeriodId}";

        $data = $this->cacheService->remember($cacheKey, function () use ($asOfDate, $fiscalPeriodId) {
            $service = new BalanceSheetService();
            return $service->generate($asOfDate, $fiscalPeriodId);
        });

        $this->auditObserver->accessed('balance_sheet', compact('asOfDate'));

        if ($request->input('format') === 'pdf') {
            return $this->exportPdf('balance_sheet', $data, 'balance-sheet');
        }

        if ($request->input('format') === 'excel') {
            return $this->exportExcel('balance_sheet', $data, 'balance-sheet');
        }

        return view('reports.balance-sheet', compact('data'));
    }

    /**
     * Generate General Ledger Report
     */
    public function generalLedger(Request $request)
    {
        $accountId = $request->input('account_id');
        
        if (!$accountId) {
            return redirect()->back()->with('error', 'يرجى اختيار حساب');
        }

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $fiscalPeriodId = $request->input('fiscal_period_id');

        $cacheKey = "general_ledger_{$accountId}_{$startDate}_{$endDate}_{$fiscalPeriodId}";

        $data = $this->cacheService->remember($cacheKey, function () use ($accountId, $startDate, $endDate, $fiscalPeriodId) {
            $service = new GeneralLedgerService();
            return $service->generate($accountId, $startDate, $endDate, $fiscalPeriodId);
        });

        $this->auditObserver->accessed('general_ledger', compact('accountId', 'startDate', 'endDate'));

        if ($request->input('format') === 'pdf') {
            return $this->exportPdf('general_ledger', $data, 'general-ledger');
        }

        if ($request->input('format') === 'excel') {
            return $this->exportExcel('general_ledger', $data, 'general-ledger');
        }

        return view('reports.general-ledger', compact('data'));
    }

    /**
     * Generate Journal Entries Report
     */
    public function journalEntries(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $fiscalPeriodId = $request->input('fiscal_period_id');

        $options = [
            'status' => $request->input('status'),
            'entry_type' => $request->input('entry_type'),
            'created_by' => $request->input('created_by'),
        ];

        $cacheKey = "journal_entries_{$startDate}_{$endDate}_" . md5(json_encode($options));

        $data = $this->cacheService->remember($cacheKey, function () use ($startDate, $endDate, $fiscalPeriodId, $options) {
            $service = new JournalEntriesReportService();
            return $service->generate($startDate, $endDate, $fiscalPeriodId, $options);
        });

        $this->auditObserver->accessed('journal_entries', compact('startDate', 'endDate'));

        if ($request->input('format') === 'pdf') {
            return $this->exportPdf('journal_entries', $data, 'journal-entries');
        }

        if ($request->input('format') === 'excel') {
            return $this->exportExcel('journal_entries', $data, 'journal-entries');
        }

        return view('reports.journal-entries', compact('data'));
    }

    /**
     * Generate Cash Flow Report
     */
    public function cashFlow(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $fiscalPeriodId = $request->input('fiscal_period_id');

        $cacheKey = "cash_flow_{$startDate}_{$endDate}_{$fiscalPeriodId}";

        $data = $this->cacheService->remember($cacheKey, function () use ($startDate, $endDate, $fiscalPeriodId) {
            $service = new CashFlowService();
            return $service->generate($startDate, $endDate, $fiscalPeriodId);
        });

        $this->auditObserver->accessed('cash_flow', compact('startDate', 'endDate'));

        if ($request->input('format') === 'pdf') {
            return $this->exportPdf('cash_flow', $data, 'cash-flow');
        }

        if ($request->input('format') === 'excel') {
            return $this->exportExcel('cash_flow', $data, 'cash-flow');
        }

        return view('reports.cash-flow', compact('data'));
    }

    /**
     * Export report to PDF
     */
    protected function exportPdf(string $reportType, array $data, string $filename)
    {
        $service = new PdfExportService();
        $this->auditObserver->exported($reportType, 'pdf', []);
        
        return $service->download($reportType, $data, $filename);
    }

    /**
     * Export report to Excel
     */
    protected function exportExcel(string $reportType, array $data, string $filename)
    {
        $service = new ExcelExportService();
        $this->auditObserver->exported($reportType, 'excel', []);
        
        return $service->export($reportType, $data, $filename);
    }

    /**
     * Clear report cache
     */
    public function clearCache()
    {
        $this->cacheService->flush();
        
        return redirect()->back()->with('success', 'تم مسح ذاكرة التخزين المؤقت للتقارير');
    }
}
