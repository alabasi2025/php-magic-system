<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Services;

use App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateAccount;
use App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateTransaction;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Service: IntermediateAccountService
 * 
 * 💡 الفكرة:
 * خدمة تدير جميع عمليات الحسابات الوسيطة.
 * 
 * 🎯 المسؤوليات:
 * - إنشاء وتعديل الحسابات الوسيطة
 * - التحقق من صحة الإعدادات
 * - إدارة حالة الحسابات الوسيطة
 * 
 * @version 1.0.0
 * @since 2025-11-27
 */
class IntermediateAccountService
{
    /**
     * Create a new intermediate account setup.
     *
     * @param int $mainAccountId
     * @param int $intermediateAccount1Id
     * @param int|null $intermediateAccount2Id
     * @param int|null $intermediateAccount3Id
     * @param string|null $notes
     * @return IntermediateAccount
     * @throws Exception
     */
    public function create(
        int $mainAccountId,
        int $intermediateAccount1Id,
        ?int $intermediateAccount2Id = null,
        ?int $intermediateAccount3Id = null,
        ?string $notes = null
    ): IntermediateAccount {
        DB::beginTransaction();
        
        try {
            // التحقق من وجود الحسابات
            $this->validateAccounts($mainAccountId, $intermediateAccount1Id, $intermediateAccount2Id, $intermediateAccount3Id);
            
            // التحقق من عدم وجود إعداد سابق
            if ($this->exists($mainAccountId)) {
                throw new Exception('الحساب الرئيسي لديه إعداد حساب وسيط بالفعل');
            }
            
            // إنشاء الحساب الوسيط
            $intermediateAccount = IntermediateAccount::create([
                'main_account_id' => $mainAccountId,
                'intermediate_account_1_id' => $intermediateAccount1Id,
                'intermediate_account_2_id' => $intermediateAccount2Id,
                'intermediate_account_3_id' => $intermediateAccount3Id,
                'status' => 'active',
                'notes' => $notes,
                'created_by' => Auth::id(),
            ]);
            
            DB::commit();
            
            return $intermediateAccount;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing intermediate account setup.
     *
     * @param int $id
     * @param array $data
     * @return IntermediateAccount
     * @throws Exception
     */
    public function update(int $id, array $data): IntermediateAccount
    {
        DB::beginTransaction();
        
        try {
            $intermediateAccount = IntermediateAccount::findOrFail($id);
            
            // التحقق من الحسابات الجديدة إذا تم تغييرها
            if (isset($data['intermediate_account_1_id']) || 
                isset($data['intermediate_account_2_id']) || 
                isset($data['intermediate_account_3_id'])) {
                
                $this->validateAccounts(
                    $intermediateAccount->main_account_id,
                    $data['intermediate_account_1_id'] ?? $intermediateAccount->intermediate_account_1_id,
                    $data['intermediate_account_2_id'] ?? $intermediateAccount->intermediate_account_2_id,
                    $data['intermediate_account_3_id'] ?? $intermediateAccount->intermediate_account_3_id
                );
            }
            
            $data['updated_by'] = Auth::id();
            $intermediateAccount->update($data);
            
            DB::commit();
            
            return $intermediateAccount->fresh();
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete an intermediate account setup.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        DB::beginTransaction();
        
        try {
            $intermediateAccount = IntermediateAccount::findOrFail($id);
            
            // التحقق من عدم وجود عمليات معلقة
            if ($intermediateAccount->getUnlinkedTransactionsCount() > 0) {
                throw new Exception('لا يمكن حذف الحساب الوسيط لوجود عمليات معلقة');
            }
            
            // التحقق من أن الرصيد = 0
            if (!$intermediateAccount->isBalanced()) {
                throw new Exception('لا يمكن حذف الحساب الوسيط لأن الرصيد ليس صفراً');
            }
            
            $intermediateAccount->delete();
            
            DB::commit();
            
            return true;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Check if a main account has an intermediate account setup.
     *
     * @param int $mainAccountId
     * @return bool
     */
    public function exists(int $mainAccountId): bool
    {
        return IntermediateAccount::where('main_account_id', $mainAccountId)->exists();
    }

    /**
     * Get intermediate account by main account ID.
     *
     * @param int $mainAccountId
     * @return IntermediateAccount|null
     */
    public function getByMainAccount(int $mainAccountId): ?IntermediateAccount
    {
        return IntermediateAccount::where('main_account_id', $mainAccountId)->first();
    }

    /**
     * Activate an intermediate account.
     *
     * @param int $id
     * @return bool
     */
    public function activate(int $id): bool
    {
        $intermediateAccount = IntermediateAccount::findOrFail($id);
        return $intermediateAccount->activate();
    }

    /**
     * Deactivate an intermediate account.
     *
     * @param int $id
     * @return bool
     */
    public function deactivate(int $id): bool
    {
        $intermediateAccount = IntermediateAccount::findOrFail($id);
        return $intermediateAccount->deactivate();
    }

    /**
     * Get all intermediate accounts with unbalanced status.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnbalancedAccounts()
    {
        return IntermediateAccount::with(['mainAccount', 'intermediateAccount1'])
            ->get()
            ->filter(function ($account) {
                return !$account->isBalanced();
            });
    }

    /**
     * Get all intermediate accounts with pending transactions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAccountsWithPendingTransactions()
    {
        return IntermediateAccount::with(['mainAccount', 'intermediateAccount1'])
            ->get()
            ->filter(function ($account) {
                return $account->getUnlinkedTransactionsCount() > 0;
            });
    }

    /**
     * Validate that all accounts exist and are different.
     *
     * @param int $mainAccountId
     * @param int $intermediateAccount1Id
     * @param int|null $intermediateAccount2Id
     * @param int|null $intermediateAccount3Id
     * @return void
     * @throws Exception
     */
    protected function validateAccounts(
        int $mainAccountId,
        int $intermediateAccount1Id,
        ?int $intermediateAccount2Id,
        ?int $intermediateAccount3Id
    ): void {
        // التحقق من وجود الحسابات
        if (!Account::find($mainAccountId)) {
            throw new Exception('الحساب الرئيسي غير موجود');
        }
        
        if (!Account::find($intermediateAccount1Id)) {
            throw new Exception('الحساب الوسيط الأول غير موجود');
        }
        
        if ($intermediateAccount2Id && !Account::find($intermediateAccount2Id)) {
            throw new Exception('الحساب الوسيط الثاني غير موجود');
        }
        
        if ($intermediateAccount3Id && !Account::find($intermediateAccount3Id)) {
            throw new Exception('الحساب الوسيط الثالث غير موجود');
        }
        
        // التحقق من أن الحسابات مختلفة
        $accounts = array_filter([
            $mainAccountId,
            $intermediateAccount1Id,
            $intermediateAccount2Id,
            $intermediateAccount3Id
        ]);
        
        if (count($accounts) !== count(array_unique($accounts))) {
            throw new Exception('يجب أن تكون جميع الحسابات مختلفة');
        }
    }
}
