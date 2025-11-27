<?php

namespace App\Http\Controllers\Cashiers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // افتراض أن المستخدمين هم المشتركون

/**
 * @class SubscriptionController
 * @package App\Http\Controllers\Cashiers
 * @description يتحكم في عمليات إدارة الاشتراكات والفواتير لنظام الصرافين (Cashiers Gene).
 *              تمثل هذه الفئة Task 5 في سياق مهام Backend لنظام الصرافين.
 *              بما أن تفاصيل Task 5 غير محددة، نفترض أنها تتعلق بإدارة الاشتراكات الأساسية.
 */
class SubscriptionController extends Controller
{
    /**
     * @var string اسم الجين (Gene) المرتبط بهذا المتحكم.
     */
    protected $geneName = 'Cashiers';

    /**
     * @var string اسم الوحدة (Module) داخل الجين.
     */
    protected $moduleName = 'Subscription';

    /**
     * @method index
     * @description عرض قائمة الاشتراكات الحالية للمستخدم.
     * @param Request $request طلب HTTP.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // 1. التحقق من صلاحيات الوصول (Gene Architecture Best Practice)
        // if (!Auth::user()->can('view-subscriptions', $this->geneName)) {
        //     return response()->json(['message' => 'Unauthorized access.'], 403);
        // }

        /** @var User $user */
        $user = Auth::user();

        // 2. استرداد الاشتراكات (افتراض استخدام Laravel Cashier)
        // يجب أن يكون نموذج المستخدم يستخدم خاصية Billable
        if (!method_exists($user, 'subscriptions')) {
            // في حالة عدم استخدام Cashier أو عدم وجود خاصية Billable
            return response()->json([
                'message' => 'User model is not configured for billing (missing Billable trait).',
                'subscriptions' => []
            ], 500);
        }

        $subscriptions = $user->subscriptions()->get()->map(function ($subscription) {
            return [
                'name' => $subscription->name,
                'stripe_plan' => $subscription->stripe_price,
                'status' => $subscription->stripe_status,
                'ends_at' => $subscription->ends_at ? $subscription->ends_at->format('Y-m-d H:i:s') : null,
                'is_active' => $subscription->active(),
            ];
        });

        // 3. تسجيل الحدث (Gene Architecture Best Practice)
        // Log::info("User {$user->id} viewed their subscriptions in {$this->geneName} Gene.");

        return response()->json([
            'status' => 'success',
            'message' => 'Subscriptions retrieved successfully.',
            'data' => $subscriptions
        ]);
    }

    /**
     * @method getInvoices
     * @description استرداد قائمة فواتير المستخدم.
     * @param Request $request طلب HTTP.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoices(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // 1. التحقق من صلاحيات الوصول (Gene Architecture Best Practice)
        // if (!Auth::user()->can('view-invoices', $this->geneName)) {
        //     return response()->json(['message' => 'Unauthorized access.'], 403);
        // }

        // 2. استرداد الفواتير (افتراض استخدام Laravel Cashier)
        try {
            $invoices = $user->invoices()->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'date' => $invoice->date()->toFormattedDateString(),
                    'total' => $invoice->total(),
                    'download_url' => route('cashiers.invoices.download', $invoice->id),
                ];
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving invoices. Check Cashier configuration.',
                'error' => $e->getMessage()
            ], 500);
        }


        // 3. تسجيل الحدث (Gene Architecture Best Practice)
        // Log::info("User {$user->id} viewed their invoices in {$this->geneName} Gene.");

        return response()->json([
            'status' => 'success',
            'message' => 'Invoices retrieved successfully.',
            'data' => $invoices
        ]);
    }

    // يمكن إضافة دوال أخرى مثل:
    // - store (لإنشاء اشتراك جديد)
    // - update (لتغيير خطة الاشتراك)
    // - cancel (لإلغاء الاشتراك)
    // - resume (لاستئناف الاشتراك)
}