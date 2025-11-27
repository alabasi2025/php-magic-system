<?php

namespace App\Services;

use App\Models\CashierSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * خدمة لإدارة عمليات جلسات الصرافين (Cashier Sessions).
 */
class CashierSessionService
{
    /**
     * بدء جلسة صراف جديدة.
     *
     * @param User $user الصراف الذي يبدأ الجلسة.
     * @param float $openingCash الرصيد النقدي الأولي.
     * @param int|null $posId معرف نقطة البيع المرتبطة بالجلسة.
     * @param string|null $notes ملاحظات حول الجلسة.
     * @return CashierSession
     * @throws ValidationException إذا كانت هناك جلسة مفتوحة بالفعل للصراف.
     */
    public function startSession(User $user, float $openingCash, ?int $posId = null, ?string $notes = null): CashierSession
    {
        // التحقق مما إذا كانت هناك جلسة مفتوحة بالفعل لهذا الصراف
        if ($this->hasOpenSession($user)) {
            throw ValidationException::withMessages([
                'user_id' => 'يوجد بالفعل جلسة مفتوحة لهذا الصراف.',
            ]);
        }

        return DB::transaction(function () use ($user, $openingCash, $posId, $notes) {
            $session = CashierSession::create([
                'user_id' => $user->id,
                'pos_id' => $posId,
                'opening_cash' => $openingCash,
                'status' => 'open',
                'notes' => $notes,
            ]);

            // يمكن إضافة منطق إضافي هنا، مثل تسجيل حدث (Log Event)

            return $session;
        });
    }

    /**
     * إغلاق جلسة صراف مفتوحة.
     *
     * @param CashierSession $session الجلسة المراد إغلاقها.
     * @param float $closingCash الرصيد النقدي النهائي.
     * @param string|null $notes ملاحظات إضافية عند الإغلاق.
     * @return CashierSession
     * @throws ValidationException إذا كانت الجلسة مغلقة بالفعل.
     */
    public function closeSession(CashierSession $session, float $closingCash, ?string $notes = null): CashierSession
    {
        if ($session->status !== 'open') {
            throw ValidationException::withMessages([
                'session_id' => 'لا يمكن إغلاق جلسة غير مفتوحة.',
            ]);
        }

        return DB::transaction(function () use ($session, $closingCash, $notes) {
            $session->update([
                'end_time' => now(),
                'closing_cash' => $closingCash,
                'status' => 'closed',
                'notes' => $session->notes . "\n\n" . ($notes ? "ملاحظات الإغلاق: " . $notes : ''),
            ]);

            // يمكن إضافة منطق إضافي هنا، مثل مقارنة الرصيد المتوقع بالرصيد الفعلي وتسجيل الفروقات

            return $session;
        });
    }

    /**
     * الحصول على الجلسة المفتوحة الحالية لصراف معين.
     *
     * @param User $user
     * @return CashierSession|null
     */
    public function getOpenSession(User $user): ?CashierSession
    {
        return CashierSession::open()->where('user_id', $user->id)->first();
    }

    /**
     * التحقق مما إذا كان لدى الصراف جلسة مفتوحة حاليًا.
     *
     * @param User $user
     * @return bool
     */
    public function hasOpenSession(User $user): bool
    {
        return $this->getOpenSession($user) !== null;
    }
}