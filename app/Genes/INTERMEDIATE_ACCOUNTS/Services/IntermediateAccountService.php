<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Services;

use App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateAccount;
use App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateTransaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * IntermediateAccountService
 * 
 * خدمة لإدارة العمليات المتعلقة بالحسابات الوسيطة.
 * تتضمن دوال لإنشاء، تحديث، حذف، واسترجاع بيانات الحسابات الوسيطة، 
 * بالإضافة إلى إدارة رصيدها وحالتها وربطها بالوحدات التنظيمية.
 * 
 * @version 2.0.0
 * @since 2025-12-02
 */
class IntermediateAccountService
{
    /**
     * @var IntermediateAccount
     */
    protected IntermediateAccount $model;

    /**
     * IntermediateAccountService constructor.
     *
     * @param IntermediateAccount $model
     */
    public function __construct(IntermediateAccount $model)
    {
        $this->model = $model;
    }

    /**
     * إنشاء حساب وسيط جديد.
     *
     * @param array $data البيانات المطلوبة لإنشاء الحساب.
     * @return IntermediateAccount الحساب الوسيط الذي تم إنشاؤه.
     * @throws Exception في حالة فشل الإنشاء
     */
    public function create(array $data): IntermediateAccount
    {
        try {
            DB::beginTransaction();
            
            // التحقق من عدم تكرار الكود
            if (isset($data['code']) && $this->model->where('code', $data['code'])->exists()) {
                throw new Exception("الكود {$data['code']} مستخدم بالفعل");
            }
            
            $account = $this->model->create($data);
            
            DB::commit();
            return $account;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * تحديث بيانات حساب وسيط موجود.
     *
     * @param int $id معرف الحساب الوسيط المراد تحديثه.
     * @param array $data البيانات الجديدة للتحديث.
     * @return IntermediateAccount الحساب الوسيط بعد التحديث.
     * @throws Exception في حالة عدم العثور على الحساب
     */
    public function update(int $id, array $data): IntermediateAccount
    {
        try {
            DB::beginTransaction();
            
            $account = $this->model->findOrFail($id);
            
            // التحقق من عدم تكرار الكود (إذا تم تغييره)
            if (isset($data['code']) && $data['code'] !== $account->code) {
                if ($this->model->where('code', $data['code'])->exists()) {
                    throw new Exception("الكود {$data['code']} مستخدم بالفعل");
                }
            }
            
            $account->update($data);
            
            DB::commit();
            return $account->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * حذف حساب وسيط.
     *
     * @param int $id معرف الحساب الوسيط المراد حذفه.
     * @return bool نتيجة عملية الحذف (true إذا تم الحذف بنجاح).
     * @throws Exception في حالة وجود عمليات مرتبطة بالحساب
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();
            
            $account = $this->model->findOrFail($id);
            
            // التحقق من عدم وجود عمليات مرتبطة
            if ($account->transactions()->count() > 0) {
                throw new Exception("لا يمكن حذف الحساب لوجود عمليات مرتبطة به");
            }
            
            $result = $account->delete();
            
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * استرجاع حساب وسيط بواسطة المعرف.
     *
     * @param int $id معرف الحساب الوسيط.
     * @return IntermediateAccount|null الحساب الوسيط، أو null إذا لم يتم العثور عليه.
     */
    public function find(int $id): ?IntermediateAccount
    {
        return $this->model->find($id);
    }

    /**
     * استرجاع حساب وسيط بواسطة المعرف (مع استثناء إذا لم يوجد).
     *
     * @param int $id معرف الحساب الوسيط.
     * @return IntermediateAccount الحساب الوسيط.
     * @throws Exception في حالة عدم العثور على الحساب
     */
    public function findOrFail(int $id): IntermediateAccount
    {
        return $this->model->findOrFail($id);
    }

    /**
     * استرجاع جميع الحسابات الوسيطة.
     *
     * @param bool $activeOnly استرجاع الحسابات النشطة فقط.
     * @return Collection مجموعة من كائنات IntermediateAccount.
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
     * استرجاع الحسابات الوسيطة المرتبطة بحساب رئيسي معين.
     *
     * @param int $mainAccountId معرف الحساب الرئيسي.
     * @return Collection مجموعة من كائنات IntermediateAccount.
     */
    public function getByMainAccount(int $mainAccountId): Collection
    {
        return $this->model
            ->where('main_account_id', $mainAccountId)
            ->orderBy('name')
            ->get();
    }

    /**
     * حساب رصيد حساب وسيط معين من العمليات.
     * 
     * يتم حساب الرصيد من مجموع العمليات:
     * - الإيرادات (receipt) تُضاف
     * - المصروفات (payment) تُطرح
     *
     * @param int $id معرف الحساب الوسيط.
     * @return float رصيد الحساب.
     * @throws Exception في حالة عدم العثور على الحساب
     */
    public function calculateBalance(int $id): float
    {
        $account = $this->model->findOrFail($id);
        
        // حساب مجموع الإيرادات (receipt)
        $receipts = $account->transactions()
            ->where('type', 'receipt')
            ->where('status', 'completed')
            ->sum('amount');
        
        // حساب مجموع المصروفات (payment)
        $payments = $account->transactions()
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->sum('amount');
        
        return (float) ($receipts - $payments);
    }

    /**
     * ربط حساب وسيط بوحدة تنظيمية.
     * 
     * يتم إنشاء حساب وسيط عام (GeneralIntermediateAccount) 
     * يربط الحساب الوسيط بالوحدة التنظيمية.
     *
     * @param int $accountId معرف الحساب الوسيط.
     * @param int $unitId معرف الوحدة التنظيمية.
     * @return void
     * @throws Exception في حالة فشل الربط
     */
    public function linkToUnit(int $accountId, int $unitId): void
    {
        try {
            DB::beginTransaction();
            
            $account = $this->model->findOrFail($accountId);
            
            // التحقق من وجود الوحدة (إذا كان Model موجود)
            if (class_exists('App\Models\Unit')) {
                $unit = \App\Models\Unit::findOrFail($unitId);
            }
            
            // إنشاء حساب وسيط عام
            $generalAccount = \App\Genes\INTERMEDIATE_ACCOUNTS\Models\GeneralIntermediateAccount::create([
                'unit_id' => $unitId,
                'intermediate_account_id' => $accountId,
                'name' => "حساب وسيط عام - {$account->name}",
                'balance' => 0,
                'is_active' => true,
                'created_by' => auth()->id() ?? 1,
            ]);
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * تبديل حالة تفعيل/إلغاء تفعيل حساب وسيط.
     *
     * @param int $id معرف الحساب الوسيط.
     * @param bool $status الحالة الجديدة (true للتفعيل، false لإلغاء التفعيل).
     * @return IntermediateAccount الحساب الوسيط بعد تحديث حالته.
     * @throws Exception في حالة عدم العثور على الحساب
     */
    public function toggleActivation(int $id, bool $status): IntermediateAccount
    {
        try {
            DB::beginTransaction();
            
            $account = $this->model->findOrFail($id);
            $account->is_active = $status;
            $account->save();
            
            DB::commit();
            return $account;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * البحث في الحسابات الوسيطة.
     *
     * @param string $searchTerm مصطلح البحث.
     * @return Collection مجموعة من الحسابات المطابقة.
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
     * الحصول على إحصائيات الحساب الوسيط.
     *
     * @param int $id معرف الحساب الوسيط.
     * @return array مصفوفة تحتوي على الإحصائيات.
     * @throws Exception في حالة عدم العثور على الحساب
     */
    public function getStatistics(int $id): array
    {
        $account = $this->model->findOrFail($id);
        
        $totalReceipts = $account->transactions()
            ->where('type', 'receipt')
            ->where('status', 'completed')
            ->sum('amount');
        
        $totalPayments = $account->transactions()
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->sum('amount');
        
        $pendingTransactions = $account->transactions()
            ->where('status', 'pending')
            ->count();
        
        $completedTransactions = $account->transactions()
            ->where('status', 'completed')
            ->count();
        
        return [
            'account_id' => $id,
            'account_name' => $account->name,
            'total_receipts' => (float) $totalReceipts,
            'total_payments' => (float) $totalPayments,
            'balance' => (float) ($totalReceipts - $totalPayments),
            'pending_transactions' => $pendingTransactions,
            'completed_transactions' => $completedTransactions,
            'total_transactions' => $pendingTransactions + $completedTransactions,
        ];
    }
}
