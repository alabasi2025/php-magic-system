<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SupplierService
{
    /**
     * جلب جميع الموردين مع إمكانية البحث والترتيب.
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllSuppliers(array $filters = [])
    {
        $query = Supplier::query();

        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
        }

        return $query->latest()->paginate(15);
    }

    /**
     * إنشاء مورد جديد.
     *
     * @param array $data
     * @return Supplier
     */
    public function createSupplier(array $data): Supplier
    {
        // إضافة المستخدم المنشئ وتعيين الرصيد الأولي كرصيد حالي
        $data['user_id'] = auth()->id();
        $data['balance'] = $data['initial_balance'] ?? 0;

        return Supplier::create($data);
    }

    /**
     * تحديث بيانات مورد موجود.
     *
     * @param Supplier $supplier
     * @param array $data
     * @return Supplier
     */
    public function updateSupplier(Supplier $supplier, array $data): Supplier
    {
        // لا نسمح بتحديث الرصيد الحالي مباشرة من هنا، فقط البيانات الأساسية
        $supplier->update($data);

        // إذا تم تحديث الرصيد الافتتاحي، يجب إعادة حساب الرصيد الكلي
        if (isset($data['initial_balance'])) {
            $this->recalculateBalance($supplier);
        }

        return $supplier;
    }

    /**
     * حذف مورد.
     *
     * @param Supplier $supplier
     * @return bool|null
     */
    public function deleteSupplier(Supplier $supplier): ?bool
    {
        // يجب التحقق من عدم وجود تعاملات مرتبطة قبل الحذف
        if ($supplier->transactions()->exists()) {
            // يمكن رمي استثناء أو إرجاع خطأ حسب سياسة معالجة الأخطاء
            throw new \Exception('لا يمكن حذف المورد لوجود تعاملات مرتبطة به.');
        }

        return $supplier->delete();
    }

    /**
     * جلب تاريخ التعاملات للمورد.
     *
     * @param Supplier $supplier
     * @return Collection
     */
    public function getTransactionHistory(Supplier $supplier): Collection
    {
        return $supplier->transactions()->latest('date')->get();
    }

    /**
     * إعادة حساب وتحديث الرصيد الحالي للمورد.
     *
     * @param Supplier $supplier
     * @return Supplier
     */
    public function recalculateBalance(Supplier $supplier): Supplier
    {
        // نستخدم المعاملة لضمان الاتساق
        return DB::transaction(function () use ($supplier) {
            $currentBalance = $supplier->calculateCurrentBalance();
            $supplier->balance = $currentBalance;
            $supplier->save();
            return $supplier;
        });
    }
}
