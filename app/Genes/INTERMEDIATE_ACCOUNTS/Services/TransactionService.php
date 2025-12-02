<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Services;

use App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateTransaction;
use App\Genes\INTERMEDIATE_ACCOUNTS\Models\TransactionLink;
use App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateAccount;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * TransactionService
 * 
 * خدمة لإدارة عمليات الحسابات الوسيطة.
 * تتضمن دوال لإنشاء العمليات، ربطها، توجيهها، وإدارة حالتها.
 * 
 * @version 1.0.0
 * @since 2025-12-02
 */
class TransactionService
{
    /**
     * @var IntermediateTransaction
     */
    protected IntermediateTransaction $model;

    /**
     * @var TransactionLink
     */
    protected TransactionLink $linkModel;

    /**
     * TransactionService constructor.
     *
     * @param IntermediateTransaction $model
     * @param TransactionLink $linkModel
     */
    public function __construct(
        IntermediateTransaction $model,
        TransactionLink $linkModel
    ) {
        $this->model = $model;
        $this->linkModel = $linkModel;
    }

    /**
     * إنشاء عملية جديدة.
     *
     * @param array $data البيانات المطلوبة لإنشاء العملية.
     * @return IntermediateTransaction العملية التي تم إنشاؤها.
     * @throws Exception في حالة فشل الإنشاء
     */
    public function create(array $data): IntermediateTransaction
    {
        try {
            DB::beginTransaction();
            
            // التحقق من وجود الحساب الوسيط
            $account = IntermediateAccount::findOrFail($data['intermediate_account_id']);
            
            // توليد رقم مرجعي تلقائي إذا لم يتم توفيره
            if (!isset($data['reference_number'])) {
                $data['reference_number'] = $this->generateReferenceNumber($data['type']);
            }
            
            // تعيين الحالة الافتراضية
            if (!isset($data['status'])) {
                $data['status'] = 'pending';
            }
            
            // تعيين تاريخ العملية الافتراضي
            if (!isset($data['transaction_date'])) {
                $data['transaction_date'] = now();
            }
            
            // تعيين المستخدم الحالي
            if (!isset($data['created_by'])) {
                $data['created_by'] = auth()->id() ?? 1;
            }
            
            $transaction = $this->model->create($data);
            
            DB::commit();
            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * ربط عمليتين (قيد مزدوج).
     * 
     * يتم ربط عملية مصدر (payment) بعملية هدف (receipt)
     * لتمثيل تحويل بين حسابين وسيطين.
     *
     * @param int $sourceId معرف العملية المصدر (payment).
     * @param int $targetId معرف العملية الهدف (receipt).
     * @param float $amount المبلغ المراد ربطه.
     * @param string|null $notes ملاحظات الربط.
     * @return TransactionLink الربط الذي تم إنشاؤه.
     * @throws Exception في حالة فشل الربط
     */
    public function link(
        int $sourceId,
        int $targetId,
        float $amount,
        ?string $notes = null
    ): TransactionLink {
        try {
            DB::beginTransaction();
            
            // التحقق من وجود العمليتين
            $sourceTransaction = $this->model->findOrFail($sourceId);
            $targetTransaction = $this->model->findOrFail($targetId);
            
            // التحقق من أن العملية المصدر من نوع payment
            if ($sourceTransaction->type !== 'payment') {
                throw new Exception("العملية المصدر يجب أن تكون من نوع payment");
            }
            
            // التحقق من أن العملية الهدف من نوع receipt
            if ($targetTransaction->type !== 'receipt') {
                throw new Exception("العملية الهدف يجب أن تكون من نوع receipt");
            }
            
            // التحقق من أن المبلغ لا يتجاوز مبلغ العملية
            if ($amount > $sourceTransaction->amount) {
                throw new Exception("المبلغ المراد ربطه يتجاوز مبلغ العملية المصدر");
            }
            
            if ($amount > $targetTransaction->amount) {
                throw new Exception("المبلغ المراد ربطه يتجاوز مبلغ العملية الهدف");
            }
            
            // إنشاء الربط
            $link = $this->linkModel->create([
                'source_transaction_id' => $sourceId,
                'target_transaction_id' => $targetId,
                'amount' => $amount,
                'notes' => $notes,
                'created_by' => auth()->id() ?? 1,
            ]);
            
            // تحديث حالة العمليات إلى completed إذا تم ربطها بالكامل
            $this->updateTransactionStatusAfterLink($sourceTransaction);
            $this->updateTransactionStatusAfterLink($targetTransaction);
            
            DB::commit();
            return $link;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * فك ربط عمليتين.
     *
     * @param int $linkId معرف الربط المراد فكه.
     * @return bool نتيجة عملية الفك.
     * @throws Exception في حالة فشل الفك
     */
    public function unlink(int $linkId): bool
    {
        try {
            DB::beginTransaction();
            
            $link = $this->linkModel->findOrFail($linkId);
            
            // الحصول على العمليات المرتبطة
            $sourceTransaction = $link->sourceTransaction;
            $targetTransaction = $link->targetTransaction;
            
            // حذف الربط
            $result = $link->delete();
            
            // تحديث حالة العمليات بعد فك الربط
            $this->updateTransactionStatusAfterUnlink($sourceTransaction);
            $this->updateTransactionStatusAfterUnlink($targetTransaction);
            
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * توجيه عملية إلى حساب وسيط.
     * 
     * يتم تحديث intermediate_account_id للعملية.
     *
     * @param int $transactionId معرف العملية.
     * @param int $accountId معرف الحساب الوسيط الجديد.
     * @return IntermediateTransaction العملية بعد التوجيه.
     * @throws Exception في حالة فشل التوجيه
     */
    public function allocate(int $transactionId, int $accountId): IntermediateTransaction
    {
        try {
            DB::beginTransaction();
            
            $transaction = $this->model->findOrFail($transactionId);
            $account = IntermediateAccount::findOrFail($accountId);
            
            $transaction->intermediate_account_id = $accountId;
            $transaction->save();
            
            DB::commit();
            return $transaction->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * تحديث حالة عملية.
     *
     * @param int $transactionId معرف العملية.
     * @param string $status الحالة الجديدة (pending/completed/cancelled).
     * @return IntermediateTransaction العملية بعد التحديث.
     * @throws Exception في حالة فشل التحديث
     */
    public function updateStatus(int $transactionId, string $status): IntermediateTransaction
    {
        try {
            DB::beginTransaction();
            
            // التحقق من صحة الحالة
            if (!in_array($status, ['pending', 'completed', 'cancelled'])) {
                throw new Exception("حالة غير صالحة: {$status}");
            }
            
            $transaction = $this->model->findOrFail($transactionId);
            $transaction->status = $status;
            $transaction->save();
            
            DB::commit();
            return $transaction->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * الحصول على العمليات غير المربوطة.
     *
     * @param int|null $accountId معرف الحساب الوسيط (اختياري).
     * @return Collection مجموعة من العمليات غير المربوطة.
     */
    public function getUnlinkedTransactions(?int $accountId = null): Collection
    {
        $query = $this->model->query()
            ->whereDoesntHave('sourceLinks')
            ->whereDoesntHave('targetLinks')
            ->where('status', 'pending');
        
        if ($accountId) {
            $query->where('intermediate_account_id', $accountId);
        }
        
        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * الحصول على العمليات المربوطة.
     *
     * @param int|null $accountId معرف الحساب الوسيط (اختياري).
     * @return Collection مجموعة من العمليات المربوطة.
     */
    public function getLinkedTransactions(?int $accountId = null): Collection
    {
        $query = $this->model->query()
            ->where(function($q) {
                $q->whereHas('sourceLinks')
                  ->orWhereHas('targetLinks');
            });
        
        if ($accountId) {
            $query->where('intermediate_account_id', $accountId);
        }
        
        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * توليد رقم مرجعي تلقائي للعملية.
     *
     * @param string $type نوع العملية (receipt/payment).
     * @return string الرقم المرجعي.
     */
    protected function generateReferenceNumber(string $type): string
    {
        $prefix = $type === 'receipt' ? 'REC' : 'PAY';
        $date = now()->format('Ymd');
        $count = $this->model->whereDate('created_at', now())->count() + 1;
        
        return sprintf('%s-%s-%05d', $prefix, $date, $count);
    }

    /**
     * تحديث حالة العملية بعد الربط.
     * 
     * إذا تم ربط العملية بالكامل، يتم تحديث حالتها إلى completed.
     *
     * @param IntermediateTransaction $transaction
     * @return void
     */
    protected function updateTransactionStatusAfterLink(IntermediateTransaction $transaction): void
    {
        // حساب مجموع المبالغ المربوطة
        $linkedAmount = $transaction->sourceLinks()->sum('amount') 
                      + $transaction->targetLinks()->sum('amount');
        
        // إذا تم ربط المبلغ بالكامل، تحديث الحالة
        if ($linkedAmount >= $transaction->amount) {
            $transaction->status = 'completed';
            $transaction->save();
        }
    }

    /**
     * تحديث حالة العملية بعد فك الربط.
     * 
     * إذا لم يعد هناك روابط، يتم تحديث الحالة إلى pending.
     *
     * @param IntermediateTransaction $transaction
     * @return void
     */
    protected function updateTransactionStatusAfterUnlink(IntermediateTransaction $transaction): void
    {
        // التحقق من وجود روابط أخرى
        $hasLinks = $transaction->sourceLinks()->exists() 
                 || $transaction->targetLinks()->exists();
        
        // إذا لم يعد هناك روابط، تحديث الحالة إلى pending
        if (!$hasLinks && $transaction->status === 'completed') {
            $transaction->status = 'pending';
            $transaction->save();
        }
    }

    /**
     * الحصول على تفاصيل عملية مع الروابط.
     *
     * @param int $transactionId معرف العملية.
     * @return array تفاصيل العملية.
     * @throws Exception في حالة عدم العثور على العملية
     */
    public function getTransactionDetails(int $transactionId): array
    {
        $transaction = $this->model->with([
            'intermediateAccount',
            'sourceLinks.targetTransaction.intermediateAccount',
            'targetLinks.sourceTransaction.intermediateAccount'
        ])->findOrFail($transactionId);
        
        $linkedAmount = $transaction->sourceLinks->sum('amount') 
                      + $transaction->targetLinks->sum('amount');
        
        return [
            'transaction' => $transaction,
            'linked_amount' => $linkedAmount,
            'remaining_amount' => $transaction->amount - $linkedAmount,
            'is_fully_linked' => $linkedAmount >= $transaction->amount,
            'source_links_count' => $transaction->sourceLinks->count(),
            'target_links_count' => $transaction->targetLinks->count(),
        ];
    }
}
