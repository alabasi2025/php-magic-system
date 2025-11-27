<?php

namespace App\Genes\Cashiers\Actions;

use App\Genes\Cashiers\Models\Cashier;
use App\Genes\ChartOfAccounts\Models\Account;
use Illuminate\Support\Facades\DB;
use App\Exceptions\BusinessLogicException;

/**
 * @class LinkCashierToAccountAction
 * @package App\Genes\Cashiers\Actions
 * @description يربط حساب الصراف (Cashier) بحساب فرعي (Sub-Account) في دليل الحسابات (Chart of Accounts).
 *              هذا الإجراء يمثل منطق العمل الأساسي لربط الحساب التحليلي (الصراف) بالحساب المحاسبي المقابل له.
 *              يجب أن يكون الحساب الفرعي موجودًا ومناسبًا للربط.
 */
class LinkCashierToAccountAction
{
    /**
     * ينفذ عملية ربط حساب الصراف بحساب دليل الحسابات.
     *
     * @param int $cashierId معرف الصراف (Cashier ID).
     * @param int $accountId معرف الحساب الفرعي في دليل الحسابات (Account ID).
     * @return Cashier
     * @throws BusinessLogicException إذا لم يتم العثور على الصراف أو الحساب.
     */
    public function execute(int $cashierId, int $accountId): Cashier
    {
        // 1. التحقق من وجود الصراف
        $cashier = Cashier::find($cashierId);
        if (!$cashier) {
            throw new BusinessLogicException("Cashier with ID {$cashierId} not found.");
        }

        // 2. التحقق من وجود الحساب
        $account = Account::find($accountId);
        if (!$account) {
            throw new BusinessLogicException("Account with ID {$accountId} not found in Chart of Accounts.");
        }

        // 3. تنفيذ عملية الربط في قاعدة البيانات
        DB::beginTransaction();
        try {
            // يتم تخزين معرف الحساب المرتبط في حقل مخصص في جدول الصرافين
            $cashier->account_id = $accountId;
            $cashier->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // تسجيل الخطأ والرمي باستثناء منطق العمل
            // Log::error("Failed to link cashier to account: " . $e->getMessage());
            throw new BusinessLogicException("Failed to link cashier to account due to a database error.");
        }

        return $cashier;
    }
}