<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\ChartAccount;

/**
 * خدمة التحقق الذكي للقيود اليومية
 */
class SmartJournalValidatorService
{
    /**
     * التحقق من قيد يومي
     */
    public function validate(JournalEntry $entry): array
    {
        $warnings = [];
        $errors = [];
        $info = [];

        // التحقق من التوازن
        if ($entry->total_debit != $entry->total_credit) {
            $errors[] = [
                'type' => 'danger',
                'icon' => 'times-circle',
                'message' => 'القيد غير متوازن! المدين: ' . number_format($entry->total_debit, 2) . 
                            ' - الدائن: ' . number_format($entry->total_credit, 2)
            ];
        } else {
            $info[] = [
                'type' => 'success',
                'icon' => 'check-circle',
                'message' => 'القيد متوازن ✓'
            ];
        }

        // التحقق من التفاصيل
        if ($entry->details->count() == 0) {
            $errors[] = [
                'type' => 'danger',
                'icon' => 'exclamation-triangle',
                'message' => 'القيد لا يحتوي على تفاصيل!'
            ];
        }

        // التحقق من الحسابات
        foreach ($entry->details as $detail) {
            $account = ChartAccount::find($detail->account_id);
            
            if (!$account) {
                $errors[] = [
                    'type' => 'danger',
                    'icon' => 'ban',
                    'message' => 'الحساب رقم ' . $detail->account_id . ' غير موجود!'
                ];
            }

            // التحقق من المبالغ السالبة
            if ($detail->debit < 0 || $detail->credit < 0) {
                $errors[] = [
                    'type' => 'danger',
                    'icon' => 'minus-circle',
                    'message' => 'يوجد مبلغ سالب في السطر!'
                ];
            }

            // تحذير إذا كان المدين والدائن معاً
            if ($detail->debit > 0 && $detail->credit > 0) {
                $warnings[] = [
                    'type' => 'warning',
                    'icon' => 'exclamation-triangle',
                    'message' => 'يوجد سطر يحتوي على مدين ودائن معاً!'
                ];
            }
        }

        // التحقق من التاريخ
        if ($entry->entry_date > now()) {
            $warnings[] = [
                'type' => 'warning',
                'icon' => 'calendar-times',
                'message' => 'تاريخ القيد في المستقبل!'
            ];
        }

        return [
            'errors' => $errors,
            'warnings' => $warnings,
            'info' => $info,
            'is_valid' => count($errors) == 0
        ];
    }
}
