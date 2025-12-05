<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BalancedJournalEntry implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('تفاصيل القيد يجب أن تكون مصفوفة.');
            return;
        }

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($value as $detail) {
            if (!isset($detail['debit']) || !isset($detail['credit'])) {
                $fail('كل سطر يجب أن يحتوي على مبلغ مدين ومبلغ دائن.');
                return;
            }

            $totalDebit += floatval($detail['debit']);
            $totalCredit += floatval($detail['credit']);
        }

        // Check if debit equals credit (with tolerance for floating point)
        $difference = abs($totalDebit - $totalCredit);
        if ($difference > 0.01) {
            $fail(sprintf(
                'القيد غير متوازن. إجمالي المدين (%.2f) يجب أن يساوي إجمالي الدائن (%.2f).',
                $totalDebit,
                $totalCredit
            ));
        }
    }
}
