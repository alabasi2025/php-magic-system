<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SupplierPolicy
{
    /**
     * تحديد ما إذا كان المستخدم يستطيع عرض أي مورد.
     */
    public function viewAny(User $user): bool
    {
        // يمكن لأي مستخدم مصادق عليه لديه صلاحية 'view-suppliers' عرض القائمة
        return $user->can('view-suppliers');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع عرض مورد محدد.
     */
    public function view(User $user, Supplier $supplier): bool
    {
        // يمكن للمستخدم عرض المورد إذا كان لديه صلاحية 'view-suppliers'
        return $user->can('view-suppliers');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع إنشاء مورد.
     */
    public function create(User $user): bool
    {
        // يمكن للمستخدم إنشاء مورد إذا كان لديه صلاحية 'create-suppliers'
        return $user->can('create-suppliers');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع تحديث مورد.
     */
    public function update(User $user, Supplier $supplier): bool
    {
        // يمكن للمستخدم تحديث المورد إذا كان لديه صلاحية 'edit-suppliers'
        // ويمكن إضافة شرط أن يكون هو من أنشأه إذا لزم الأمر: && $user->id === $supplier->user_id
        return $user->can('edit-suppliers');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع حذف مورد.
     */
    public function delete(User $user, Supplier $supplier): bool
    {
        // يمكن للمستخدم حذف المورد إذا كان لديه صلاحية 'delete-suppliers'
        return $user->can('delete-suppliers');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع استعادة مورد محذوف.
     */
    public function restore(User $user, Supplier $supplier): bool
    {
        // غير مطبق في هذا المكون
        return false;
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع حذف مورد نهائياً.
     */
    public function forceDelete(User $user, Supplier $supplier): bool
    {
        // غير مطبق في هذا المكون
        return false;
    }
}
