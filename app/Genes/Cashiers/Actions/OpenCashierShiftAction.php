<?php

namespace App\Genes\Cashiers\Actions;

use App\Genes\Cashiers\Models\CashierShift;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * @author Manus AI
 * @description Action لفتح وردية صراف جديدة.
 *
 * هذه الـ Action مسؤولة عن إنشاء سجل وردية صراف جديد (CashierShift)
 * وتعيين حالته كـ 'open' مع تسجيل وقت البدء والمستخدم الحالي.
 *
 * Task 2025: [نظام الصرافين (Cashiers)] Backend - نظام الصرافين (Cashiers) - Task 10
 */
class OpenCashierShiftAction
{
    use AsAction;

    /**
     * تنفيذ الـ Action لفتح وردية الصراف.
     *
     * @param float $starting_cash المبلغ النقدي الافتتاحي للوردية.
     * @return CashierShift
     */
    public function handle(float $starting_cash = 0.00): CashierShift
    {
        // التحقق من وجود وردية مفتوحة بالفعل لنفس المستخدم (اختياري، يمكن إضافته لاحقاً كتحقق إضافي)
        // $existingShift = CashierShift::where('user_id', Auth::id())->where('status', 'open')->first();
        // if ($existingShift) {
        //     throw new \Exception('A shift is already open for this cashier.');
        // }

        // إنشاء وردية صراف جديدة
        $shift = CashierShift::create([
            'user_id' => Auth::id(),
            'starting_cash' => $starting_cash,
            'status' => 'open', // تعيين الحالة كـ 'مفتوحة'
            'opened_at' => now(), // تسجيل وقت فتح الوردية
        ]);

        // يمكن إضافة منطق إرسال إشعار أو تسجيل حدث هنا

        return $shift;
    }

    /**
     * تحديد قواعد التحقق (Validation Rules) للـ Action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'starting_cash' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * تحديد أذونات المستخدم (Authorization) لتنفيذ الـ Action.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // يجب أن يكون المستخدم مسجلاً للدخول ولديه إذن 'open-cashier-shift'
        return Auth::check() && Auth::user()->can('open-cashier-shift');
    }
}