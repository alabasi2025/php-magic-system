<?php

namespace App\Genes\Cashiers\Services;

use App\Genes\Cashiers\Models\Cashier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * @class CashiersIntegrationService
 * @package App\Genes\Cashiers\Services
 *
 * خدمة مخصصة للتعامل مع عمليات التكامل الخارجية لنظام الصرافين.
 * تفترض هذه الخدمة وجود نظام خارجي للتكامل معه (مثلاً، نظام محاسبة أو نظام جرد).
 */
class CashiersIntegrationService
{
    /**
     * تنفيذ مهمة التكامل رقم 2 (Task 2097).
     * تفترض هذه المهمة إرسال بيانات الصراف إلى نظام خارجي.
     *
     * @param Cashier $cashier نموذج الصراف المراد إرسال بياناته.
     * @return bool نتيجة عملية التكامل.
     */
    public function integrateCashierData(Cashier $cashier): bool
    {
        // TODO: يجب استبدال هذا بعنوان URL الفعلي لنظام التكامل الخارجي
        $externalApiUrl = config('services.external_system.cashiers_api_url');

        if (!$externalApiUrl) {
            Log::error('Cashiers Integration API URL is not configured.');
            return false;
        }

        try {
            // تحضير البيانات للإرسال
            $data = [
                'cashier_id' => $cashier->id,
                'name' => $cashier->name,
                'status' => $cashier->status,
                'last_updated_at' => now()->toDateTimeString(),
            ];

            // إرسال طلب POST إلى النظام الخارجي
            $response = Http::timeout(10)->post($externalApiUrl, $data);

            // التحقق من نجاح الطلب
            if ($response->successful()) {
                Log::info("Successfully integrated cashier data for ID: {$cashier->id}", ['response' => $response->json()]);
                return true;
            }

            // تسجيل الخطأ في حالة فشل الطلب
            Log::error("Failed to integrate cashier data for ID: {$cashier->id}", [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            // تسجيل أي استثناء يحدث أثناء عملية التكامل
            Log::error("Exception during cashier data integration for ID: {$cashier->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * مثال على دالة تكامل أخرى يمكن إضافتها لاحقاً.
     *
     * @param array $transactionData بيانات العملية المالية.
     * @return bool
     */
    public function syncTransaction(array $transactionData): bool
    {
        // منطق مزامنة العمليات المالية
        return true;
    }
}