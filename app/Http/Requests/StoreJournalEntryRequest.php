<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\BalancedJournalEntry;

class StoreJournalEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Journal Entry Fields
            'entry_number' => ['nullable', 'string', 'max:50', 'unique:journal_entries,entry_number'],
            'entry_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:500'],
            'reference' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:draft,pending,approved,rejected,posted'],
            'notes' => ['nullable', 'string', 'max:1000'],
            
            // Journal Entry Details
            'details' => ['required', 'array', 'min:2', new BalancedJournalEntry],
            'details.*.account_id' => ['required', 'exists:chart_accounts,id'],
            'details.*.debit' => ['required', 'numeric', 'min:0'],
            'details.*.credit' => ['required', 'numeric', 'min:0'],
            'details.*.description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'entry_number.unique' => 'رقم القيد مستخدم من قبل.',
            'entry_date.required' => 'تاريخ القيد مطلوب.',
            'entry_date.date' => 'تاريخ القيد يجب أن يكون تاريخاً صحيحاً.',
            'description.required' => 'وصف القيد مطلوب.',
            'description.max' => 'وصف القيد يجب ألا يتجاوز 500 حرف.',
            'status.required' => 'حالة القيد مطلوبة.',
            'status.in' => 'حالة القيد غير صحيحة.',
            
            'details.required' => 'تفاصيل القيد مطلوبة.',
            'details.array' => 'تفاصيل القيد يجب أن تكون مصفوفة.',
            'details.min' => 'يجب إدخال سطرين على الأقل في تفاصيل القيد.',
            
            'details.*.account_id.required' => 'الحساب مطلوب في كل سطر.',
            'details.*.account_id.exists' => 'الحساب المحدد غير موجود.',
            'details.*.debit.required' => 'المبلغ المدين مطلوب.',
            'details.*.debit.numeric' => 'المبلغ المدين يجب أن يكون رقماً.',
            'details.*.debit.min' => 'المبلغ المدين يجب أن يكون صفر أو أكثر.',
            'details.*.credit.required' => 'المبلغ الدائن مطلوب.',
            'details.*.credit.numeric' => 'المبلغ الدائن يجب أن يكون رقماً.',
            'details.*.credit.min' => 'المبلغ الدائن يجب أن يكون صفر أو أكثر.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'entry_number' => 'رقم القيد',
            'entry_date' => 'تاريخ القيد',
            'description' => 'الوصف',
            'reference' => 'المرجع',
            'status' => 'الحالة',
            'notes' => 'الملاحظات',
            'details' => 'التفاصيل',
            'details.*.account_id' => 'الحساب',
            'details.*.debit' => 'المبلغ المدين',
            'details.*.credit' => 'المبلغ الدائن',
            'details.*.description' => 'الوصف',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate entry number if not provided
        if (empty($this->entry_number)) {
            $this->merge([
                'entry_number' => $this->generateEntryNumber(),
            ]);
        }

        // Set default status if not provided
        if (empty($this->status)) {
            $this->merge([
                'status' => 'draft',
            ]);
        }
    }

    /**
     * Generate a unique entry number.
     */
    private function generateEntryNumber(): string
    {
        $year = date('Y');
        $lastEntry = \App\Models\JournalEntry::whereYear('entry_date', $year)
            ->orderBy('entry_number', 'desc')
            ->first();

        if ($lastEntry && preg_match('/JE-' . $year . '-(\d+)/', $lastEntry->entry_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('JE-%s-%04d', $year, $nextNumber);
    }
}
