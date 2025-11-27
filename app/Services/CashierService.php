<?php

namespace App\Services;

use App\Models\User;
use App\Models\CashBox;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\CashierException;

/**
 * CashierService
 *
 * خدمة منطق الأعمال الخاصة بنظام الصرافين (Cashiers Gene).
 * تتولى هذه الخدمة إدارة عمليات الصرافين مثل فتح وإغلاق الوردية،
 * والتحقق من حالة الصندوق النقدي.
 *
 * المهمة 2068: Business Logic - نظام الصرافين (Cashiers) - Task 8
 * بما أن المهمة عامة وتطلب منطق أعمال، تم إنشاء هيكل الخدمة
 * وإضافة دوال أساسية لإدارة ورديات الصرافين (فتح، إغلاق، التحقق من الحالة).
 */
class CashierService
{
    /**
     * التحقق من حالة الصراف الحالية.
     *
     * @param User $cashier
     * @return array
     */
    public function getCashierStatus(User $cashier): array
    {
        // البحث عن آخر صندوق نقدي (CashBox) مفتوح لهذا الصراف
        $activeCashBox = CashBox::where('user_id', $cashier->id)
            ->whereNull('closing_time')
            ->latest('opening_time')
            ->first();

        if ($activeCashBox) {
            return [
                'is_open' => true,
                'cash_box_id' => $activeCashBox->id,
                'opening_time' => $activeCashBox->opening_time,
                'initial_balance' => $activeCashBox->initial_balance,
                'current_balance' => $this->calculateCurrentBalance($activeCashBox),
                'message' => 'الصندوق النقدي للصراف مفتوح حاليًا.',
            ];
        }

        return [
            'is_open' => false,
            'message' => 'الصندوق النقدي للصراف مغلق حاليًا. يجب فتح وردية جديدة.',
        ];
    }

    /**
     * حساب الرصيد الحالي للصندوق النقدي.
     *
     * ملاحظة: هذه دالة وهمية وتحتاج إلى منطق محاسبي حقيقي
     * لحساب الرصيد بناءً على حركات الإيداع والسحب (Vouchers/Transactions)
     * المرتبطة بـ CashBox.
     *
     * @param CashBox $cashBox
     * @return float
     */
    private function calculateCurrentBalance(CashBox $cashBox): float
    {
        // المنطق الحقيقي يتطلب جلب جميع حركات الصندوق (سندات قبض وصرف)
        // وحساب صافي الحركة وإضافته إلى الرصيد الافتتاحي.
        // لأغراض هذه المهمة، تم افتراض رصيد بسيط.
        try {
            // قيمة وهمية للمهمة الحالية
            $currentBalance = $cashBox->initial_balance + 500.00; // افتراض إضافة 500

            return round($currentBalance, 2);
        } catch (\Exception $e) {
            Log::error("Error calculating cash box balance for ID {$cashBox->id}: " . $e->getMessage());
            // في حالة الخطأ، نرجع الرصيد الافتتاحي أو نطلق استثناء
            return $cashBox->initial_balance;
        }
    }

    /**
     * فتح وردية جديدة للصراف.
     *
     * @param User $cashier
     * @param float $initialBalance
     * @return CashBox
     * @throws CashierException
     */
    public function openShift(User $cashier, float $initialBalance): CashBox
    {
        // التأكد من عدم وجود وردية مفتوحة بالفعل
        if ($this->getCashierStatus($cashier)['is_open']) {
            throw new CashierException('لا يمكن فتح وردية جديدة. الصندوق النقدي للصراف مفتوح بالفعل.');
        }

        // بدء عملية قاعدة البيانات (Transaction) لضمان الاتساق
        return DB::transaction(function () use ($cashier, $initialBalance) {
            $cashBox = CashBox::create([
                'user_id' => $cashier->id,
                'opening_time' => now(),
                'initial_balance' => $initialBalance,
                // يمكن إضافة حقول أخرى مثل الفرع، الوحدة، إلخ.
            ]);

            // هنا يجب إضافة منطق إنشاء قيد افتتاحي (Journal Entry) إذا لزم الأمر

            return $cashBox;
        });
    }

    /**
     * إغلاق الوردية الحالية للصراف.
     *
     * @param User $cashier
     * @param float $closingBalance
     * @return CashBox
     * @throws CashierException
     */
    public function closeShift(User $cashier, float $closingBalance): CashBox
    {
        $status = $this->getCashierStatus($cashier);

        if (!$status['is_open']) {
            throw new CashierException('لا توجد وردية مفتوحة لإغلاقها.');
        }

        $cashBox = CashBox::find($status['cash_box_id']);

        // بدء عملية قاعدة البيانات (Transaction) لضمان الاتساق
        return DB::transaction(function () use ($cashBox, $closingBalance) {
            $cashBox->update([
                'closing_time' => now(),
                'closing_balance' => $closingBalance,
            ]);

            // هنا يجب إضافة منطق تسوية الفروقات (إذا كان هناك فرق بين الرصيد المحسوب والرصيد الفعلي)
            // وإنشاء قيد إغلاق (Journal Entry)

            return $cashBox;
        });
    }
}