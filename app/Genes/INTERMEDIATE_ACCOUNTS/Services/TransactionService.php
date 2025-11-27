<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Services;

use App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateAccount;
use App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateTransaction;
use App\Genes\INTERMEDIATE_ACCOUNTS\Models\TransactionLink;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Service: TransactionService
 * 
 * 💡 الفكرة:
 * خدمة تدير جميع عمليات الحساب الوسيط والربط بينها.
 * 
 * 🎯 المسؤوليات:
 * - تسجيل عمليات القبض والصرف في الحساب الوسيط
 * - ربط العمليات المعاكسة (قبض ← صرف)
 * - التحقق من صحة الربط
 * - حساب الأرصدة المعلقة
 * 
 * 📊 أنواع العمليات:
 * - receipt: قبض (دائن) - يزيد رصيد الحساب الوسيط
 * - payment: صرف (مدين) - ينقص رصيد الحساب الوسيط
 * 
 * 🔗 قواعد الربط:
 * 1. يمكن ربط عملية واحدة بعدة عمليات معاكسة
 * 2. مجموع المبالغ المرتبطة يجب أن يكون متساوياً
 * 3. لا يمكن ربط عمليتين من نفس النوع
 * 4. المبلغ المرتبط يجب ألا يتجاوز المبلغ المتاح
 * 
 * @version 1.0.0
 * @since 2025-11-27
 */
class TransactionService
{
    /**
     * Record a new transaction (receipt or payment).
     *
     * @param int $intermediateAccountId
     * @param string $type (receipt|payment)
     * @param float $amount
     * @param string $description
     * @param string|null $referenceNumber
     * @param array|null $metadata
     * @return IntermediateTransaction
     * @throws Exception
     */
    public function recordTransaction(
        int $intermediateAccountId,
        string $type,
        float $amount,
        string $description,
        ?string $referenceNumber = null,
        ?array $metadata = null
    ): IntermediateTransaction {
        DB::beginTransaction();
        
        try {
            // التحقق من الحساب الوسيط
            $intermediateAccount = IntermediateAccount::findOrFail($intermediateAccountId);
            
            if (!$intermediateAccount->isActive()) {
                throw new Exception('الحساب الوسيط غير نشط');
            }
            
            // التحقق من نوع العملية
            if (!in_array($type, ['receipt', 'payment'])) {
                throw new Exception('نوع العملية غير صحيح');
            }
            
            // التحقق من المبلغ
            if ($amount <= 0) {
                throw new Exception('المبلغ يجب أن يكون أكبر من صفر');
            }
            
            // إنشاء العملية
            $transaction = IntermediateTransaction::create([
                'intermediate_account_id' => $intermediateAccountId,
                'type' => $type,
                'amount' => $amount,
                'description' => $description,
                'reference_number' => $referenceNumber,
                'metadata' => $metadata,
                'status' => 'pending',
                'transaction_date' => now(),
                'created_by' => Auth::id(),
            ]);
            
            DB::commit();
            
            return $transaction;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Link two or more transactions together.
     *
     * @param array $links Format: [['source_id' => 1, 'target_id' => 2, 'amount' => 100], ...]
     * @param string|null $notes
     * @return array
     * @throws Exception
     */
    public function linkTransactions(array $links, ?string $notes = null): array
    {
        DB::beginTransaction();
        
        try {
            $createdLinks = [];
            
            foreach ($links as $link) {
                $sourceId = $link['source_id'];
                $targetId = $link['target_id'];
                $amount = $link['amount'];
                
                // التحقق من العمليات
                $source = IntermediateTransaction::findOrFail($sourceId);
                $target = IntermediateTransaction::findOrFail($targetId);
                
                // التحقق من أن العمليات من نفس الحساب الوسيط
                if ($source->intermediate_account_id !== $target->intermediate_account_id) {
                    throw new Exception('العمليات يجب أن تكون من نفس الحساب الوسيط');
                }
                
                // التحقق من أن العمليات معاكسة
                if ($source->type === $target->type) {
                    throw new Exception('لا يمكن ربط عمليتين من نفس النوع');
                }
                
                // التحقق من المبلغ المتاح
                $sourceAvailable = $source->getAvailableAmount();
                $targetAvailable = $target->getAvailableAmount();
                
                if ($amount > $sourceAvailable) {
                    throw new Exception("المبلغ المراد ربطه أكبر من المبلغ المتاح في العملية المصدر (متاح: {$sourceAvailable})");
                }
                
                if ($amount > $targetAvailable) {
                    throw new Exception("المبلغ المراد ربطه أكبر من المبلغ المتاح في العملية الهدف (متاح: {$targetAvailable})");
                }
                
                // إنشاء الربط
                $transactionLink = TransactionLink::create([
                    'source_transaction_id' => $sourceId,
                    'target_transaction_id' => $targetId,
                    'linked_amount' => $amount,
                    'linked_at' => now(),
                    'linked_by' => Auth::id(),
                    'notes' => $notes,
                ]);
                
                // تحديث حالة العمليات
                $source->updateStatus();
                $target->updateStatus();
                
                $createdLinks[] = $transactionLink;
            }
            
            DB::commit();
            
            return $createdLinks;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Unlink a transaction link.
     *
     * @param int $linkId
     * @return bool
     * @throws Exception
     */
    public function unlinkTransactions(int $linkId): bool
    {
        DB::beginTransaction();
        
        try {
            $link = TransactionLink::findOrFail($linkId);
            
            $sourceTransaction = $link->sourceTransaction;
            $targetTransaction = $link->targetTransaction;
            
            // حذف الربط
            $link->delete();
            
            // تحديث حالة العمليات
            $sourceTransaction->updateStatus();
            $targetTransaction->updateStatus();
            
            DB::commit();
            
            return true;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Auto-link transactions (smart matching).
     *
     * @param int $intermediateAccountId
     * @param array $options
     * @return array
     */
    public function autoLinkTransactions(int $intermediateAccountId, array $options = []): array
    {
        DB::beginTransaction();
        
        try {
            $intermediateAccount = IntermediateAccount::findOrFail($intermediateAccountId);
            
            // الحصول على العمليات المعلقة
            $receipts = $intermediateAccount->getUnlinkedTransactions('receipt');
            $payments = $intermediateAccount->getUnlinkedTransactions('payment');
            
            $links = [];
            
            // محاولة ربط العمليات تلقائياً
            foreach ($receipts as $receipt) {
                $availableAmount = $receipt->getAvailableAmount();
                
                if ($availableAmount <= 0) {
                    continue;
                }
                
                foreach ($payments as $payment) {
                    $paymentAvailable = $payment->getAvailableAmount();
                    
                    if ($paymentAvailable <= 0) {
                        continue;
                    }
                    
                    // ربط بالمبلغ الأصغر
                    $linkAmount = min($availableAmount, $paymentAvailable);
                    
                    $link = TransactionLink::create([
                        'source_transaction_id' => $receipt->id,
                        'target_transaction_id' => $payment->id,
                        'linked_amount' => $linkAmount,
                        'linked_at' => now(),
                        'linked_by' => Auth::id(),
                        'notes' => 'ربط تلقائي',
                    ]);
                    
                    $links[] = $link;
                    
                    $availableAmount -= $linkAmount;
                    
                    // تحديث حالة العمليات
                    $receipt->updateStatus();
                    $payment->updateStatus();
                    
                    if ($availableAmount <= 0) {
                        break;
                    }
                }
            }
            
            DB::commit();
            
            return $links;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get unlinked transactions for an intermediate account.
     *
     * @param int $intermediateAccountId
     * @param string|null $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnlinkedTransactions(int $intermediateAccountId, ?string $type = null)
    {
        $intermediateAccount = IntermediateAccount::findOrFail($intermediateAccountId);
        return $intermediateAccount->getUnlinkedTransactions($type);
    }

    /**
     * Get transaction details with links.
     *
     * @param int $transactionId
     * @return IntermediateTransaction
     */
    public function getTransactionWithLinks(int $transactionId): IntermediateTransaction
    {
        return IntermediateTransaction::with(['sourceLinks.targetTransaction', 'targetLinks.sourceTransaction'])
            ->findOrFail($transactionId);
    }

    /**
     * Cancel a transaction.
     *
     * @param int $transactionId
     * @return bool
     * @throws Exception
     */
    public function cancelTransaction(int $transactionId): bool
    {
        DB::beginTransaction();
        
        try {
            $transaction = IntermediateTransaction::findOrFail($transactionId);
            
            // التحقق من عدم وجود روابط
            if ($transaction->getLinkedAmount() > 0) {
                throw new Exception('لا يمكن إلغاء العملية لوجود روابط. يجب فك الربط أولاً');
            }
            
            $transaction->status = 'cancelled';
            $transaction->save();
            
            DB::commit();
            
            return true;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get balance summary for an intermediate account.
     *
     * @param int $intermediateAccountId
     * @return array
     */
    public function getBalanceSummary(int $intermediateAccountId): array
    {
        $intermediateAccount = IntermediateAccount::findOrFail($intermediateAccountId);
        
        return [
            'total_receipts' => $intermediateAccount->getTotalReceipts(),
            'total_payments' => $intermediateAccount->getTotalPayments(),
            'current_balance' => $intermediateAccount->getCurrentBalance(),
            'unlinked_receipts' => $intermediateAccount->getUnlinkedAmount('receipt'),
            'unlinked_payments' => $intermediateAccount->getUnlinkedAmount('payment'),
            'is_balanced' => $intermediateAccount->isBalanced(),
            'unlinked_count' => $intermediateAccount->getUnlinkedTransactionsCount(),
        ];
    }
}
