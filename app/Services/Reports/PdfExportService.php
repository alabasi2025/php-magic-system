<?php

namespace App\Services\Reports;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class PdfExportService
{
    /**
     * Export report to PDF
     *
     * @param string $reportType
     * @param array $data
     * @param array $options
     * @return \Barryvdh\DomPDF\PDF
     */
    public function export(string $reportType, array $data, array $options = [])
    {
        $viewName = $this->getViewName($reportType);
        $orientation = $options['orientation'] ?? 'portrait';
        $paperSize = $options['paper_size'] ?? 'a4';

        $pdf = Pdf::loadView($viewName, [
            'data' => $data,
            'options' => $options,
            'generated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        $pdf->setPaper($paperSize, $orientation);

        return $pdf;
    }

    /**
     * Get view name for report type
     *
     * @param string $reportType
     * @return string
     */
    protected function getViewName(string $reportType): string
    {
        $viewMap = [
            'trial_balance' => 'reports.pdf.trial-balance',
            'income_statement' => 'reports.pdf.income-statement',
            'balance_sheet' => 'reports.pdf.balance-sheet',
            'general_ledger' => 'reports.pdf.general-ledger',
            'journal_entries' => 'reports.pdf.journal-entries',
            'cash_flow' => 'reports.pdf.cash-flow',
        ];

        return $viewMap[$reportType] ?? 'reports.pdf.default';
    }

    /**
     * Download PDF
     *
     * @param string $reportType
     * @param array $data
     * @param string $filename
     * @param array $options
     * @return \Illuminate\Http\Response
     */
    public function download(string $reportType, array $data, string $filename, array $options = [])
    {
        $pdf = $this->export($reportType, $data, $options);
        return $pdf->download($filename . '.pdf');
    }

    /**
     * Stream PDF to browser
     *
     * @param string $reportType
     * @param array $data
     * @param array $options
     * @return \Illuminate\Http\Response
     */
    public function stream(string $reportType, array $data, array $options = [])
    {
        $pdf = $this->export($reportType, $data, $options);
        return $pdf->stream();
    }

    /**
     * Save PDF to storage
     *
     * @param string $reportType
     * @param array $data
     * @param string $path
     * @param array $options
     * @return bool
     */
    public function save(string $reportType, array $data, string $path, array $options = []): bool
    {
        $pdf = $this->export($reportType, $data, $options);
        return $pdf->save($path);
    }
}
