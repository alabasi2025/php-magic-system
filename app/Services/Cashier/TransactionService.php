<?php

namespace App\Services\Cashier;

use App\Models\User;
use App\Genes\Cashier\CheckUserBalanceGene;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * TransactionService
 *
 * خدمة منطق العمل المسؤولة عن إدارة معاملات الصرافين.
 * تستخدم "جين" CheckUserBalanceGene لتغليف منطق التحقق من الرصيد.
 *
 * @package App\Services\Cashier
 */
class TransactionService
{
    // استخدام الجين (Trait) لتضمين منطق التحقق من الرصيد
    use CheckUserBalanceGene;

    /**
     * تنفيذ معاملة سحب (Withdrawal) بعد التحقق من الرصيد.
     *
     * @param User $user المستخدم الذي سيتم السحب من رصيده.
     * @param float $amount المبلغ المراد سحبه.
     * @return bool
     * @throws \Throwable
     */
    public function withdraw(User $user, float $amount): bool
    {
        // 1. التحقق من الرصيد باستخدام "الجين"
        // سيتم إطلاق استثناء InsufficientBalanceException إذا كان الرصيد غير كافٍ
        $this->checkBalance($user, $amount);

        // 2. تنفيذ المعاملة داخل قاعدة بيانات (Transaction) لضمان السلامة
        try {
            DB::beginTransaction();

            // تحديث رصيد المستخدم
            // يجب التأكد من أن نموذج User يحتوي على حقل 'balance'
            $user->balance -= $amount;
            $user->save();

            // تسجيل المعاملة (يجب أن يكون هناك جدول للمعاملات، لكن نكتفي بالتسجيل في السجل هنا)
            Log::info("تم سحب مبلغ {$amount} بنجاح من المستخدم ID: {$user->id}. الرصيد الجديد: {$user->balance}");

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("فشل تنفيذ معاملة السحب للمستخدم ID: {$user->id}. الخطأ: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * تنفيذ معاملة إيداع (Deposit).
     *
     * @param User $user المستخدم الذي سيتم الإيداع في رصيده.
     * @param float $amount المبلغ المراد إيداعه.
     * @return bool
     * @throws \Throwable
     */
    public function deposit(User $user, float $amount): bool
    {
        try {
            DB::beginTransaction();

            // تحديث رصيد المستخدم
            // يجب التأكد من أن نموذج User يحتوي على حقل 'balance'
            $user->balance += $amount;
            $user->save();

            // تسجيل المعاملة
            Log::info("تم إيداع مبلغ {$amount} بنجاح للمستخدم ID: {$user->id}. الرصيد الجديد: {$user->balance}");

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("فشل تنفيذ معاملة الإيداع للمستخدم ID: {$user->id}. الخطأ: " . $e->getMessage());
            throw $e;
        }
    }
}