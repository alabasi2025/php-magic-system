<?php

namespace App\Genes\CASHIERS\Services;

use App\Genes\CASHIERS\Models\Cashier;
use App\Genes\CASHIERS\Models\CashierTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * خدمة إدارة معاملات الصرافين
 */
class CashierTransactionService
{
    /**
     * إنشاء معاملة جديدة
     */
    public function createTransaction(array $data)
    {
        DB::beginTransaction();
        try {
            $cashier = Cashier::findOrFail($data['cashier_id']);

            // التحقق من الحدود
            $this->validateTransactionLimits($cashier, $data);

            // توليد الكود
            if (!isset($data['code'])) {
                $data['code'] = $this->generateTransactionCode();
            }

            // إنشاء المعاملة
            $transaction = CashierTransaction::create($data);

            DB::commit();
            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * اعتماد معاملة
     */
    public function approveTransaction($id)
    {
        DB::beginTransaction();
        try {
            $transaction = CashierTransaction::findOrFail($id);

            if ($transaction->status !== 'pending') {
                throw new Exception('المعاملة ليست في حالة الانتظار');
            }

            $cashier = $transaction->cashier;

            // تحديث رصيد الصراف
            if ($transaction->transaction_type === 'deposit') {
                $cashier->current_balance += $transaction->amount;
            } elseif ($transaction->transaction_type === 'withdrawal') {
                if ($cashier->current_balance < $transaction->amount) {
                    throw new Exception('الرصيد غير كافٍ');
                }
                $cashier->current_balance -= $transaction->amount;
            }

            $cashier->save();

            // تحديث حالة المعاملة
            $transaction->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            DB::commit();
            return $transaction->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * رفض معاملة
     */
    public function rejectTransaction($id, $reason = null)
    {
        $transaction = CashierTransaction::findOrFail($id);

        if ($transaction->status !== 'pending') {
            throw new Exception('المعاملة ليست في حالة الانتظار');
        }

        $transaction->update([
            'status' => 'rejected',
            'notes' => $reason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return $transaction;
    }

    /**
     * إلغاء معاملة
     */
    public function cancelTransaction($id)
    {
        DB::beginTransaction();
        try {
            $transaction = CashierTransaction::findOrFail($id);

            if ($transaction->status === 'cancelled') {
                throw new Exception('المعاملة ملغاة بالفعل');
            }

            if ($transaction->status === 'approved') {
                // عكس المعاملة
                $this->reverseTransaction($transaction);
            }

            $transaction->update(['status' => 'cancelled']);

            DB::commit();
            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * الحصول على معاملات الصراف
     */
    public function getTransactions(array $filters = [])
    {
        $query = CashierTransaction::query();

        if (isset($filters['cashier_id'])) {
            $query->byCashier($filters['cashier_id']);
        }

        if (isset($filters['entity_id'])) {
            $query->where('entity_id', $filters['entity_id']);
        }

        if (isset($filters['transaction_type'])) {
            $query->byType($filters['transaction_type']);
        }

        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['from']) && isset($filters['to'])) {
            $query->byDateRange($filters['from'], $filters['to']);
        }

        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('code', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        return $query->with(['cashier', 'entity', 'creator', 'approver'])
            ->orderBy('transaction_date', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * التحقق من حدود المعاملة
     */
    private function validateTransactionLimits(Cashier $cashier, array $data)
    {
        // التحقق من الحد الأقصى للمعاملة
        if ($cashier->max_transaction_limit && $data['amount'] > $cashier->max_transaction_limit) {
            throw new Exception('المبلغ يتجاوز الحد الأقصى للمعاملة');
        }

        // التحقق من الحد اليومي
        if ($cashier->daily_limit) {
            $todayTotal = CashierTransaction::where('cashier_id', $cashier->id)
                ->whereDate('transaction_date', today())
                ->where('status', 'approved')
                ->sum('amount');

            if (($todayTotal + $data['amount']) > $cashier->daily_limit) {
                throw new Exception('المبلغ يتجاوز الحد اليومي');
            }
        }

        // التحقق من الرصيد للسحب
        if ($data['transaction_type'] === 'withdrawal') {
            if ($cashier->current_balance < $data['amount']) {
                throw new Exception('الرصيد غير كافٍ');
            }
        }
    }

    /**
     * عكس معاملة معتمدة
     */
    private function reverseTransaction(CashierTransaction $transaction)
    {
        $cashier = $transaction->cashier;

        if ($transaction->transaction_type === 'deposit') {
            $cashier->current_balance -= $transaction->amount;
        } elseif ($transaction->transaction_type === 'withdrawal') {
            $cashier->current_balance += $transaction->amount;
        }

        $cashier->save();
    }

    /**
     * توليد كود المعاملة
     */
    private function generateTransactionCode()
    {
        $lastTransaction = CashierTransaction::latest('id')->first();
        $number = $lastTransaction ? (int)substr($lastTransaction->code, 4) + 1 : 1;
        return 'CTR-' . str_pad($number, 8, '0', STR_PAD_LEFT);
    }

    /**
     * تقرير المعاملات
     */
    public function getTransactionReport(array $filters)
    {
        $query = CashierTransaction::query()->where('status', 'approved');

        if (isset($filters['cashier_id'])) {
            $query->byCashier($filters['cashier_id']);
        }

        if (isset($filters['from']) && isset($filters['to'])) {
            $query->byDateRange($filters['from'], $filters['to']);
        }

        $transactions = $query->get();

        return [
            'total_transactions' => $transactions->count(),
            'total_deposits' => $transactions->where('transaction_type', 'deposit')->sum('amount'),
            'total_withdrawals' => $transactions->where('transaction_type', 'withdrawal')->sum('amount'),
            'total_transfers' => $transactions->where('transaction_type', 'transfer')->sum('amount'),
            'net_amount' => $transactions->where('transaction_type', 'deposit')->sum('amount') - 
                           $transactions->where('transaction_type', 'withdrawal')->sum('amount'),
        ];
    }
}
