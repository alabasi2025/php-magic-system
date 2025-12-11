<?php

namespace App\Services\Reports;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ExcelExportService
{
    /**
     * Export report to Excel
     *
     * @param string $reportType
     * @param array $data
     * @param string $filename
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(string $reportType, array $data, string $filename, array $options = [])
    {
        $exportClass = $this->getExportClass($reportType, $data, $options);
        
        return Excel::download($exportClass, $filename . '.xlsx');
    }

    /**
     * Get export class for report type
     *
     * @param string $reportType
     * @param array $data
     * @param array $options
     * @return object
     */
    protected function getExportClass(string $reportType, array $data, array $options)
    {
        return new class($data, $options) implements FromArray, ShouldAutoSize, WithStyles {
            protected $data;
            protected $options;

            public function __construct($data, $options)
            {
                $this->data = $data;
                $this->options = $options;
            }

            public function array(): array
            {
                // Convert data to array format
                return $this->data;
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => ['font' => ['bold' => true, 'size' => 14]],
                    2 => ['font' => ['bold' => true]],
                ];
            }
        };
    }

    /**
     * Export to CSV
     *
     * @param string $reportType
     * @param array $data
     * @param string $filename
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportToCsv(string $reportType, array $data, string $filename, array $options = [])
    {
        $exportClass = $this->getExportClass($reportType, $data, $options);
        
        return Excel::download($exportClass, $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * Store Excel file
     *
     * @param string $reportType
     * @param array $data
     * @param string $path
     * @param array $options
     * @return bool
     */
    public function store(string $reportType, array $data, string $path, array $options = []): bool
    {
        $exportClass = $this->getExportClass($reportType, $data, $options);
        
        return Excel::store($exportClass, $path);
    }
}
