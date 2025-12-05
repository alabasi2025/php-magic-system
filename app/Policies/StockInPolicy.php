<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StockIn;
use Illuminate\Auth\Access\Response;

class StockInPolicy
{
    /**
     * تحديد ما إذا كان المستخدم يمكنه عرض أي نموذج.
     */
    public function viewAny(User $user): bool
    {
        // يمكن لأي مستخدم لديه صلاحية 'view-stock-in' عرض القائمة
        return $user->hasPermissionTo('view-stock-in');
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه عرض النموذج المحدد.
     */
    public function view(User $user, StockIn $stockIn): bool
    {
        // يمكن للمستخدم عرض الإذن إذا كان لديه الصلاحية أو كان هو المنشئ
        return $user->hasPermissionTo('view-stock-in') || $user->id === $stockIn->created_by;
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه إنشاء نماذج.
     */
    public function create(User $user): bool
    {
        // يمكن للمستخدم إنشاء إذن إذا كان لديه صلاحية 'create-stock-in'
        return $user->hasPermissionTo('create-stock-in');
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه تحديث النموذج المحدد.
     */
    public function update(User $user, StockIn $stockIn): bool
    {
        // يمكن للمستخدم التحديث إذا كان لديه الصلاحية وكان الإذن في حالة المسودة
        return $stockIn->status === 'Draft' && (
            $user->hasPermissionTo('edit-stock-in') || $user->id === $stockIn->created_by
        );
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه حذف النموذج المحدد.
     */
    public function delete(User $user, StockIn $stockIn): bool
    {
        // يمكن للمستخدم الحذف إذا كان لديه الصلاحية وكان الإذن في حالة المسودة
        return $stockIn->status === 'Draft' && (
            $user->hasPermissionTo('delete-stock-in') || $user->id === $stockIn->created_by
        );
    }

    /**
     * دالة إضافية: تحديد ما إذا كان المستخدم يمكنه ترحيل الإذن.
     */
    public function complete(User $user, StockIn $stockIn): bool
    {
        // يمكن للمستخدم الترحيل إذا كان لديه صلاحية 'complete-stock-in' وكان الإذن في حالة المسودة
        return $stockIn->status === 'Draft' && $user->hasPermissionTo('complete-stock-in');
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه استعادة النموذج المحذوف.
     */
    public function restore(User $user, StockIn $stockIn): bool
    {
        // يمكن للمستخدم الاستعادة إذا كان لديه صلاحية 'restore-stock-in'
        return $user->hasPermissionTo('restore-stock-in');
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه حذف النموذج نهائياً.
     */
    public function forceDelete(User $user, StockIn $stockIn): bool
    {
        // يمكن للمستخدم الحذف النهائي إذا كان لديه صلاحية 'force-delete-stock-in'
        return $user->hasPermissionTo('force-delete-stock-in');
    }
}
