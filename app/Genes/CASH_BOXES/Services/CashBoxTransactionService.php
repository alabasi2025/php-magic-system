<?php

namespace App\Genes\CASH_BOXES\Services;

use App\Genes\CASH_BOXES\Models\CashBox;
use App\Genes\CASH_BOXES\Models\CashBoxTransaction;
use App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateTransaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * CashBoxTransactionService
 * 
 * خدمة إدارة عمليات الصناديق النقدية.
 * تتضمن دوال لإنشاء العمليات، مزامنتها مع الحسابات الوسيطة،
 * وإدارة حالتها.
 * 
 * @version 1.0.0
 * @since 2025-12-02
 */
class CashBoxTransactionService
{
    /**
     * @var CashBoxTransaction
     */
    protected CashBoxTransaction $model;

    /**
     * CashBoxTransactionService constructor.
     *
     * @param CashBoxTransaction $model
     */
    public function __construct(CashBoxTransaction $model)
    {
        $this->model = $model;
    }

    /**
     * إنشاء عملية على صندوق نقدي.
     * 
     * إذا كان الصندوق مرتبط بحساب وسيط، يتم تسجيل العملية
     * في intermediate_transactions أيضاً.
     *
     * @param array $data بيانات العملية.
     * @return CashBoxTransaction العملية التي تم إنشاؤها.
     * @throws Exception في حالة فشل الإنشاء
     */
    public function create(array $data): CashBoxTransaction
    {
        try {
            DB::beginTransaction();
            
            // التحقق من وجود الصندوق
            $cashBox = CashBox::findOrFail($data['cash_box_id']);
            
            // التحقق من نوع العملية
            if (!in_array($data['type'], ['deposit', 'withdrawal'])) {
                throw new Exception("نوع العملية غير صالح: {$data['type']}");
            }
            
            // التحقق من الرصيد في حالة السحب
            if ($data['type'] === 'withdrawal' && $data['amount'] > $cashBox->balance) {
                throw new Exception("الرصيد غير كافٍ للسحب");
            }
            
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
            
            // إنشاء العملية
            $transaction = $this->model->create($data);
            
            // تحديث رصيد الصندوق إذا كانت العملية مكتملة
            if ($transaction->status === 'completed') {
                $this->updateCashBoxBalance($cashBox, $transaction);
            }
            
            // مزامنة مع الحساب الوسيط (إذا كان مرتبط)
            if ($cashBox->intermediate_account_id) {
                $this->syncToIntermediateAccount($transaction);
            }
            
            DB::commit();
            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * تحديث عملية موجودة.
     *
     * @param int $id معرف العملية.
     * @param array $data البيانات الجديدة.
     * @return CashBoxTransaction العملية بعد التحديث.
     * @throws Exception في حالة فشل التحديث
     */
    public function update(int $id, array $data): CashBoxTransaction
    {
        try {
            DB::beginTransaction();
            
            $transaction = $this->model->findOrFail($id);
            $oldStatus = $transaction->status;
            
            $transaction->update($data);
            
            // إذا تغيرت الحالة إلى completed، تحديث الرصيد
            if ($oldStatus !== 'completed' && $transaction->status === 'completed') {
                $this->updateCashBoxBalance($transaction->cashBox, $transaction);
            }
            
            DB::commit();
            return $transaction->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * حذف عملية.
     *
     * @param int $id معرف العملية.
     * @return bool نتيجة عملية الحذف.
     * @throws Exception في حالة فشل الحذف
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();
            
            $transaction = $this->model->findOrFail($id);
            
            // التحقق من أن العملية غير مكتملة
            if ($transaction->status === 'completed') {
                throw new Exception("لا يمكن حذف عملية مكتملة");
            }
            
            $result = $transaction->delete();
            
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * تحديث حالة عملية.
     *
     * @param int $id معرف العملية.
     * @param string $status الحالة الجديدة (pending/completed/cancelled).
     * @return CashBoxTransaction العملية بعد التحديث.
     * @throws Exception في حالة فشل التحديث
     */
    public function updateStatus(int $id, string $status): CashBoxTransaction
    {
        try {
            DB::beginTransaction();
            
            // التحقق من صحة الحالة
            if (!in_array($status, ['pending', 'completed', 'cancelled'])) {
                throw new Exception("حالة غير صالحة: {$status}");
            }
            
            $transaction = $this->model->findOrFail($id);
            $oldStatus = $transaction->status;
            
            $transaction->status = $status;
            $transaction->save();
            
            // تحديث الرصيد عند تغيير الحالة
            if ($oldStatus !== 'completed' && $status === 'completed') {
                $this->updateCashBoxBalance($transaction->cashBox, $transaction);
            } elseif ($oldStatus === 'completed' && $status !== 'completed') {
                $this->reverseCashBoxBalance($transaction->cashBox, $transaction);
            }
            
            DB::commit();
            return $transaction->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * مزامنة عملية صندوق إلى الحساب الوسيط.
     * 
     * يتم إنشاء عملية مقابلة في intermediate_transactions.
     *
     * @param CashBoxTransaction $transaction
     * @return IntermediateTransaction|null العملية الوسيطة المُنشأة.
     * @throws Exception في حالة فشل المزامنة
     */
    public function syncToIntermediateAccount(CashBoxTransaction $transaction): ?IntermediateTransaction
    {
        try {
            // التحقق من أن الصندوق مرتبط بحساب وسيط
            if (!$transaction->cashBox->intermediate_account_id) {
                return null;
            }
            
            // تحديد نوع العملية في الحساب الوسيط
            // deposit في الصندوق = receipt في الحساب الوسيط
            // withdrawal في الصندوق = payment في الحساب الوسيط
            $intermediateType = $transaction->type === 'deposit' ? 'receipt' : 'payment';
            
            // إنشاء عملية في الحساب الوسيط
            $intermediateTransaction = IntermediateTransaction::create([
                'intermediate_account_id' => $transaction->cashBox->intermediate_account_id,
                'type' => $intermediateType,
                'amount' => $transaction->amount,
                'status' => $transaction->status,
                'description' => "مزامنة من صندوق {$transaction->cashBox->name}: {$transaction->description}",
                'reference_number' => $transaction->reference_number,
                'transaction_date' => $transaction->transaction_date,
                'created_by' => $transaction->created_by,
            ]);
            
            // حفظ معرف العملية الوسيطة في العملية الأصلية (إذا كان الحقل موجود)
            if (schema()->hasColumn('alabasi_cash_box_transactions', 'intermediate_transaction_id')) {
                $transaction->intermediate_transaction_id = $intermediateTransaction->id;
                $transaction->save();
            }
            
            return $intermediateTransaction;
        } catch (Exception $e) {
            throw new Exception("فشلت مزامنة العملية مع الحساب الوسيط: " . $e->getMessage());
        }
    }

    /**
     * تحديث رصيد الصندوق بعد عملية.
     *
     * @param CashBox $cashBox
     * @param CashBoxTransaction $transaction
     * @return void
     */
    protected function updateCashBoxBalance(CashBox $cashBox, CashBoxTransaction $transaction): void
    {
        if ($transaction->type === 'deposit') {
            $cashBox->balance += $transaction->amount;
        } elseif ($transaction->type === 'withdrawal') {
            $cashBox->balance -= $transaction->amount;
        }
        
        $cashBox->save();
    }

    /**
     * عكس تأثير عملية على رصيد الصندوق.
     *
     * @param CashBox $cashBox
     * @param CashBoxTransaction $transaction
     * @return void
     */
    protected function reverseCashBoxBalance(CashBox $cashBox, CashBoxTransaction $transaction): void
    {
        if ($transaction->type === 'deposit') {
            $cashBox->balance -= $transaction->amount;
        } elseif ($transaction->type === 'withdrawal') {
            $cashBox->balance += $transaction->amount;
        }
        
        $cashBox->save();
    }

    /**
     * توليد رقم مرجعي تلقائي للعملية.
     *
     * @param string $type نوع العملية (deposit/withdrawal).
     * @return string الرقم المرجعي.
     */
    protected function generateReferenceNumber(string $type): string
    {
        $prefix = $type === 'deposit' ? 'DEP' : 'WTH';
        $date = now()->format('Ymd');
        $count = $this->model->whereDate('created_at', now())->count() + 1;
        
        return sprintf('%s-%s-%05d', $prefix, $date, $count);
    }

    /**
     * الحصول على عمليات صندوق نقدي.
     *
     * @param int $cashBoxId معرف الصندوق.
     * @param string|null $type نوع العملية (اختياري).
     * @param string|null $status حالة العملية (اختياري).
     * @return Collection مجموعة من العمليات.
     */
    public function getTransactions(
        int $cashBoxId,
        ?string $type = null,
        ?string $status = null
    ): Collection {
        $query = $this->model->where('cash_box_id', $cashBoxId);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * الحصول على إحصائيات العمليات لصندوق.
     *
     * @param int $cashBoxId معرف الصندوق.
     * @param string|null $startDate تاريخ البداية (اختياري).
     * @param string|null $endDate تاريخ النهاية (اختياري).
     * @return array مصفوفة تحتوي على الإحصائيات.
     */
    public function getStatistics(
        int $cashBoxId,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $query = $this->model->where('cash_box_id', $cashBoxId);
        
        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }
        
        $transactions = $query->get();
        
        return [
            'total_deposits' => $transactions->where('type', 'deposit')->where('status', 'completed')->sum('amount'),
            'total_withdrawals' => $transactions->where('type', 'withdrawal')->where('status', 'completed')->sum('amount'),
            'deposits_count' => $transactions->where('type', 'deposit')->count(),
            'withdrawals_count' => $transactions->where('type', 'withdrawal')->count(),
            'pending_count' => $transactions->where('status', 'pending')->count(),
            'completed_count' => $transactions->where('status', 'completed')->count(),
            'cancelled_count' => $transactions->where('status', 'cancelled')->count(),
        ];
    }
}
