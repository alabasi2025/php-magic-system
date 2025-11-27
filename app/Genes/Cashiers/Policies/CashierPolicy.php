<?php

namespace App\Genes\Cashiers\Policies;

use App\Models\User;
use App\Genes\Cashiers\Models\Cashier;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Cashier Policy
 *
 * تحدد قواعد التفويض (Authorization) لنموذج Cashier.
 * يتم استخدام هذه السياسة للتحكم في وصول المستخدمين إلى موارد الصرافين.
 *
 * @package App\Genes\Cashiers\Policies
 */
class CashierPolicy
{
    use HandlesAuthorization;

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض أي نموذج صراف (Cashier).
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'view-any-cashier'
        // في نظام الصلاحيات الفعلي، يجب التحقق من الصلاحيات هنا.
        // نفترض أن أي مستخدم مصادق عليه يمكنه عرض قائمة الصرافين لأغراض الإدارة.
        return $user->can('view-any-cashier');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض نموذج صراف محدد (Cashier).
     *
     * @param \App\Models\User $user
     * @param \App\Genes\Cashiers\Models\Cashier $cashier
     * @return bool
     */
    public function view(User $user, Cashier $cashier): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'view-cashier'
        // أو أن يكون هو الصراف نفسه (في حالة عرض ملفه الشخصي).
        return $user->can('view-cashier') || $user->id === $cashier->user_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إنشاء نماذج صرافين (Cashier).
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'create-cashier'
        return $user->can('create-cashier');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تحديث نموذج صراف محدد (Cashier).
     *
     * @param \App\Models\User $user
     * @param \App\Genes\Cashiers\Models\Cashier $cashier
     * @return bool
     */
    public function update(User $user, Cashier $cashier): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'update-cashier'
        // أو أن يكون هو الصراف نفسه (في حالة تحديث بياناته الشخصية).
        return $user->can('update-cashier') || $user->id === $cashier->user_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم حذف نموذج صراف محدد (Cashier).
     *
     * @param \App\Models\User $user
     * @param \App\Genes\Cashiers\Models\Cashier $cashier
     * @return bool
     */
    public function delete(User $user, Cashier $cashier): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'delete-cashier'
        return $user->can('delete-cashier');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم استعادة نموذج صراف محذوف (Cashier).
     *
     * @param \App\Models\User $user
     * @param \App\Genes\Cashiers\Models\Cashier $cashier
     * @return bool
     */
    public function restore(User $user, Cashier $cashier): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'restore-cashier'
        return $user->can('restore-cashier');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم فرض حذف نموذج صراف محدد (Cashier).
     *
     * @param \App\Models\User $user
     * @param \App\Genes\Cashiers\Models\Cashier $cashier
     * @return bool
     */
    public function forceDelete(User $user, Cashier $cashier): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'force-delete-cashier'
        return $user->can('force-delete-cashier');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إجراء عملية مالية (مثل إيداع أو سحب).
     *
     * @param \App\Models\User $user
     * @param \App\Genes\Cashiers\Models\Cashier $cashier
     * @return bool
     */
    public function transact(User $user, Cashier $cashier): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'perform-transaction'
        // وأن يكون الصراف في حالة نشطة.
        return $user->can('perform-transaction') && $cashier->is_active;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إغلاق وردية الصراف (Shift).
     *
     * @param \App\Models\User $user
     * @param \App\Genes\Cashiers\Models\Cashier $cashier
     * @return bool
     */
    public function closeShift(User $user, Cashier $cashier): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'close-cashier-shift'
        // وأن يكون هو الصراف نفسه.
        return $user->can('close-cashier-shift') && $user->id === $cashier->user_id;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تجاوز أي قيود (Super Admin/Bypass).
     *
     * @param \App\Models\User $user
     * @param string $ability
     * @return bool|null
     */
    public function before(User $user, string $ability): ?bool
    {
        // إذا كان المستخدم يمتلك صلاحية 'manage-cashiers' فإنه يتجاوز جميع القيود.
        if ($user->can('manage-cashiers')) {
            return true;
        }

        return null;
    }
}