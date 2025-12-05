<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StockMovement;
use Illuminate\Auth\Access\Response;

class StockMovementPolicy
{
    /**
     * تحديد ما إذا كان المستخدم يستطيع عرض أي حركات مخزون.
     */
    public function viewAny(User $user): bool
    {
        // السماح للمستخدمين الذين لديهم صلاحية 'view-stock-movements'
        return $user->hasPermissionTo('view-stock-movements');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع عرض حركة مخزون محددة.
     */
    public function view(User $user, StockMovement $stockMovement): bool
    {
        // السماح إذا كان لديه صلاحية العرض العامة
        return $user->hasPermissionTo('view-stock-movements');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع إنشاء حركات مخزون.
     */
    public function create(User $user): bool
    {
        // السماح للمستخدمين الذين لديهم صلاحية 'create-stock-movements'
        return $user->hasPermissionTo('create-stock-movements');
    }

    // لا يتم تضمين update, delete, restore, forceDelete لأن حركات المخزون سجلات تاريخية لا يجب تعديلها أو حذفها.

    /**
     * تحديد ما إذا كان المستخدم يستطيع عرض التقارير.
     */
    public function viewReports(User $user): bool
    {
        // صلاحية خاصة لعرض التقارير
        return $user->hasPermissionTo('view-stock-reports');
    }
}
