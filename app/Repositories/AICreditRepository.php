<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\AICreditTransaction; // نفترض وجود هذا النموذج لتسجيل المعاملات

/**
 * AICreditRepository
 *
 * مستودع (Repository) لإدارة أرصدة الذكاء الاصطناعي للمستخدمين.
 * يتبع أفضل الممارسات في Laravel باستخدام Eloquent والمعاملات (Transactions) لضمان سلامة البيانات.
 *
 * @package App\Repositories
 */
class AICreditRepository // يفضل أن يقوم بتنفيذ واجهة (Interface) مثل AICreditRepositoryInterface
{
    /**
     * استرداد الرصيد المتاح من أرصدة الذكاء الاصطناعي للمستخدم.
     * نفترض أن الرصيد مخزن في عمود 'ai_credits' في جدول المستخدمين.
     *
     * @param User $user نموذج المستخدم.
     * @return int الرصيد المتاح.
     */
    public function getAvailableCredits(User $user): int
    {
        // استخدام سمة (Attribute) مباشرة من نموذج المستخدم
        return (int) $user->ai_credits;
    }

    /**
     * التحقق مما إذا كان لدى المستخدم رصيد كافٍ لإجراء عملية ما.
     *
     * @param User $user نموذج المستخدم.
     * @param int $requiredAmount المبلغ المطلوب.
     * @return bool
     */
    public function hasSufficientCredits(User $user, int $requiredAmount): bool
    {
        return $this->getAvailableCredits($user) >= $requiredAmount;
    }

    /**
     * إضافة أرصدة إلى رصيد المستخدم.
     * يتم تنفيذ العملية داخل معاملة (Transaction) لضمان تحديث الرصيد وتسجيل المعاملة معًا.
     *
     * @param User $user نموذج المستخدم.
     * @param int $amount المبلغ المراد إضافته.
     * @param string|null $description وصف المعاملة.
     * @return bool
     */
    public function addCredits(User $user, int $amount, ?string $description = null): bool
    {
        if ($amount <= 0) {
            return false;
        }

        try {
            DB::beginTransaction();

            // 1. تحديث رصيد المستخدم
            $user->increment('ai_credits', $amount);

            // 2. تسجيل المعاملة (نفترض وجود نموذج AICreditTransaction)
            if (class_exists(AICreditTransaction::class)) {
                AICreditTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'add', // نوع المعاملة: إضافة
                    'amount' => $amount,
                    'description' => $description ?? 'إضافة رصيد ذكاء اصطناعي',
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // يمكن تسجيل الخطأ هنا
            return false;
        }
    }

    /**
     * خصم أرصدة من رصيد المستخدم.
     * يتم تنفيذ العملية داخل معاملة (Transaction) لضمان تحديث الرصيد وتسجيل المعاملة معًا.
     *
     * @param User $user نموذج المستخدم.
     * @param int $amount المبلغ المراد خصمه.
     * @param string|null $description وصف المعاملة.
     * @return bool
     */
    public function deductCredits(User $user, int $amount, ?string $description = null): bool
    {
        if ($amount <= 0) {
            return false;
        }

        // التحقق من الرصيد قبل الخصم
        if (!$this->hasSufficientCredits($user, $amount)) {
            return false; // لا يوجد رصيد كافٍ
        }

        try {
            DB::beginTransaction();

            // 1. تحديث رصيد المستخدم
            // استخدام decrement لضمان تحديث آمن للرصيد
            $user->decrement('ai_credits', $amount);

            // 2. تسجيل المعاملة (نفترض وجود نموذج AICreditTransaction)
            if (class_exists(AICreditTransaction::class)) {
                AICreditTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'deduct', // نوع المعاملة: خصم
                    'amount' => $amount,
                    'description' => $description ?? 'خصم رصيد ذكاء اصطناعي للاستخدام',
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // يمكن تسجيل الخطأ هنا
            return false;
        }
    }

    /**
     * استرداد سجل معاملات الرصيد للمستخدم.
     *
     * @param User $user نموذج المستخدم.
     * @param int $limit عدد المعاملات المراد استردادها.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTransactionHistory(User $user, int $limit = 10)
    {
        if (class_exists(AICreditTransaction::class)) {
            return AICreditTransaction::where('user_id', $user->id)
                ->latest()
                ->limit($limit)
                ->get();
        }

        return collect();
    }
}
