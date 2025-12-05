<?php

namespace App\Policies;

use App\Models\StockOut;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StockOutPolicy
{
    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض أي أذونات إخراج.
     */
    public function viewAny(User $user): bool
    {
        // مثال: السماح لجميع المستخدمين الذين لديهم صلاحية 'view-stock-outs'
        return $user->hasPermissionTo('view-stock-outs');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم عرض إذن الإخراج المحدد.
     */
    public function view(User $user, StockOut $stockOut): bool
    {
        // مثال: يمكن للمستخدم عرض الإذن إذا كان هو من أنشأه أو لديه صلاحية 'view-all-stock-outs'
        return $user->hasPermissionTo('view-all-stock-outs') || $user->id === $stockOut->created_by;
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إنشاء أذونات إخراج.
     */
    public function create(User $user): bool
    {
        // مثال: السماح لجميع المستخدمين الذين لديهم صلاحية 'create-stock-out'
        return $user->hasPermissionTo('create-stock-out');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم تحديث إذن الإخراج المحدد.
     *
     * ملاحظة: التحديث مقيد جداً في منطق الأعمال، لذا قد تكون هذه الصلاحية نادرة الاستخدام.
     */
    public function update(User $user, StockOut $stockOut): bool
    {
        // مثال: يمكن التحديث فقط إذا كان الإذن في حالة 'pending' ولديه صلاحية 'edit-stock-out'
        return $stockOut->status === 'pending' && $user->hasPermissionTo('edit-stock-out');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم حذف إذن الإخراج المحدد.
     */
    public function delete(User $user, StockOut $stockOut): bool
    {
        // مثال: يمكن الحذف فقط إذا كان الإذن في حالة 'canceled' ولديه صلاحية 'delete-stock-out'
        return $stockOut->status === 'canceled' && $user->hasPermissionTo('delete-stock-out');
    }

    /**
     * تحديد ما إذا كان يمكن للمستخدم إلغاء إذن الإخراج المحدد (دالة إضافية).
     */
    public function cancel(User $user, StockOut $stockOut): bool
    {
        // مثال: يمكن الإلغاء إذا لم يكن ملغياً بالفعل ولديه صلاحية 'cancel-stock-out'
        return $stockOut->status !== 'canceled' && $user->hasPermissionTo('cancel-stock-out');
    }
}
