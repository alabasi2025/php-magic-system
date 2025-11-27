<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cashier; // افتراض وجود نموذج Cashier
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * @class CashierPolicy
 * @package App\Policies
 * @brief سياسة التحكم في صلاحيات الوصول والإجراءات المتعلقة بنموذج Cashier.
 *
 * هذه السياسة تضمن تطبيق قواعد الأمان والتحقق من الصلاحيات (Authorization)
 * على مستوى نموذج الصراف (Cashier) ضمن نظام الصرافين (Cashiers Gene).
 * يتم استخدامها في وحدات التحكم (Controllers) للتحقق من أن المستخدم الحالي
 * يمتلك الصلاحيات اللازمة لتنفيذ الإجراء المطلوب.
 */
class CashierPolicy
{
    use HandlesAuthorization;

    /**
     * @brief تحديد ما إذا كان يمكن للمستخدم عرض قائمة النماذج.
     *
     * @param \App\Models\User $user المستخدم الحالي.
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'view-any-cashiers'
        // يتم استخدام وظيفة can() من Laravel للتحقق من الصلاحيات المحددة في نظام الأذونات.
        return $user->can('view-any-cashiers');
    }

    /**
     * @brief تحديد ما إذا كان يمكن للمستخدم عرض نموذج معين.
     *
     * @param \App\Models\User $user المستخدم الحالي.
     * @param \App\Models\Cashier $cashier نموذج الصراف المراد عرضه.
     * @return bool
     */
    public function view(User $user, Cashier $cashier): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'view-cashier'
        // بالإضافة إلى التحقق من أن الصراف يتبع نفس الوحدة التنظيمية للمستخدم (إذا لزم الأمر).
        return $user->can('view-cashier');
    }

    /**
     * @brief تحديد ما إذا كان يمكن للمستخدم إنشاء نماذج جديدة.
     *
     * @param \App\Models\User $user المستخدم الحالي.
     * @return bool
     */
    public function create(User $user): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'create-cashier'
        return $user->can('create-cashier');
    }

    /**
     * @brief تحديد ما إذا كان يمكن للمستخدم تحديث نموذج معين.
     *
     * @param \App\Models\User $user المستخدم الحالي.
     * @param \App\Models\Cashier $cashier نموذج الصراف المراد تحديثه.
     * @return bool
     */
    public function update(User $user, Cashier $cashier): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'update-cashier'
        // ويمكن إضافة منطق للتحقق من ملكية النموذج (مثلاً: $user->id === $cashier->user_id)
        return $user->can('update-cashier');
    }

    /**
     * @brief تحديد ما إذا كان يمكن للمستخدم حذف نموذج معين.
     *
     * @param \App\Models\User $user المستخدم الحالي.
     * @param \App\Models\Cashier $cashier نموذج الصراف المراد حذفه.
     * @return bool
     */
    public function delete(User $user, Cashier $cashier): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'delete-cashier'
        return $user->can('delete-cashier');
    }

    /**
     * @brief تحديد ما إذا كان يمكن للمستخدم استعادة نموذج معين (Soft Deletes).
     *
     * @param \App\Models\User $user المستخدم الحالي.
     * @param \App\Models\Cashier $cashier نموذج الصراف المراد استعادته.
     * @return bool
     */
    public function restore(User $user, Cashier $cashier): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'restore-cashier'
        return $user->can('restore-cashier');
    }

    /**
     * @brief تحديد ما إذا كان يمكن للمستخدم حذف نموذج معين بشكل دائم.
     *
     * @param \App\Models\User $user المستخدم الحالي.
     * @param \App\Models\Cashier $cashier نموذج الصراف المراد حذفه نهائياً.
     * @return bool
     */
    public function forceDelete(User $user, Cashier $cashier): bool
    {
        // يجب أن يكون المستخدم يمتلك صلاحية 'force-delete-cashier'
        return $user->can('force-delete-cashier');
    }
}