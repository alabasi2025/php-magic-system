<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Exception;

class StockMovementService
{
    /**
     * تسجيل حركة مخزون جديدة وتحديث رصيد الصنف في المخزن.
     *
     * @param array $data بيانات الحركة
     * @return StockMovement
     * @throws Exception
     */
    public function createMovement(array $data): StockMovement
    {
        // استخدام المعاملات لضمان سلامة البيانات
        return DB::transaction(function () use ($data) {
            $warehouseId = $data['warehouse_id'];
            $itemId = $data['item_id'];
            $quantity = $data['quantity'];
            $movementType = $data['movement_type'];

            // 1. تحديد رصيد الصنف الحالي في المخزن
            // نفترض وجود جدول 'stock_balances' أو طريقة لحساب الرصيد الحالي.
            // لغرض هذا المثال، سنقوم بحساب الرصيد من آخر حركة.
            $lastMovement = StockMovement::where('warehouse_id', $warehouseId)
                ->where('item_id', $itemId)
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->first();

            $balanceBefore = $lastMovement ? $lastMovement->balance_after : 0.00;

            // 2. حساب الرصيد بعد الحركة
            $balanceAfter = $balanceBefore + $quantity;

            // التحقق من عدم سلبية الرصيد في حركات الخروج
            if (in_array($movementType, ['out', 'transfer']) && $balanceAfter < 0) {
                throw new Exception('الرصيد غير كافٍ لتسجيل هذه الحركة.');
            }

            // 3. إعداد بيانات الحركة
            $movementData = array_merge($data, [
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'date' => now(), // تسجيل تاريخ الحركة الحالي
                'created_by' => auth()->id() ?? null,
            ]);

            // 4. إنشاء الحركة
            $movement = StockMovement::create($movementData);

            // 5. تحديث جدول الأرصدة (إذا كان موجوداً)
            // في نظام حقيقي، يجب تحديث جدول أرصدة المخزون (StockBalance) هنا.
            // مثال افتراضي:
            // StockBalance::updateOrCreate(
            //     ['warehouse_id' => $warehouseId, 'item_id' => $itemId],
            //     ['balance' => $balanceAfter]
            // );

            return $movement;
        });
    }

    /**
     * جلب سجل حركات المخزون مع ترشيح اختياري.
     *
     * @param array $filters مرشحات البحث
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getMovementsHistory(array $filters = [])
    {
        $query = StockMovement::with(['warehouse', 'item', 'creator'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        // ترشيح حسب المخزن
        if (isset($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        // ترشيح حسب الصنف
        if (isset($filters['item_id'])) {
            $query->where('item_id', $filters['item_id']);
        }

        // ترشيح حسب نوع الحركة
        if (isset($filters['movement_type'])) {
            $query->where('movement_type', $filters['movement_type']);
        }

        // ترشيح حسب التاريخ
        if (isset($filters['start_date'])) {
            $query->whereDate('date', '>=', $filters['start_date']);
        }
        if (isset($filters['end_date'])) {
            $query->whereDate('date', '<=', $filters['end_date']);
        }

        return $query->paginate(20);
    }

    /**
     * جلب تقرير حركة صنف معين.
     *
     * @param int $itemId معرف الصنف
     * @param int|null $warehouseId معرف المخزن (اختياري)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getItemMovementReport(int $itemId, ?int $warehouseId = null)
    {
        $query = StockMovement::where('item_id', $itemId)
            ->with(['warehouse', 'creator'])
            ->orderBy('date')
            ->orderBy('id');

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        return $query->paginate(50);
    }

    /**
     * جلب تقرير حركة مخزن معين.
     *
     * @param int $warehouseId معرف المخزن
     * @param int|null $itemId معرف الصنف (اختياري)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getWarehouseMovementReport(int $warehouseId, ?int $itemId = null)
    {
        $query = StockMovement::where('warehouse_id', $warehouseId)
            ->with(['item', 'creator'])
            ->orderBy('date')
            ->orderBy('id');

        if ($itemId) {
            $query->where('item_id', $itemId);
        }

        return $query->paginate(50);
    }
}
