<?php

namespace App\Services\Cashiers;

use App\Models\Cashier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @class CashierService
 * @package App\Services\Cashiers
 * @brief خدمة لإدارة عمليات الصرافين في نظام Cashiers Gene.
 *
 * هذه الخدمة مسؤولة عن منطق الأعمال المتعلق بالصرافين، مثل إنشاء صراف جديد،
 * تحديث بياناته، أو استعراض سجلاته.
 */
class CashierService
{
    /**
     * @brief إنشاء صراف جديد.
     *
     * @param array $data البيانات المطلوبة لإنشاء الصراف.
     * @return Cashier|null نموذج الصراف الذي تم إنشاؤه، أو null في حالة الفشل.
     */
    public function createCashier(array $data): ?Cashier
    {
        DB::beginTransaction();
        try {
            // التحقق من البيانات المدخلة يمكن أن يتم في طبقة الـ Request
            // لكن يمكن إضافة بعض التحققات المنطقية هنا.

            $cashier = Cashier::create([
                'user_id' => $data['user_id'], // يجب أن يكون الصراف مرتبطًا بمستخدم
                'branch_id' => $data['branch_id'] ?? null, // فرع الصراف
                'status' => $data['status'] ?? 'active', // حالة الصراف الافتراضية
                // ... إضافة حقول أخرى ذات صلة
            ]);

            DB::commit();
            Log::info("Cashier created successfully: ID " . $cashier->id);
            return $cashier;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create cashier: " . $e->getMessage());
            // يمكن رمي استثناء مخصص هنا
            return null;
        }
    }

    /**
     * @brief تحديث بيانات صراف موجود.
     *
     * @param int $cashierId معرف الصراف.
     * @param array $data البيانات الجديدة للتحديث.
     * @return Cashier|null نموذج الصراف المحدث، أو null في حالة عدم العثور عليه أو الفشل.
     */
    public function updateCashier(int $cashierId, array $data): ?Cashier
    {
        $cashier = Cashier::find($cashierId);

        if (!$cashier) {
            return null;
        }

        DB::beginTransaction();
        try {
            $cashier->update($data);

            DB::commit();
            Log::info("Cashier updated successfully: ID " . $cashier->id);
            return $cashier;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to update cashier ID {$cashierId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * @brief استعراض صراف بواسطة المعرف.
     *
     * @param int $cashierId معرف الصراف.
     * @return Cashier|null نموذج الصراف، أو null في حالة عدم العثور عليه.
     */
    public function getCashierById(int $cashierId): ?Cashier
    {
        return Cashier::find($cashierId);
    }

    /**
     * @brief استعراض جميع الصرافين مع إمكانية التصفية والترقيم.
     *
     * @param array $filters مصفوفة للتصفية (مثال: ['status' => 'active']).
     * @param int $perPage عدد العناصر في كل صفحة.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator قائمة الصرافين المرقّمة.
     */
    public function getAllCashiers(array $filters = [], int $perPage = 15)
    {
        $query = Cashier::query();

        // تطبيق التصفية
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $query->where($key, $value);
            }
        }

        return $query->paginate($perPage);
    }

    // ... يمكن إضافة المزيد من الدوال مثل:
    // - إغلاق وردية الصراف (closeShift)
    // - جلب سجلات المعاملات للصراف (getCashierTransactions)
    // - تفعيل/تعطيل الصراف (toggleStatus)
}