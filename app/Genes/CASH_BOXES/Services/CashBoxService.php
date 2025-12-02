<?php

namespace App\Genes\CASH_BOXES\Services;

use App\Models\CashBox;
use App\Models\CashBoxTransaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * خدمة إدارة الصناديق النقدية والمعاملات المتعلقة بها.
 *
 * ملاحظة: تم افتراض أن الموديلات CashBox و CashBoxTransaction موجودة في App\Models.
 */
class CashBoxService
{
    /**
     * إنشاء صندوق نقدي جديد.
     *
     * @param array $data بيانات الصندوق النقدي (مثل الاسم، الوصف، الرصيد الأولي).
     * @return CashBox
     */
    public function create(array $data): CashBox
    {
        // يجب إضافة منطق التحقق من الصحة هنا
        return CashBox::create($data);
    }

    /**
     * تحديث بيانات صندوق نقدي موجود.
     *
     * @param int $id معرف الصندوق النقدي.
     * @param array $data البيانات المراد تحديثها.
     * @return CashBox|null
     */
    public function update(int $id, array $data): ?CashBox
    {
        $cashBox = $this->getById($id);
        if ($cashBox) {
            $cashBox->update($data);
        }
        return $cashBox;
    }

    /**
     * حذف صندوق نقدي.
     *
     * @param int $id معرف الصندوق النقدي.
     * @return bool
     */
    public function delete(int $id): bool
    {
        $cashBox = $this->getById($id);
        if ($cashBox) {
            // يجب إضافة منطق للتحقق من عدم وجود معاملات مرتبطة أو أرصدة قبل الحذف
            return $cashBox->delete();
        }
        return false;
    }

    /**
     * الحصول على صندوق نقدي بواسطة المعرف.
     *
     * @param int $id معرف الصندوق النقدي.
     * @return CashBox|null
     */
    public function getById(int $id): ?CashBox
    {
        return CashBox::find($id);
    }

    /**
     * الحصول على جميع الصناديق النقدية.
     *
     * @return Collection<int, CashBox>
     */
    public function getAll(): Collection
    {
        return CashBox::all();
    }

    /**
     * إضافة معاملة (إيداع أو سحب) إلى صندوق نقدي.
     *
     * @param int $cashBoxId معرف الصندوق النقدي.
     * @param array $transactionData بيانات المعاملة (مثل النوع، المبلغ، الوصف).
     * @return CashBoxTransaction
     * @throws \Exception إذا فشلت العملية.
     */
    public function addTransaction(int $cashBoxId, array $transactionData): CashBoxTransaction
    {
        $cashBox = $this->getById($cashBoxId);

        if (!$cashBox) {
            throw new \Exception("صندوق نقدي غير موجود.");
        }

        // يجب إضافة منطق للتحقق من الرصيد الكافي في حالة السحب
        // وتنفيذ العملية داخل معاملة قاعدة بيانات (DB Transaction) لضمان الاتساق.

        return DB::transaction(function () use ($cashBox, $transactionData) {
            $transaction = $cashBox->transactions()->create($transactionData);

            // تحديث رصيد الصندوق النقدي
            $amount = $transaction->amount;
            $type = $transaction->type; // افتراض أن 'type' يحدد ما إذا كانت العملية إيداع أو سحب

            if ($type === 'deposit') {
                $cashBox->balance += $amount;
            } elseif ($type === 'withdrawal') {
                $cashBox->balance -= $amount;
            }

            $cashBox->save();

            return $transaction;
        });
    }

    /**
     * الحصول على الرصيد الحالي لصندوق نقدي.
     *
     * @param int $id معرف الصندوق النقدي.
     * @return float
     */
    public function getBalance(int $id): float
    {
        $cashBox = $this->getById($id);
        return $cashBox ? (float) $cashBox->balance : 0.0;
    }

    /**
     * الحصول على سجل المعاملات لصندوق نقدي.
     *
     * @param int $id معرف الصندوق النقدي.
     * @return Collection<int, CashBoxTransaction>
     */
    public function getTransactions(int $id): Collection
    {
        $cashBox = $this->getById($id);
        return $cashBox ? $cashBox->transactions()->get() : Collection::make();
    }
}
