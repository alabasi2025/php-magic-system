<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StockTransfer;
use Illuminate\Auth\Access\Response;

/**
 * سياسة التخويل لنموذج StockTransfer.
 */
class StockTransferPolicy
{
    /**
     * تحديد ما إذا كان المستخدم يستطيع عرض أي تحويلات.
     */
    public function viewAny(User $user): bool
    {
        // مثال: السماح لأي مستخدم لديه صلاحية 'view-stock-transfers'
        return $user->can('view-stock-transfers');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع عرض تحويل معين.
     */
    public function view(User $user, StockTransfer $stockTransfer): bool
    {
        // مثال: السماح إذا كان المستخدم يستطيع عرض التحويلات أو هو منشئ التحويل
        return $user->can('view-stock-transfers') || $user->id === $stockTransfer->created_by;
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع إنشاء تحويلات.
     */
    public function create(User $user): bool
    {
        // مثال: السماح لأي مستخدم لديه صلاحية 'create-stock-transfers'
        return $user->can('create-stock-transfers');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع تحديث تحويل معين.
     */
    public function update(User $user, StockTransfer $stockTransfer): bool
    {
        // مثال: السماح فقط إذا كان التحويل 'pending' والمستخدم هو المنشئ ولديه صلاحية التعديل
        return $stockTransfer->status === 'pending' &&
               $user->id === $stockTransfer->created_by &&
               $user->can('edit-stock-transfers');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع حذف تحويل معين.
     */
    public function delete(User $user, StockTransfer $stockTransfer): bool
    {
        // مثال: السماح فقط إذا كان التحويل 'pending' والمستخدم هو المنشئ ولديه صلاحية الحذف
        return $stockTransfer->status === 'pending' &&
               $user->id === $stockTransfer->created_by &&
               $user->can('delete-stock-transfers');
    }

    /**
     * تحديد ما إذا كان المستخدم يستطيع الموافقة على تحويل.
     */
    public function approve(User $user, StockTransfer $stockTransfer): Response
    {
        // يجب أن يكون التحويل في حالة 'pending'
        if ($stockTransfer->status !== 'pending') {
            return Response::deny('لا يمكن الموافقة على تحويل حالته ليست "قيد الانتظار".');
        }

        // يجب أن يكون المستخدم لديه صلاحية 'approve-stock-transfers'
        if (!$user->can('approve-stock-transfers')) {
            return Response::deny('ليس لديك الصلاحية للموافقة على تحويلات المخزون.');
        }

        // لا يمكن للمستخدم أن يوافق على تحويل أنشأه بنفسه (لمنع تضارب المصالح)
        if ($user->id === $stockTransfer->created_by) {
            return Response::deny('لا يمكنك الموافقة على تحويل قمت بإنشائه.');
        }

        return Response::allow();
    }
}
