<?php

namespace App\Genes\Cashiers\Actions;

use App\Genes\Cashiers\Data\SubscriptionData;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Cashiers Gene - Task 15:
 * إنشاء اشتراك جديد للمستخدم باستخدام Laravel Cashier.
 * هذه العملية تفترض أن المستخدم قد قام بالفعل بإضافة وسيلة دفع صالحة.
 *
 * @package App\Genes\Cashiers\Actions
 */
class CreateNewSubscription
{
    use AsAction;

    /**
     * تنفيذ عملية إنشاء الاشتراك.
     *
     * @param User $user المستخدم الذي سيتم إنشاء الاشتراك له.
     * @param SubscriptionData $data بيانات الاشتراك (مثل اسم الخطة، الخ).
     * @return \Laravel\Cashier\Subscription
     * @throws \Exception
     */
    public function handle(User $user, SubscriptionData $data)
    {
        // التحقق من أن المستخدم لديه وسيلة دفع افتراضية
        if (!$user->hasDefaultPaymentMethod()) {
            throw new \Exception('User must have a default payment method to create a subscription.');
        }

        // استخدام معاملة قاعدة البيانات لضمان سلامة العملية
        return DB::transaction(function () use ($user, $data) {
            // إنشاء الاشتراك الجديد
            $subscription = $user->newSubscription($data->name, $data->plan_id)
                ->create($data->payment_method_id); // يتم تمرير payment_method_id هنا إذا لم يكن هناك وسيلة دفع افتراضية، ولكننا نعتمد على hasDefaultPaymentMethod()

            // إضافة توثيق للعملية
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user() ?? $user)
                ->log("Subscription '{$data->name}' created for user ID: {$user->id} with plan ID: {$data->plan_id}");

            return $subscription;
        });
    }
}