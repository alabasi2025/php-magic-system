<?php

namespace App\Services;

use App\Exports\JournalEntriesExport;
use App\Imports\JournalEntriesImport;
use App\Models\JournalEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class JournalEntryImportExportService
{
    /**
     * تصدير القيود إلى ملف Excel.
     *
     * @param string $fileName اسم الملف المراد تصديره
     * @return BinaryFileResponse
     */
    public function exportToExcel(string $fileName = 'journal_entries.xlsx'): BinaryFileResponse
    {
        // استخدام فئة التصدير المخصصة لتنسيق البيانات
        return Excel::download(new JournalEntriesExport, $fileName);
    }

    /**
     * تصدير القيود إلى ملف CSV.
     *
     * @param string $fileName اسم الملف المراد تصديره
     * @return BinaryFileResponse
     */
    public function exportToCsv(string $fileName = 'journal_entries.csv'): BinaryFileResponse
    {
        // استخدام فئة التصدير المخصصة
        return Excel::download(new JournalEntriesExport, $fileName, \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * استيراد البيانات من ملف Excel/CSV والتحقق من صحتها.
     *
     * @param string $filePath المسار المؤقت للملف
     * @return Collection مجموعة البيانات المستوردة بعد التحقق
     * @throws \Illuminate\Validation\ValidationException
     */
    public function importFromExcel(string $filePath): Collection
    {
        // استخدام فئة الاستيراد المخصصة التي تقوم بالتحقق
        $import = new JournalEntriesImport();
        Excel::import($import, $filePath);

        // التحقق من وجود أخطاء في الاستيراد
        if ($import->failures()->isNotEmpty()) {
            // يمكن تخصيص رسالة الخطأ هنا
            $errors = $import->failures()->map(function ($failure) {
                return [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            })->toArray();

            throw \Illuminate\Validation\ValidationException::withMessages([
                'import_errors' => $errors,
            ]);
        }

        // إرجاع مجموعة البيانات المستوردة بنجاح (يمكن تعديل هذا لإرجاع البيانات قبل الحفظ)
        // في هذا المثال، سنفترض أن الاستيراد يقوم بالحفظ مباشرة بعد التحقق.
        // إذا أردنا المعاينة، يجب تعديل منطق الاستيراد لعدم الحفظ.
        // لغرض المعاينة، سنقوم بقراءة الملف مرة أخرى بدون حفظ.
        $rows = Excel::toCollection(new JournalEntriesImport(false), $filePath)->first();
        return $this->validateImportData($rows);
    }

    /**
     * التحقق من صحة البيانات المستوردة يدوياً قبل الحفظ.
     *
     * @param Collection $rows مجموعة الصفوف المستوردة
     * @return Collection مجموعة البيانات بعد التحقق
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateImportData(Collection $rows): Collection
    {
        $errors = [];
        $validatedData = collect();

        // قواعد التحقق
        $rules = [
            'entry_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'account_code' => ['required', 'exists:accounts,code'], // نفترض وجود حقل 'code' في جدول 'accounts'
            'type' => ['required', 'in:مدين,دائن'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ];

        foreach ($rows as $index => $row) {
            // تحويل الأسماء العربية إلى أسماء الحقول في قاعدة البيانات
            $data = [
                'entry_date' => $row['تاريخ_القيد'] ?? null,
                'description' => $row['الوصف'] ?? null,
                'reference_number' => $row['الرقم_المرجعي'] ?? null,
                'account_code' => $row['كود_الحساب'] ?? null,
                'type' => $row['النوع'] ?? null,
                'amount' => $row['المبلغ'] ?? null,
            ];

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $index + 2, // الصف يبدأ من 2 بافتراض وجود رأس
                    'messages' => $validator->errors()->all(),
                    'data' => $data,
                ];
            } else {
                // إضافة البيانات التي تم التحقق منها
                $validatedData->push($data);
            }
        }

        if (!empty($errors)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'validation_errors' => $errors,
            ]);
        }

        return $validatedData;
    }
}
