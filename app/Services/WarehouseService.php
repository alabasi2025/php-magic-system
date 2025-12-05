<?php

namespace App\Services;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WarehouseService
{
    /**
     * جلب جميع المخازن مع ترقيم.
     *
     * @param int $perPage عدد العناصر في الصفحة
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllWarehouses(int $perPage = 15)
    {
        // جلب المخازن مع تحميل مسبق لعلاقة المدير
        return Warehouse::with('manager')->paginate($perPage);
    }

    /**
     * إنشاء مخزن جديد.
     *
     * @param array $data بيانات المخزن
     * @return Warehouse
     */
    public function createWarehouse(array $data): Warehouse
    {
        // التأكد من أن قيمة المخزون الافتراضية هي 0.00 إذا لم يتم تمريرها
        $data['current_stock_value'] = $data['current_stock_value'] ?? 0.00;

        return Warehouse::create($data);
    }

    /**
     * جلب مخزن معين.
     *
     * @param int $id معرف المخزن
     * @return Warehouse|null
     */
    public function getWarehouseById(int $id): ?Warehouse
    {
        return Warehouse::with('manager')->find($id);
    }

    /**
     * تحديث بيانات مخزن موجود.
     *
     * @param Warehouse $warehouse نموذج المخزن
     * @param array $data البيانات الجديدة
     * @return Warehouse
     */
    public function updateWarehouse(Warehouse $warehouse, array $data): Warehouse
    {
        $warehouse->update($data);
        return $warehouse;
    }

    /**
     * حذف مخزن.
     *
     * @param Warehouse $warehouse نموذج المخزن
     * @return bool|null
     */
    public function deleteWarehouse(Warehouse $warehouse): ?bool
    {
        return $warehouse->delete();
    }

    /**
     * تبديل حالة تفعيل المخزن (تفعيل/تعطيل).
     *
     * @param Warehouse $warehouse نموذج المخزن
     * @return Warehouse
     */
    public function toggleStatus(Warehouse $warehouse): Warehouse
    {
        $warehouse->is_active = !$warehouse->is_active;
        $warehouse->save();
        return $warehouse;
    }

    /**
     * جلب إحصائيات عامة عن المخازن.
     *
     * @return array
     */
    public function getWarehousesStatistics(): array
    {
        $totalWarehouses = Warehouse::count();
        $activeWarehouses = Warehouse::active()->count();
        $totalCapacity = Warehouse::sum('capacity');
        $totalStockValue = Warehouse::sum('current_stock_value');

        return [
            'total_warehouses' => $totalWarehouses,
            'active_warehouses' => $activeWarehouses,
            'inactive_warehouses' => $totalWarehouses - $activeWarehouses,
            'total_capacity' => $totalCapacity,
            'total_stock_value' => $totalStockValue,
            'average_stock_value' => $totalWarehouses > 0 ? $totalStockValue / $totalWarehouses : 0,
        ];
    }
}
