<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Services\JournalEntryImportExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class JournalEntryController extends Controller
{
    protected JournalEntryImportExportService $importExportService;

    public function __construct(JournalEntryImportExportService $importExportService)
    {
        $this->importExportService = $importExportService;
    }

    /**
     * عرض قائمة القيود (مثال).
     */
    public function index()
    {
        $entries = JournalEntry::paginate(10);
        return view('journal_entries.index', compact('entries'));
    }

    /**
     * تصدير القيود إلى ملف Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        $fileName = 'journal_entries_' . now()->format('Ymd_His');

        if ($format === 'csv') {
            return $this->importExportService->exportToCsv($fileName . '.csv');
        }

        // الافتراضي هو Excel (xlsx)
        return $this->importExportService->exportToExcel($fileName . '.xlsx');
    }

    /**
     * عرض واجهة الاستيراد.
     *
     * @return \Illuminate\View\View
     */
    public function import()
    {
        // عرض البيانات التي تم معاينتها في الجلسة
        $previewData = session('import_preview_data');
        $validationErrors = session('import_validation_errors');

        return view('journal_entries.import', compact('previewData', 'validationErrors'));
    }

    /**
     * معالجة ملف الاستيراد (معاينة أو حفظ).
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'import_file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
            'action' => ['required', 'in:preview,import'],
        ]);

        $file = $request->file('import_file');
        $filePath = $file->storeAs('temp', $file->getClientOriginalName());

        try {
            if ($request->input('action') === 'preview') {
                // وضع المعاينة: قراءة البيانات والتحقق منها دون حفظ
                $validatedData = $this->importExportService->validateImportData(
                    \Maatwebsite\Excel\Facades\Excel::toCollection(null, Storage::path($filePath))->first()
                );

                // تخزين البيانات والرسائل في الجلسة
                return redirect()->route('journal_entries.import')
                    ->with('success', 'تمت معاينة البيانات بنجاح. يرجى مراجعة الجدول أدناه.')
                    ->with('import_preview_data', $validatedData)
                    ->with('import_validation_errors', null);

            } elseif ($request->input('action') === 'import') {
                // وضع الاستيراد: قراءة البيانات والتحقق منها وحفظها
                $this->importExportService->importFromExcel(Storage::path($filePath));

                return redirect()->route('journal_entries.index')
                    ->with('success', 'تم استيراد القيود بنجاح.');
            }
        } catch (ValidationException $e) {
            // التعامل مع أخطاء التحقق
            $errors = $e->errors();
            if (isset($errors['validation_errors'])) {
                // أخطاء التحقق اليدوي (في وضع المعاينة)
                return redirect()->route('journal_entries.import')
                    ->withErrors(['import_file' => 'يوجد أخطاء في البيانات المستوردة.'])
                    ->with('import_validation_errors', $errors['validation_errors']);
            } elseif (isset($errors['import_errors'])) {
                // أخطاء التحقق من Maatwebsite (في وضع الاستيراد)
                return redirect()->route('journal_entries.import')
                    ->withErrors(['import_file' => 'فشل الاستيراد بسبب أخطاء في البيانات.'])
                    ->with('import_validation_errors', $errors['import_errors']);
            }
            throw $e; // رمي أي استثناء آخر
        } finally {
            Storage::delete($filePath);
        }
    }

    /**
     * تحميل قالب Excel للاستيراد.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate()
    {
        // إنشاء ملف Excel فارغ يحتوي على رؤوس الأعمدة المطلوبة
        $headings = [
            'تاريخ القيد',
            'الوصف',
            'الرقم المرجعي',
            'كود الحساب',
            'النوع (مدين/دائن)',
            'المبلغ',
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new class($headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $headings;
            public function __construct($headings) { $this->headings = $headings; }
            public function array(): array { return []; }
            public function headings(): array { return $this->headings; }
        }, 'journal_entries_template.xlsx');
    }
}
