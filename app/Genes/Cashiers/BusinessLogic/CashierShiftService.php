<?php

namespace App\Genes\Cashiers\BusinessLogic;

use App\Models\Cashier;
use App\Models\CashierShift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Class CashierShiftService
 *
 * @package App\Genes\Cashiers\BusinessLogic
 * @description خدمة منطق العمل لإدارة ورديات الصرافين (فتح وإغلاق الوردية).
 *              تتبع هذه الفئة معمارية الجينات (Gene Architecture) وتُعد جزءًا من منطق العمل الأساسي لنظام الصرافين.
 */
class CashierShiftService
{
    /**
     * فتح وردية جديدة للصراف.
     *
     * @param Cashier $cashier كائن الصراف الذي يقوم بفتح الوردية.
     * @param float $initial_cash المبلغ النقدي الأولي في بداية الوردية.
     * @return CashierShift الوردية الجديدة التي تم فتحها.
     * @throws Exception إذا كان الصراف لديه وردية مفتوحة بالفعل.
     */
    public function openShift(Cashier $cashier, float $initial_cash): CashierShift
    {
        // 1. التحقق من عدم وجود وردية مفتوحة حاليًا للصراف
        if ($cashier->activeShift()->exists()) {
            throw new Exception("الصراف لديه وردية مفتوحة بالفعل.");
        }

        try {
            // 2. بدء عملية قاعدة بيانات (Transaction) لضمان الاتساق
            DB::beginTransaction();

            // 3. إنشاء سجل الوردية الجديدة
            $shift = CashierShift::create([
                'cashier_id' => $cashier->id,
                'opening_time' => now(),
                'initial_cash' => $initial_cash,
                'status' => 'open', // حالة الوردية: مفتوحة
            ]);

            // 4. تحديث حالة الصراف (اختياري، يعتمد على تصميم جدول الصرافين)
            // $cashier->update(['is_on_shift' => true]);

            DB::commit();

            Log::info("تم فتح وردية جديدة للصراف ID: {$cashier->id} بنجاح. الوردية ID: {$shift->id}");

            return $shift;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("فشل في فتح وردية للصراف ID: {$cashier->id}. الخطأ: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * إغلاق الوردية الحالية للصراف.
     *
     * @param Cashier $cashier كائن الصراف الذي يقوم بإغلاق الوردية.
     * @param float $closing_cash المبلغ النقدي النهائي عند إغلاق الوردية.
     * @return CashierShift الوردية التي تم إغلاقها.
     * @throws Exception إذا لم يكن لدى الصراف وردية مفتوحة.
     */
    public function closeShift(Cashier $cashier, float $closing_cash): CashierShift
    {
        // 1. العثور على الوردية المفتوحة حاليًا
        $shift = $cashier->activeShift()->first();

        if (!$shift) {
            throw new Exception("الصراف ليس لديه وردية مفتوحة لإغلاقها.");
        }

        try {
            // 2. بدء عملية قاعدة بيانات (Transaction)
            DB::beginTransaction();

            // 3. تحديث سجل الوردية بالإغلاق
            $shift->update([
                'closing_time' => now(),
                'closing_cash' => $closing_cash,
                'status' => 'closed', // حالة الوردية: مغلقة
                // يمكن إضافة حقول أخرى مثل 'total_sales', 'difference', إلخ.
            ]);

            // 4. تحديث حالة الصراف (اختياري)
            // $cashier->update(['is_on_shift' => false]);

            DB::commit();

            Log::info("تم إغلاق الوردية ID: {$shift->id} للصراف ID: {$cashier->id} بنجاح.");

            return $shift;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("فشل في إغلاق الوردية ID: {$shift->id} للصراف ID: {$cashier->id}. الخطأ: " . $e->getMessage());
            throw $e;
        }
    }
}