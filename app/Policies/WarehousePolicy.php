<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Auth\Access\Response;

class WarehousePolicy
{
    /**
     * السماح للمستخدمين ذوي الصلاحيات العليا بتجاوز التحقق.
     */
    public function before(User $user, string $ability): ?bool
    {
        // مثال: إذا كان المستخدم "مدير نظام"، فلديه صلاحية على كل شيء
        if ($user->hasRole('system_admin')) {
            return true;
        }

        return null;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض أي مخازن.
     */
    public function viewAny(User $user): bool
    {
        // مثال: يمكن لأي مستخدم لديه إذن 'view-warehouses' عرض القائمة
        return $user->can('view-warehouses');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض مخزن معين.
     */
    public function view(User $user, Warehouse $warehouse): bool
    {
        // مثال: يمكن للمستخدم عرض المخزن إذا كان لديه إذن 'view-warehouses' أو كان هو مدير هذا المخزن
        return $user->can('view-warehouses') || $user->id === $warehouse->manager_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إنشاء مخازن.
     */
    public function create(User $user): bool
    {
        // مثال: يمكن للمستخدم إنشاء مخزن إذا كان لديه إذن 'create-warehouses'
        return $user->can('create-warehouses');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تحديث مخزن معين.
     */
    public function update(User $user, Warehouse $warehouse): bool
    {
        // مثال: يمكن للمستخدم تحديث المخزن إذا كان لديه إذن 'edit-warehouses'
        return $user->can('edit-warehouses');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم حذف مخزن معين.
     */
    public function delete(User $user, Warehouse $warehouse): bool
    {
        // مثال: يمكن للمستخدم حذف المخزن إذا كان لديه إذن 'delete-warehouses'
        return $user->can('delete-warehouses');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تبديل حالة التفعيل.
     */
    public function toggleStatus(User $user, Warehouse $warehouse): bool
    {
        // مثال: يتطلب إذن التحديث
        return $this->update($user, $warehouse);
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض الإحصائيات.
     */
    public function viewStatistics(User $user): bool
    {
        // مثال: يتطلب إذن خاص لعرض الإحصائيات
        return $user->can('view-warehouse-statistics');
    }

    // ... يمكن إضافة دوال أخرى مثل restore و forceDelete
}
