<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StockCount;
use Illuminate\Auth\Access\Response;

/**
 * سياسة التحكم في صلاحيات الوصول لعمليات الجرد.
 */
class StockCountPolicy
{
    /**
     * تحديد ما إذا كان المستخدم يستطيع عرض أي عمليات جرد.
     */
    public function viewAny(User $user): bool
    {
        // يمكن لأي مستخدم لديه صلاحية "view-stock-counts" عرض القائمة
        return $user->can('view-stock-counts');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع عرض عملية جرد محددة.
     */
    public function view(User $user, StockCount $stockCount): bool
    {
        // يمكن للمستخدم عرض الجرد إذا كان لديه الصلاحية
        return $user->can('view-stock-counts');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع إنشاء عمليات جرد.
     */
    public function create(User $user): bool
    {
        // يمكن للمستخدم إنشاء جرد إذا كان لديه صلاحية "create-stock-count"
        return $user->can('create-stock-count');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع تحديث عملية جرد (إدخال الكميات).
     */
    public function update(User $user, StockCount $stockCount): bool
    {
        // يمكن للمستخدم التحديث إذا كان لديه صلاحية "update-stock-count"
        // ويجب أن يكون الجرد في حالة "Draft"
        return $user->can('update-stock-count') && $stockCount->status === 'Draft';
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع الموافقة على عملية جرد وتعديل المخزون.
     */
    public function approve(User $user, StockCount $stockCount): bool
    {
        // يمكن للمستخدم الموافقة إذا كان لديه صلاحية "approve-stock-count"
        // ويجب أن يكون الجرد في حالة "Pending Approval"
        return $user->can('approve-stock-count') && $stockCount->status === 'Pending Approval';
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع حذف عملية جرد.
     */
    public function delete(User $user, StockCount $stockCount): bool
    {
        // يمكن للمستخدم الحذف إذا كان لديه صلاحية "delete-stock-count"
        // ويجب أن يكون الجرد في حالة "Draft"
        return $user->can('delete-stock-count') && $stockCount->status === 'Draft';
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع استعادة عملية جرد محذوفة.
     */
    public function restore(User $user, StockCount $stockCount): bool
    {
        // لا يوجد دعم للحذف الناعم في هذا النموذج، لكن يمكن إضافة المنطق هنا
        return false;
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع حذف عملية جرد نهائياً.
     */
    public function forceDelete(User $user, StockCount $stockCount): bool
    {
        return false;
    }
}
