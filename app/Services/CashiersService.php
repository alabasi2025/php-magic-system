<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;

/**
 * @gene Cashiers
 * @category Business Logic
 * @task 2067 (Task 7)
 *
 * خدمة منطق الأعمال لنظام الصرافين (Cashiers Gene).
 * تتولى هذه الخدمة إدارة العمليات المتعلقة بالصرافين، مثل إنشاء، تحديث، واستعراض بياناتهم.
 */
class CashiersService
{
    /**
     * استعراض قائمة بجميع الصرافين (المستخدمين الذين لديهم دور صراف).
     *
     * @return Collection|User[]
     */
    public function getAllCashiers(): Collection
    {
        // افتراض أن الصرافين يتم تمثيلهم في جدول المستخدمين (Users)
        // وأن هناك آلية لتحديد دورهم (مثلاً، من خلال علاقة الأدوار أو حقل محدد).
        // في هذا المثال، نفترض وجود نطاق (Scope) أو علاقة لتحديد الصرافين.
        // يجب تعديل هذا الجزء ليتناسب مع آلية الأدوار الفعلية في المشروع.
        // مثال افتراضي:
        return User::where('is_cashier', true)->get();
    }

    /**
     * إنشاء صراف جديد في النظام.
     *
     * @param array $data البيانات المطلوبة لإنشاء الصراف (الاسم، البريد الإلكتروني، كلمة المرور، إلخ).
     * @return User
     * @throws \Exception
     */
    public function createCashier(array $data): User
    {
        DB::beginTransaction();
        try {
            // التحقق من وجود البيانات الأساسية
            if (!isset($data['name'], $data['email'], $data['password'])) {
                throw new \InvalidArgumentException('البيانات الأساسية لإنشاء الصراف غير مكتملة.');
            }

            // إنشاء المستخدم
            $cashier = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_cashier' => true, // تعيين علامة الصراف الافتراضية
                // إضافة أي حقول أخرى مطلوبة
            ]);

            // هنا يمكن إضافة منطق لربط الصراف بدور محدد (Role) إذا كان النظام يستخدم نظام الأدوار.
            // مثال: $cashier->assignRole('cashier');

            DB::commit();
            return $cashier;
        } catch (\Exception $e) {
            DB::rollBack();
            // يمكن تسجيل الخطأ هنا
            throw $e;
        }
    }

    /**
     * تحديث بيانات صراف موجود.
     *
     * @param int $cashierId معرف الصراف.
     * @param array $data البيانات المراد تحديثها.
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updateCashier(int $cashierId, array $data): User
    {
        $cashier = User::where('is_cashier', true)->findOrFail($cashierId);

        DB::beginTransaction();
        try {
            $cashier->update($data);

            // تحديث كلمة المرور إذا تم تمريرها
            if (isset($data['password'])) {
                $cashier->password = Hash::make($data['password']);
                $cashier->save();
            }

            DB::commit();
            return $cashier;
        } catch (\Exception $e) {
            DB::rollBack();
            // يمكن تسجيل الخطأ هنا
            throw $e;
        }
    }

    /**
     * حذف صراف من النظام.
     *
     * @param int $cashierId معرف الصراف.
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deleteCashier(int $cashierId): bool
    {
        $cashier = User::where('is_cashier', true)->findOrFail($cashierId);

        // يمكن إضافة منطق للتحقق من عدم وجود معاملات مالية مرتبطة بالصراف قبل الحذف
        // if ($cashier->hasTransactions()) {
        //     throw new \Exception('لا يمكن حذف الصراف لوجود معاملات مالية مرتبطة به.');
        // }

        return $cashier->delete();
    }
}