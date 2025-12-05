<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
{
    /**
     * تحديد ما إذا كان المستخدم يستطيع عرض أي عميل.
     */
    public function viewAny(User $user): bool
    {
        // يمكن لأي مستخدم مصادق عليه لديه صلاحية 'view-customers' عرض القائمة
        return $user->can('view-customers');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع عرض عميل محدد.
     */
    public function view(User $user, Customer $customer): bool
    {
        // يمكن للمستخدم عرض العميل إذا كان لديه صلاحية 'view-customers'
        return $user->can('view-customers');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع إنشاء عميل.
     */
    public function create(User $user): bool
    {
        // يمكن للمستخدم إنشاء عميل إذا كان لديه صلاحية 'create-customers'
        return $user->can('create-customers');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع تحديث عميل.
     */
    public function update(User $user, Customer $customer): bool
    {
        // يمكن للمستخدم تحديث العميل إذا كان لديه صلاحية 'edit-customers'
        return $user->can('edit-customers');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع حذف عميل.
     */
    public function delete(User $user, Customer $customer): bool
    {
        // يمكن للمستخدم حذف العميل إذا كان لديه صلاحية 'delete-customers'
        return $user->can('delete-customers');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع استعادة عميل محذوف.
     */
    public function restore(User $user, Customer $customer): bool
    {
        // غير مطبق في هذا المكون
        return false;
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع حذف عميل نهائياً.
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        // غير مطبق في هذا المكون
        return false;
    }
}
