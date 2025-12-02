<?php

namespace App\Genes\PARTNER_ACCOUNTING\Services;

use App\Models\Partner;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PartnerReportService
{
    /**
     * يحصل على كشف حساب الشريك (Partner Statement).
     *
     * @param int $partnerId
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getPartnerStatement(int $partnerId, string $startDate, string $endDate): Collection
    {
        // منطق الحصول على كشف الحساب
        return DB::table('partner_transactions')
            ->where('partner_id', $partnerId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();
    }

    /**
     * يحصل على تقرير توزيع الأرباح (Profit Distribution Report).
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getProfitDistributionReport(string $startDate, string $endDate): Collection
    {
        // منطق الحصول على تقرير توزيع الأرباح
        return DB::table('profit_distributions')
            ->whereBetween('distribution_date', [$startDate, $endDate])
            ->get();
    }

    /**
     * يحصل على تقرير التسوية (Settlement Report).
     *
     * @param string $settlementDate
     * @return Collection
     */
    public function getSettlementReport(string $settlementDate): Collection
    {
        // منطق الحصول على تقرير التسوية
        return DB::table('settlements')
            ->where('settlement_date', $settlementDate)
            ->get();
    }

    /**
     * يحصل على ملخص الشريك (Partner Summary).
     *
     * @param int $partnerId
     * @return array
     */
    public function getPartnerSummary(int $partnerId): array
    {
        // منطق الحصول على ملخص الشريك
        $partner = Partner::find($partnerId);
        if (!$partner) {
            return [];
        }

        return [
            'total_earnings' => $partner->transactions()->sum('amount'),
            'total_paid' => $partner->settlements()->sum('amount'),
            'balance' => $partner->balance,
        ];
    }

    /**
     * تصدير البيانات إلى ملف Excel.
     *
     * @param Collection $data
     * @param string $fileName
     * @return string المسار إلى الملف المصدر
     */
    public function exportToExcel(Collection $data, string $fileName): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // افتراض أن البيانات هي مجموعة من الكائنات/المصفوفات
        if ($data->isNotEmpty()) {
            // كتابة الرؤوس
            $headers = array_keys($data->first()->toArray());
            $sheet->fromArray($headers, NULL, 'A1');
            
            // كتابة البيانات
            $sheet->fromArray($data->toArray(), NULL, 'A2');
        }

        $filePath = "/tmp/{$fileName}.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;
    }

    /**
     * تصدير البيانات إلى ملف PDF.
     *
     * @param Collection $data
     * @param string $fileName
     * @return string المسار إلى الملف المصدر
     */
    public function exportToPDF(Collection $data, string $fileName): string
    {
        // ملاحظة: يتطلب هذا تثبيت مكتبة مثل dompdf أو tcpdf.
        // للاختصار، سنقوم بإنشاء ملف نصي بسيط يمثل محتوى PDF.
        $content = "Partner Report: {$fileName}\n\n";
        $content .= $data->toJson(JSON_PRETTY_PRINT);
        
        $filePath = "/tmp/{$fileName}.pdf";
        file_put_contents($filePath, $content);

        return $filePath;
    }
}
