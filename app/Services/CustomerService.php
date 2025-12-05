<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    /**
     * جلب جميع العملاء مع إمكانية البحث والترتيب.
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllCustomers(array $filters = [])
    {
        $query = Customer::query();

        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
        }

        return $query->latest()->paginate(15);
    }

    /**
     * إنشاء عميل جديد.
     *
     * @param array $data
     * @return Customer
     */
    public function createCustomer(array $data): Customer
    {
        // إضافة المستخدم المنشئ وتعيين الرصيد الأولي كرصيد حالي
        $data['user_id'] = auth()->id();
        $data['balance'] = $data['initial_balance'] ?? 0;

        return Customer::create($data);
    }

    /**
     * تحديث بيانات عميل موجود.
     *
     * @param Customer $customer
     * @param array $data
     * @return Customer
     */
    public function updateCustomer(Customer $customer, array $data): Customer
    {
        // لا نسمح بتحديث الرصيد الحالي مباشرة من هنا، فقط البيانات الأساسية
        $customer->update($data);

        // إذا تم تحديث الرصيد الافتتاحي، يجب إعادة حساب الرصيد الكلي
        if (isset($data['initial_balance'])) {
            $this->recalculateBalance($customer);
        }

        return $customer;
    }

    /**
     * حذف عميل.
     *
     * @param Customer $customer
     * @return bool|null
     */
    public function deleteCustomer(Customer $customer): ?bool
    {
        // يجب التحقق من عدم وجود تعاملات مرتبطة قبل الحذف
        if ($customer->transactions()->exists()) {
            // يمكن رمي استثناء أو إرجاع خطأ حسب سياسة معالجة الأخطاء
            throw new \Exception('لا يمكن حذف العميل لوجود تعاملات مرتبطة به.');
        }

        return $customer->delete();
    }

    /**
     * جلب تاريخ التعاملات للعميل.
     *
     * @param Customer $customer
     * @return Collection
     */
    public function getTransactionHistory(Customer $customer): Collection
    {
        return $customer->transactions()->latest('date')->get();
    }

    /**
     * إعادة حساب وتحديث الرصيد الحالي للعميل.
     *
     * @param Customer $customer
     * @return Customer
     */
    public function recalculateBalance(Customer $customer): Customer
    {
        // نستخدم المعاملة لضمان الاتساق
        return DB::transaction(function () use ($customer) {
            $currentBalance = $customer->calculateCurrentBalance();
            $customer->balance = $currentBalance;
            $customer->save();
            return $customer;
        });
    }
}
