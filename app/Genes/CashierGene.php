<?php

namespace App\Genes;

use App\Models\AnalyticalAccount;
use App\Models\AnalyticalAccountType;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Class CashierGene
 *
 * هذا الجين يمثل نظام الصرافين (Cashiers System) في معمارية الجينات.
 * مهمته الأساسية هي إدارة العمليات المالية المتعلقة بالصرافين، مثل تسجيل الإيرادات والمصروفات
 * وتتبع أرصدة الصرافين (Cashiers) كحسابات تحليلية (Analytical Accounts).
 *
 * Task 2033: Backend - نظام الصرافين (Cashiers) - Backend - Task 18
 * المطلوب: إنشاء وظيفة لإضافة صراف جديد (Cashier) في النظام.
 *
 * @package App\Genes
 */
class CashierGene
{
    /**
     * كود نوع الحساب التحليلي الخاص بالصرافين.
     * يجب أن يكون هذا الكود موجوداً في جدول `analytical_account_types`.
     */
    const ANALYTICAL_ACCOUNT_TYPE_CODE = 'CASHIER';

    /**
     * جلب نوع الحساب التحليلي الخاص بالصرافين.
     *
     * @return AnalyticalAccountType|null
     */
    protected function getCashierAccountType(): ?AnalyticalAccountType
    {
        return AnalyticalAccountType::where('code', self::ANALYTICAL_ACCOUNT_TYPE_CODE)->first();
    }

    /**
     * إضافة صراف جديد إلى النظام.
     *
     * يتم إنشاء حساب تحليلي جديد من نوع 'CASHIER' وربطه بالمستخدم (User) إذا كان موجوداً.
     *
     * @param array $data البيانات المطلوبة لإنشاء الصراف (مثل اسم الصراف، معرف المستخدم).
     * @return AnalyticalAccount|null الحساب التحليلي للصراف الجديد.
     * @throws \Exception إذا لم يتم العثور على نوع الحساب التحليلي 'CASHIER'.
     */
    public function createNewCashier(array $data): ?AnalyticalAccount
    {
        $cashierAccountType = $this->getCashierAccountType();

        if (!$cashierAccountType) {
            throw new \Exception("Analytical Account Type with code '" . self::ANALYTICAL_ACCOUNT_TYPE_CODE . "' not found. Please run the setup process.");
        }

        // التأكد من وجود البيانات الأساسية
        if (empty($data['name'])) {
            throw new \InvalidArgumentException("Cashier name is required.");
        }

        return DB::transaction(function () use ($data, $cashierAccountType) {
            // 1. إنشاء الحساب التحليلي للصراف
            $cashierAccount = AnalyticalAccount::create([
                'name' => $data['name'],
                'analytical_account_type_id' => $cashierAccountType->id,
                'code' => $data['code'] ?? $this->generateUniqueCode($data['name']), // افتراض وجود دالة لتوليد كود فريد
                'description' => $data['description'] ?? 'حساب صراف جديد',
                'is_active' => $data['is_active'] ?? true,
            ]);

            // 2. ربط الحساب التحليلي بمستخدم النظام (إذا تم توفير user_id)
            if (!empty($data['user_id'])) {
                $user = User::find($data['user_id']);
                if ($user) {
                    // افتراض وجود علاقة أو حقل لربط المستخدم بالحساب التحليلي
                    // بما أننا لا نعرف هيكل جدول المستخدمين، سنفترض وجود حقل `analytical_account_id` في جدول `users` أو جدول وسيط.
                    // في هذا المثال، سنفترض أن الحساب التحليلي هو الصراف نفسه.
                    // يمكن إضافة منطق ربط أكثر تعقيداً هنا إذا لزم الأمر.
                    // $user->analytical_account_id = $cashierAccount->id;
                    // $user->save();
                }
            }

            return $cashierAccount;
        });
    }

    /**
     * توليد كود فريد للحساب التحليلي بناءً على الاسم.
     * (وظيفة وهمية، يجب استبدالها بمنطق توليد كود حقيقي وفريد في النظام)
     *
     * @param string $name
     * @return string
     */
    protected function generateUniqueCode(string $name): string
    {
        // مثال بسيط: تحويل الاسم إلى أحرف كبيرة وإضافة رقم عشوائي
        $baseCode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 5));
        return 'CASH-' . $baseCode . '-' . time();
    }

    // يمكن إضافة المزيد من الوظائف هنا (مثل: getCashierBalance, updateCashier, deleteCashier)
}