<?php

namespace App\Genes\Cashiers\Services;

use App\Genes\Cashiers\Models\Cashier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @author [Your Name/Manus AI]
 * @since [Date]
 *
 * تطبيق خدمة الصرافين (CashierService).
 * يوفر منطق الأعمال الأساسي لإدارة نموذج الصرافين.
 */
class CashierService implements CashierServiceInterface
{
    /**
     * استرداد جميع الصرافين.
     *
     * @return Collection<int, Cashier>
     */
    public function getAllCashiers(): Collection
    {
        return Cashier::all();
    }

    /**
     * استرداد صراف معين بواسطة المعرف.
     *
     * @param int $cashierId
     * @return Cashier|null
     */
    public function getCashierById(int $cashierId): ?Cashier
    {
        return Cashier::find($cashierId);
    }

    /**
     * إنشاء صراف جديد.
     *
     * @param array $data
     * @return Cashier
     */
    public function createCashier(array $data): Cashier
    {
        // يجب إضافة التحقق من الصحة (Validation) ومنطق الأعمال الإضافي هنا
        return Cashier::create($data);
    }

    /**
     * تحديث بيانات صراف موجود.
     *
     * @param int $cashierId
     * @param array $data
     * @return Cashier|null
     */
    public function updateCashier(int $cashierId, array $data): ?Cashier
    {
        $cashier = $this->getCashierById($cashierId);

        if ($cashier) {
            // يجب إضافة التحقق من الصحة (Validation) ومنطق الأعمال الإضافي هنا
            $cashier->update($data);
        }

        return $cashier;
    }

    /**
     * حذف صراف بواسطة المعرف.
     *
     * @param int $cashierId
     * @return bool
     */
    public function deleteCashier(int $cashierId): bool
    {
        $cashier = $this->getCashierById($cashierId);

        if ($cashier) {
            return $cashier->delete();
        }

        return false;
    }
}