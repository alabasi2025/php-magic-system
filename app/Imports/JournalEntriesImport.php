<?php

namespace App\Imports;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class JournalEntriesImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected bool $shouldSave;

    public function __construct(bool $shouldSave = true)
    {
        $this->shouldSave = $shouldSave;
    }

    /**
     * معالجة مجموعة الصفوف المستوردة.
     *
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        if (!$this->shouldSave) {
            return; // لا يتم الحفظ في وضع المعاينة
        }

        // تجميع الصفوف حسب تاريخ القيد والوصف والرقم المرجعي لتكوين قيود رئيسية
        $groupedEntries = $rows->groupBy(function ($item) {
            return $item['تاريخ_القيد'] . '|' . $item['الوصف'] . '|' . $item['الرقم_المرجعي'];
        });

        DB::beginTransaction();
        try {
            foreach ($groupedEntries as $key => $items) {
                // التحقق من توازن القيد (المدين = الدائن)
                $totalDebit = $items->where('النوع', 'مدين')->sum('المبلغ');
                $totalCredit = $items->where('النوع', 'دائن')->sum('المبلغ');

                if (abs($totalDebit - $totalCredit) > 0.001) {
                    // يمكن تسجيل فشل هنا أو رمي استثناء
                    // لتبسيط المثال، سنقوم بتخطي القيد غير المتوازن
                    continue;
                }

                // إنشاء القيد الرئيسي
                $firstItem = $items->first();
                $entry = JournalEntry::create([
                    'entry_date' => $firstItem['تاريخ_القيد'],
                    'description' => $firstItem['الوصف'],
                    'reference_number' => $firstItem['الرقم_المرجعي'],
                    'user_id' => auth()->id() ?? 1, // يجب تعديل هذا ليناسب نظام المصادقة
                ]);

                // إنشاء أطراف القيد
                foreach ($items as $item) {
                    $account = Account::where('code', $item['كود_الحساب'])->first();

                    if ($account) {
                        JournalEntryItem::create([
                            'journal_entry_id' => $entry->id,
                            'account_id' => $account->id,
                            'type' => $item['النوع'] === 'مدين' ? 'debit' : 'credit',
                            'amount' => $item['المبلغ'],
                        ]);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // يمكن تسجيل الخطأ هنا
            throw $e;
        }
    }

    /**
     * تعيين رؤوس الأعمدة المتوقعة للتحقق.
     *
     * @return array
     */
    public function headingRow(): int
    {
        return 1; // الصف الأول هو رأس الأعمدة
    }

    /**
     * قواعد التحقق من صحة البيانات لكل صف.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'تاريخ_القيد' => ['required', 'date'],
            'الوصف' => ['nullable', 'string', 'max:255'],
            'الرقم_المرجعي' => ['nullable', 'string', 'max:255'],
            'كود_الحساب' => ['required', 'exists:accounts,code'],
            'النوع' => ['required', 'in:مدين,دائن'],
            'المبلغ' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    /**
     * تعيين رسائل الخطأ المخصصة.
     *
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'كود_الحساب.exists' => 'كود الحساب غير موجود في النظام.',
            'النوع.in' => 'يجب أن يكون النوع إما "مدين" أو "دائن".',
            'المبلغ.min' => 'يجب أن يكون المبلغ أكبر من صفر.',
        ];
    }
}
