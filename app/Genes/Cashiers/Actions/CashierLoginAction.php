<?php

namespace App\Genes\Cashiers\Actions;

use App\Genes\Cashiers\Data\CashierLoginData;
use App\Genes\Cashiers\Models\Cashier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @Gene Cashiers
 * @Task 2073
 * @Category Business Logic
 * @Description Task 13: تسجيل دخول الصراف
 *
 * يقوم هذا الإجراء بمعالجة منطق تسجيل دخول الصراف.
 * يتلقى بيانات تسجيل الدخول (رقم الصراف وكلمة المرور) ويتحقق من صحتها.
 * في حالة النجاح، يقوم بتسجيل دخول الصراف في النظام.
 */
class CashierLoginAction
{
    /**
     * تنفيذ إجراء تسجيل دخول الصراف.
     *
     * @param CashierLoginData $data كائن بيانات تسجيل الدخول.
     * @return Cashier الصراف الذي تم تسجيل دخوله.
     * @throws ValidationException في حالة فشل التحقق من الصحة.
     */
    public function execute(CashierLoginData $data): Cashier
    {
        // 1. البحث عن الصراف باستخدام رقم الصراف (cashier_number)
        $cashier = Cashier::where('cashier_number', $data->cashier_number)->first();

        // 2. التحقق من وجود الصراف وصحة كلمة المرور
        if (!$cashier || !Hash::check($data->password, $cashier->password)) {
            // رمي استثناء تحقق في حالة فشل تسجيل الدخول
            throw ValidationException::withMessages([
                'cashier_number' => [__('auth.failed')], // استخدام رسالة خطأ عامة لأسباب أمنية
            ]);
        }

        // 3. تسجيل دخول الصراف
        // ملاحظة: يجب التأكد من أن موديل Cashier يستخدم خاصية Authenticatable
        Auth::guard('cashier')->login($cashier);

        // 4. تحديث حالة الصراف أو تسجيل الجلسة (يمكن إضافة منطق إضافي هنا)
        // مثال: تسجيل وقت آخر تسجيل دخول
        $cashier->last_login_at = now();
        $cashier->save();

        return $cashier;
    }
}