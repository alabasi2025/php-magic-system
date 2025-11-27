<?php

namespace App\Genes\Cashiers\BusinessLogic;

use App\Models\CashierTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessCashierTransactionAction
 *
 * هذا الإجراء مسؤول عن معالجة معاملة صراف (Cashier Transaction) جديدة.
 * يتضمن ذلك التحقق من صحة البيانات، تسجيل المعاملة، وتحديث أرصدة الصناديق/الحسابات ذات الصلة.
 *
 * Task 2063: [نظام الصرافين (Cashiers)] Business Logic - Task 3
 *
 * @package App\Genes\Cashiers\BusinessLogic
 */
class ProcessCashierTransactionAction
{
    /**
     * ينفذ الإجراء لمعالجة المعاملة.
     *
     * @param array $data البيانات المطلوبة لإنشاء المعاملة.
     * @return CashierTransaction المعاملة التي تم إنشاؤها.
     * @throws \Exception في حالة فشل أي خطوة من خطوات المعالجة.
     */
    public function execute(array $data): CashierTransaction
    {
        // 1. التحقق من صحة البيانات الأساسية (يفترض أن يتم التحقق المسبق في طبقة أعلى، ولكن يتم هنا التحقق من وجود المفاتيح الأساسية)
        if (!isset($data['cashier_id'], $data['amount'], $data['type'], $data['description'])) {
            Log::error('Cashier transaction data is incomplete.', $data);
            throw new \InvalidArgumentException('بيانات المعاملة غير مكتملة.');
        }

        DB::beginTransaction();

        try {
            // 2. تسجيل المعاملة في قاعدة البيانات
            $transaction = CashierTransaction::create([
                'cashier_id' => $data['cashier_id'],
                'amount' => $data['amount'],
                'type' => $data['type'], // مثلاً: 'deposit', 'withdrawal', 'transfer'
                'description' => $data['description'],
                'status' => 'completed', // الحالة الافتراضية
                'transaction_date' => $data['transaction_date'] ?? now(),
                // يمكن إضافة حقول أخرى مثل user_id, related_account_id
            ]);

            // 3. تحديث أرصدة الصناديق/الحسابات ذات الصلة (منطق عمل افتراضي)
            // هذا الجزء يحتاج إلى نماذج (Models) إضافية مثل CashBox أو Account
            // سنفترض وجود منطق تحديث الرصيد هنا.
            $this->updateRelatedBalances($transaction);

            DB::commit();

            Log::info('Cashier transaction processed successfully.', ['transaction_id' => $transaction->id]);

            return $transaction;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process cashier transaction.', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw new \Exception('فشل في معالجة معاملة الصراف: ' . $e->getMessage());
        }
    }

    /**
     * وظيفة مساعدة لتحديث أرصدة الصناديق/الحسابات ذات الصلة.
     * (يجب استبدالها بمنطق حقيقي يعتمد على بنية قاعدة البيانات والنماذج)
     *
     * @param CashierTransaction $transaction
     * @return void
     */
    protected function updateRelatedBalances(CashierTransaction $transaction): void
    {
        // مثال: إذا كان نوع المعاملة 'deposit'، قم بزيادة رصيد الصندوق.
        // إذا كان نوع المعاملة 'withdrawal'، قم بإنقاص رصيد الصندوق.

        // Log::info('Updating balances for transaction.', ['transaction_id' => $transaction->id]);

        // // مثال افتراضي:
        // $cashBox = CashBox::find($transaction->cashier_id); // افتراض أن cashier_id هو نفسه cash_box_id
        // if ($transaction->type === 'deposit') {
        //     $cashBox->balance += $transaction->amount;
        // } elseif ($transaction->type === 'withdrawal') {
        //     $cashBox->balance -= $transaction->amount;
        // }
        // $cashBox->save();

        // في نظام حقيقي، يجب أن يتم هذا التحديث من خلال نظام محاسبي مزدوج القيد (Double-Entry Accounting System)
        // لضمان الدقة والاتساق المالي.
    }
}