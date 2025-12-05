<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\BalancedJournalEntry;
use Illuminate\Validation\Rule;

class UpdateJournalEntryRequest extends FormRequest
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
        $journalEntryId = $this->route('journal_entry');

        return [
            // Journal Entry Fields
            'entry_number' => ['nullable', 'string', 'max:50', Rule::unique('journal_entries', 'entry_number')->ignore($journalEntryId)],
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
            'description.required' => 'وصف القيد مطلوب.',
            'status.required' => 'حالة القيد مطلوبة.',
            'details.required' => 'تفاصيل القيد مطلوبة.',
            'details.min' => 'يجب إدخال سطرين على الأقل في تفاصيل القيد.',
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
            'status' => 'الحالة',
        ];
    }
}
