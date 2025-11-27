<?php

namespace App\Genes\CASHIERS\Services;

use App\Genes\CASHIERS\Models\Cashier;
use App\Genes\CASHIERS\Models\CashierTransaction;
use App\Genes\CASHIERS\Models\CashierSettlement;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * خدمة إدارة الصرافين
 */
class CashierService
{
    /**
     * إنشاء صراف جديد
     */
    public function createCashier(array $data)
    {
        DB::beginTransaction();
        try {
            // توليد الكود تلقائياً
            if (!isset($data['code'])) {
                $data['code'] = $this->generateCashierCode();
            }

            // إنشاء الصراف
            $cashier = Cashier::create($data);

            // تسجيل الرصيد الافتتاحي كمعاملة
            if ($cashier->opening_balance > 0) {
                $this->recordOpeningBalance($cashier);
            }

            DB::commit();
            return $cashier;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * تحديث بيانات الصراف
     */
    public function updateCashier($id, array $data)
    {
        $cashier = Cashier::findOrFail($id);
        $cashier->update($data);
        return $cashier->fresh();
    }

    /**
     * حذف صراف
     */
    public function deleteCashier($id)
    {
        $cashier = Cashier::findOrFail($id);
        
        // التحقق من عدم وجود معاملات معلقة
        $pendingTransactions = $cashier->transactions()
            ->where('status', 'pending')
            ->count();

        if ($pendingTransactions > 0) {
            throw new Exception('لا يمكن حذف الصراف لوجود معاملات معلقة');
        }

        return $cashier->delete();
    }

    /**
     * الحصول على صراف بالتفاصيل
     */
    public function getCashierWithDetails($id)
    {
        return Cashier::with([
            'entity',
            'branch',
            'user',
            'safe',
            'transactions' => function($q) {
                $q->latest()->limit(10);
            },
            'settlements' => function($q) {
                $q->latest()->limit(5);
            }
        ])->findOrFail($id);
    }

    /**
     * الحصول على قائمة الصرافين
     */
    public function getCashiers(array $filters = [])
    {
        $query = Cashier::query();

        if (isset($filters['entity_id'])) {
            $query->byEntity($filters['entity_id']);
        }

        if (isset($filters['branch_id'])) {
            $query->byBranch($filters['branch_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('code', 'like', "%{$filters['search']}%")
                  ->orWhere('name', 'like', "%{$filters['search']}%");
            });
        }

        return $query->with(['entity', 'branch', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * تسجيل الرصيد الافتتاحي
     */
    private function recordOpeningBalance(Cashier $cashier)
    {
        return CashierTransaction::create([
            'code' => $this->generateTransactionCode(),
            'cashier_id' => $cashier->id,
            'entity_id' => $cashier->entity_id,
            'transaction_type' => 'deposit',
            'amount' => $cashier->opening_balance,
            'amount_in_base_currency' => $cashier->opening_balance,
            'description' => 'رصيد افتتاحي',
            'transaction_date' => now(),
            'created_by' => auth()->id(),
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }

    /**
     * توليد كود الصراف
     */
    private function generateCashierCode()
    {
        $lastCashier = Cashier::latest('id')->first();
        $number = $lastCashier ? (int)substr($lastCashier->code, 4) + 1 : 1;
        return 'CSH-' . str_pad($number, 6, '0', STR_PAD_LEFT);
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
     * الحصول على إحصائيات الصراف
     */
    public function getCashierStatistics($cashierId, $from = null, $to = null)
    {
        $cashier = Cashier::findOrFail($cashierId);
        
        $query = $cashier->transactions()->where('status', 'approved');
        
        if ($from && $to) {
            $query->byDateRange($from, $to);
        }

        $transactions = $query->get();

        return [
            'cashier' => $cashier,
            'current_balance' => $cashier->current_balance,
            'total_deposits' => $transactions->where('transaction_type', 'deposit')->sum('amount'),
            'total_withdrawals' => $transactions->where('transaction_type', 'withdrawal')->sum('amount'),
            'total_transfers' => $transactions->where('transaction_type', 'transfer')->sum('amount'),
            'transaction_count' => $transactions->count(),
            'avg_transaction' => $transactions->avg('amount'),
        ];
    }
}
