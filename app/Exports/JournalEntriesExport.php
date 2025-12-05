<?php

namespace App\Exports;

use App\Models\JournalEntry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JournalEntriesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // جلب جميع القيود مع تفاصيلها
        return JournalEntry::with('items.account')->get();
    }

    /**
     * تعيين رؤوس الأعمدة في ملف Excel.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'تاريخ القيد',
            'الوصف',
            'الرقم المرجعي',
            'كود الحساب',
            'اسم الحساب',
            'النوع (مدين/دائن)',
            'المبلغ',
        ];
    }

    /**
     * تعيين البيانات لكل صف.
     *
     * @param JournalEntry $entry
     * @return array
     */
    public function map($entry): array
    {
        $rows = [];
        // تصدير كل طرف قيد كصف منفصل
        foreach ($entry->items as $item) {
            $rows[] = [
                $entry->id,
                $entry->entry_date,
                $entry->description,
                $entry->reference_number,
                $item->account->code ?? 'N/A', // نفترض وجود حقل 'code' في نموذج Account
                $item->account->name ?? 'N/A', // نفترض وجود حقل 'name' في نموذج Account
                $item->type === 'debit' ? 'مدين' : 'دائن',
                $item->amount,
            ];
        }
        return $rows;
    }

    /**
     * تطبيق التنسيقات على ورقة العمل.
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // تنسيق الصف الأول (رؤوس الأعمدة)
            1    => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFA0A0A0']]],
        ];
    }
}
