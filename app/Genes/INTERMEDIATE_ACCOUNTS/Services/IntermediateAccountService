<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Services;

use App\Models\IntermediateAccount;
use Illuminate\Database\Eloquent\Collection;

/**
 * IntermediateAccountService
 * خدمة لإدارة العمليات المتعلقة بالحسابات الوسيطة.
 * تتضمن دوال لإنشاء، تحديث، حذف، واسترجاع بيانات الحسابات الوسيطة، بالإضافة إلى إدارة رصيدها وحالتها.
 */
class IntermediateAccountService
{
    /**
     * @var IntermediateAccount
     */
    protected $model;

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
     * @return IntermediateAccount|null الحساب الوسيط الذي تم إنشاؤه، أو null في حالة الفشل.
     */
    public function create(array $data): ?IntermediateAccount
    {
        // منطق إنشاء الحساب الوسيط
        return $this->model->create($data);
    }

    /**
     * تحديث بيانات حساب وسيط موجود.
     *
     * @param int $id معرف الحساب الوسيط المراد تحديثه.
     * @param array $data البيانات الجديدة للتحديث.
     * @return IntermediateAccount|null الحساب الوسيط بعد التحديث، أو null في حالة عدم العثور عليه أو الفشل.
     */
    public function update(int $id, array $data): ?IntermediateAccount
    {
        $account = $this->model->find($id);
        if ($account) {
            $account->update($data);
            return $account;
        }
        return null;
    }

    /**
     * حذف حساب وسيط.
     *
     * @param int $id معرف الحساب الوسيط المراد حذفه.
     * @return bool نتيجة عملية الحذف (true إذا تم الحذف بنجاح، false خلاف ذلك).
     */
    public function delete(int $id): bool
    {
        return $this->model->destroy($id) > 0;
    }

    /**
     * استرجاع حساب وسيط بواسطة المعرف.
     *
     * @param int $id معرف الحساب الوسيط.
     * @return IntermediateAccount|null الحساب الوسيط، أو null إذا لم يتم العثور عليه.
     */
    public function getById(int $id): ?IntermediateAccount
    {
        return $this->model->find($id);
    }

    /**
     * استرجاع جميع الحسابات الوسيطة.
     *
     * @return Collection مجموعة من كائنات IntermediateAccount.
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * استرجاع الحسابات الوسيطة المرتبطة بحساب رئيسي معين.
     *
     * @param int $mainAccountId معرف الحساب الرئيسي.
     * @return Collection مجموعة من كائنات IntermediateAccount.
     */
    public function getByMainAccount(int $mainAccountId): Collection
    {
        return $this->model->where('main_account_id', $mainAccountId)->get();
    }

    /**
     * استرجاع رصيد حساب وسيط معين.
     *
     * @param int $id معرف الحساب الوسيط.
     * @return float|null رصيد الحساب، أو null إذا لم يتم العثور على الحساب.
     */
    public function getBalance(int $id): ?float
    {
        $account = $this->model->find($id);
        // نفترض أن حقل الرصيد هو 'balance'
        return $account ? (float) $account->balance : null;
    }

    /**
     * تبديل حالة تفعيل/إلغاء تفعيل حساب وسيط.
     *
     * @param int $id معرف الحساب الوسيط.
     * @param bool $status الحالة الجديدة (true للتفعيل، false لإلغاء التفعيل).
     * @return IntermediateAccount|null الحساب الوسيط بعد تحديث حالته، أو null في حالة عدم العثور عليه.
     */
    public function toggleActivation(int $id, bool $status): ?IntermediateAccount
    {
        $account = $this->model->find($id);
        if ($account) {
            // نفترض أن حقل الحالة هو 'is_active'
            $account->is_active = $status;
            $account->save();
            return $account;
        }
        return null;
    }
}
