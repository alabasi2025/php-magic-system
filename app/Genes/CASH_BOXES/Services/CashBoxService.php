<?php

namespace App\Genes\CASH_BOXES\Services;

use App\Genes\CASH_BOXES\Models\CashBox;
use App\Genes\CASH_BOXES\Models\CashBoxTransaction;
use App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateAccount;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * CashBoxService
 * 
 * خدمة إدارة الصناديق النقدية والمعاملات المتعلقة بها.
 * تتضمن دوال لإنشاء الصناديق، ربطها بالحسابات الوسيطة،
 * وإدارة العمليات المالية عليها.
 *
 * @version 2.0.0
 * @since 2025-12-02
 */
class CashBoxService
{
    /**
     * @var CashBox
     */
    protected CashBox $model;

    /**
     * CashBoxService constructor.
     *
     * @param CashBox $model
     */
    public function __construct(CashBox $model)
    {
        $this->model = $model;
    }

    /**
     * إنشاء صندوق نقدي جديد.
     * 
     * إذا تم تحديد intermediate_account_id، يتم ربط الصندوق بالحساب الوسيط.
     *
     * @param array $data بيانات الصندوق النقدي.
     * @return CashBox الصندوق الذي تم إنشاؤه.
     * @throws Exception في حالة فشل الإنشاء
     */
    public function create(array $data): CashBox
    {
        try {
            DB::beginTransaction();
            
            // التحقق من عدم تكرار الكود
            if (isset($data['code']) && $this->model->where('code', $data['code'])->exists()) {
                throw new Exception("كود الصندوق {$data['code']} مستخدم بالفعل");
            }
            
            // التحقق من وجود الحساب الوسيط (إذا تم تحديده)
            if (isset($data['intermediate_account_id'])) {
                $intermediateAccount = IntermediateAccount::findOrFail($data['intermediate_account_id']);
            }
            
            // تعيين القيم الافتراضية
            if (!isset($data['balance'])) {
                $data['balance'] = 0;
            }
            
            if (!isset($data['is_active'])) {
                $data['is_active'] = true;
            }
            
            if (!isset($data['created_by'])) {
                $data['created_by'] = auth()->id() ?? 1;
            }
            
            $cashBox = $this->model->create($data);
            
            DB::commit();
            return $cashBox;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * تحديث بيانات صندوق نقدي موجود.
     *
     * @param int $id معرف الصندوق النقدي.
     * @param array $data البيانات المراد تحديثها.
     * @return CashBox الصندوق بعد التحديث.
     * @throws Exception في حالة عدم العثور على الصندوق
     */
    public function update(int $id, array $data): CashBox
    {
        try {
            DB::beginTransaction();
            
            $cashBox = $this->model->findOrFail($id);
            
            // التحقق من عدم تكرار الكود (إذا تم تغييره)
            if (isset($data['code']) && $data['code'] !== $cashBox->code) {
                if ($this->model->where('code', $data['code'])->exists()) {
                    throw new Exception("كود الصندوق {$data['code']} مستخدم بالفعل");
                }
            }
            
            // التحقق من وجود الحساب الوسيط (إذا تم تحديده)
            if (isset($data['intermediate_account_id'])) {
                IntermediateAccount::findOrFail($data['intermediate_account_id']);
            }
            
            $cashBox->update($data);
            
            DB::commit();
            return $cashBox->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * حذف صندوق نقدي.
     *
     * @param int $id معرف الصندوق النقدي.
     * @return bool نتيجة عملية الحذف.
     * @throws Exception في حالة وجود عمليات مرتبطة
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();
            
            $cashBox = $this->model->findOrFail($id);
            
            // التحقق من عدم وجود عمليات مرتبطة
            if ($cashBox->transactions()->count() > 0) {
                throw new Exception("لا يمكن حذف الصندوق لوجود عمليات مرتبطة به");
            }
            
            // التحقق من أن الرصيد صفر
            if ($cashBox->balance != 0) {
                throw new Exception("لا يمكن حذف الصندوق لأن رصيده غير صفر");
            }
            
            $result = $cashBox->delete();
            
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * الحصول على صندوق نقدي بواسطة المعرف.
     *
     * @param int $id معرف الصندوق النقدي.
     * @return CashBox|null الصندوق أو null.
     */
    public function find(int $id): ?CashBox
    {
        return $this->model->find($id);
    }

    /**
     * الحصول على صندوق نقدي بواسطة المعرف (مع استثناء).
     *
     * @param int $id معرف الصندوق النقدي.
     * @return CashBox الصندوق.
     * @throws Exception في حالة عدم العثور على الصندوق
     */
    public function findOrFail(int $id): CashBox
    {
        return $this->model->findOrFail($id);
    }

    /**
     * الحصول على جميع الصناديق النقدية.
     *
     * @param bool $activeOnly استرجاع الصناديق النشطة فقط.
     * @return Collection مجموعة من الصناديق.
     */
    public function getAll(bool $activeOnly = false): Collection
    {
        $query = $this->model->query();
        
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        
        return $query->orderBy('name')->get();
    }

    /**
     * ربط صندوق بحساب وسيط.
     *
     * @param int $cashBoxId معرف الصندوق.
     * @param int $intermediateAccountId معرف الحساب الوسيط.
     * @return CashBox الصندوق بعد الربط.
     * @throws Exception في حالة فشل الربط
     */
    public function linkToIntermediateAccount(
        int $cashBoxId,
        int $intermediateAccountId
    ): CashBox {
        try {
            DB::beginTransaction();
            
            $cashBox = $this->model->findOrFail($cashBoxId);
            $intermediateAccount = IntermediateAccount::findOrFail($intermediateAccountId);
            
            $cashBox->intermediate_account_id = $intermediateAccountId;
            $cashBox->save();
            
            DB::commit();
            return $cashBox->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * فك ربط صندوق من حساب وسيط.
     *
     * @param int $cashBoxId معرف الصندوق.
     * @return CashBox الصندوق بعد فك الربط.
     * @throws Exception في حالة فشل فك الربط
     */
    public function unlinkFromIntermediateAccount(int $cashBoxId): CashBox
    {
        try {
            DB::beginTransaction();
            
            $cashBox = $this->model->findOrFail($cashBoxId);
            $cashBox->intermediate_account_id = null;
            $cashBox->save();
            
            DB::commit();
            return $cashBox->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * حساب الرصيد الحالي لصندوق نقدي من العمليات.
     *
     * @param int $id معرف الصندوق النقدي.
     * @return float الرصيد الحالي.
     * @throws Exception في حالة عدم العثور على الصندوق
     */
    public function calculateBalance(int $id): float
    {
        $cashBox = $this->model->findOrFail($id);
        
        // حساب مجموع الإيداعات
        $deposits = $cashBox->transactions()
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->sum('amount');
        
        // حساب مجموع السحوبات
        $withdrawals = $cashBox->transactions()
            ->where('type', 'withdrawal')
            ->where('status', 'completed')
            ->sum('amount');
        
        return (float) ($deposits - $withdrawals);
    }

    /**
     * الحصول على سجل المعاملات لصندوق نقدي.
     *
     * @param int $id معرف الصندوق النقدي.
     * @param string|null $startDate تاريخ البداية (اختياري).
     * @param string|null $endDate تاريخ النهاية (اختياري).
     * @return Collection مجموعة من العمليات.
     */
    public function getTransactions(
        int $id,
        ?string $startDate = null,
        ?string $endDate = null
    ): Collection {
        $cashBox = $this->model->findOrFail($id);
        
        $query = $cashBox->transactions();
        
        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }
        
        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * الحصول على إحصائيات الصندوق.
     *
     * @param int $id معرف الصندوق النقدي.
     * @return array مصفوفة تحتوي على الإحصائيات.
     * @throws Exception في حالة عدم العثور على الصندوق
     */
    public function getStatistics(int $id): array
    {
        $cashBox = $this->model->findOrFail($id);
        
        $totalDeposits = $cashBox->transactions()
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->sum('amount');
        
        $totalWithdrawals = $cashBox->transactions()
            ->where('type', 'withdrawal')
            ->where('status', 'completed')
            ->sum('amount');
        
        $pendingTransactions = $cashBox->transactions()
            ->where('status', 'pending')
            ->count();
        
        $completedTransactions = $cashBox->transactions()
            ->where('status', 'completed')
            ->count();
        
        return [
            'cash_box_id' => $id,
            'cash_box_name' => $cashBox->name,
            'current_balance' => $cashBox->balance,
            'calculated_balance' => $this->calculateBalance($id),
            'total_deposits' => (float) $totalDeposits,
            'total_withdrawals' => (float) $totalWithdrawals,
            'pending_transactions' => $pendingTransactions,
            'completed_transactions' => $completedTransactions,
            'total_transactions' => $pendingTransactions + $completedTransactions,
            'is_linked_to_intermediate' => $cashBox->intermediate_account_id !== null,
            'intermediate_account_id' => $cashBox->intermediate_account_id,
        ];
    }

    /**
     * البحث في الصناديق النقدية.
     *
     * @param string $searchTerm مصطلح البحث.
     * @return Collection مجموعة من الصناديق المطابقة.
     */
    public function search(string $searchTerm): Collection
    {
        return $this->model
            ->where('name', 'like', "%{$searchTerm}%")
            ->orWhere('code', 'like', "%{$searchTerm}%")
            ->orWhere('description', 'like', "%{$searchTerm}%")
            ->orderBy('name')
            ->get();
    }

    /**
     * تبديل حالة تفعيل/إلغاء تفعيل صندوق.
     *
     * @param int $id معرف الصندوق.
     * @param bool $status الحالة الجديدة.
     * @return CashBox الصندوق بعد تحديث حالته.
     * @throws Exception في حالة عدم العثور على الصندوق
     */
    public function toggleActivation(int $id, bool $status): CashBox
    {
        try {
            DB::beginTransaction();
            
            $cashBox = $this->model->findOrFail($id);
            $cashBox->is_active = $status;
            $cashBox->save();
            
            DB::commit();
            return $cashBox;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
