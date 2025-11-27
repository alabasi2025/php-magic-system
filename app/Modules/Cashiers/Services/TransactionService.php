<?php

namespace App\Modules\Cashiers\Services;

use App\Modules\Cashiers\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @class TransactionService
 * @package App\Modules\Cashiers\Services
 * @description خدمة إدارة العمليات المالية (Transactions) الخاصة بنظام الصرافين.
 *              تتبع هذه الخدمة مبادئ معمارية الجينات (Gene Architecture) لفصل منطق الأعمال.
 */
class TransactionService
{
    /**
     * جلب قائمة بأحدث العمليات المالية (Transactions) مع ترقيم الصفحات.
     *
     * @param int $perPage عدد العمليات في الصفحة الواحدة
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getRecentTransactions(int $perPage = 15)
    {
        // استخدام Query Builder أو Eloquent لجلب البيانات
        return Transaction::with(['cashier', 'customer']) // افتراض وجود علاقات مع الصراف والعميل
            ->latest()
            ->paginate($perPage);
    }

    /**
     * إنشاء عملية مالية جديدة.
     *
     * @param array $data البيانات الخاصة بالعملية الجديدة
     * @return Transaction
     * @throws \Exception
     */
    public function createTransaction(array $data): Transaction
    {
        DB::beginTransaction();
        try {
            // 1. التحقق من صحة البيانات (يفترض أن يتم التحقق في طبقة أعلى مثل Controller/Request)
            // 2. إنشاء العملية
            $transaction = Transaction::create([
                'cashier_id' => $data['cashier_id'],
                'customer_id' => $data['customer_id'] ?? null,
                'amount' => $data['amount'],
                'type' => $data['type'], // مثلاً: 'sale', 'refund', 'deposit'
                'status' => 'completed',
                'notes' => $data['notes'] ?? null,
            ]);

            // 3. تحديث المخزون أو أي منطق آخر مرتبط بالعملية (يفترض وجود منطق إضافي هنا)
            // ...

            DB::commit();
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            // يمكن تسجيل الخطأ هنا
            throw $e;
        }
    }

    /**
     * حساب إجمالي المبيعات ليوم معين.
     *
     * @param string $date التاريخ المطلوب (بصيغة Y-m-d)
     * @return float
     */
    public function calculateDailySales(string $date): float
    {
        $totalSales = Transaction::whereDate('created_at', $date)
            ->where('type', 'sale')
            ->where('status', 'completed')
            ->sum('amount');

        return (float) $totalSales;
    }
}