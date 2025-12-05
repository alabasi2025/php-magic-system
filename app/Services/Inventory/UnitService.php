<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Unit;
use Illuminate\Database\Eloquent\Collection;

class UnitService
{
    /**
     * جلب جميع الوحدات مع وحداتها الأساسية والمشتقة.
     *
     * @return Collection<int, Unit>
     */
    public function getAllUnits(): Collection
    {
        return Unit::with(['baseUnit', 'derivedUnits'])->get();
    }

    /**
     * جلب الوحدات الأساسية فقط.
     *
     * @return Collection<int, Unit>
     */
    public function getBaseUnits(): Collection
    {
        return Unit::where('is_base_unit', true)->get();
    }

    /**
     * إنشاء وحدة جديدة.
     *
     * @param array $data بيانات الوحدة
     * @return Unit
     */
    public function createUnit(array $data): Unit
    {
        // منطق إضافي للتأكد من تناسق البيانات قبل الإنشاء
        $data = $this->normalizeUnitData($data);

        return Unit::create($data);
    }

    /**
     * تحديث وحدة موجودة.
     *
     * @param Unit $unit نموذج الوحدة
     * @param array $data البيانات الجديدة
     * @return Unit
     */
    public function updateUnit(Unit $unit, array $data): Unit
    {
        // منطق إضافي للتأكد من تناسق البيانات قبل التحديث
        $data = $this->normalizeUnitData($data);

        // منع الوحدة من أن تكون هي نفسها الوحدة الأساسية
        if (isset($data['base_unit_id']) && $data['base_unit_id'] == $unit->id) {
            throw new \InvalidArgumentException('لا يمكن أن تكون الوحدة هي نفسها الوحدة الأساسية.');
        }

        $unit->update($data);
        return $unit;
    }

    /**
     * حذف وحدة.
     *
     * @param Unit $unit نموذج الوحدة
     * @return bool|null
     */
    public function deleteUnit(Unit $unit): ?bool
    {
        // منع حذف وحدة إذا كانت تستخدم كوحدة أساسية لوحدات أخرى
        if ($unit->derivedUnits()->exists()) {
            throw new \RuntimeException('لا يمكن حذف هذه الوحدة لأنها تستخدم كوحدة أساسية لوحدات أخرى.');
        }

        return $unit->delete();
    }

    /**
     * تحويل كمية من وحدة إلى أخرى.
     *
     * @param float $quantity الكمية المراد تحويلها
     * @param int $fromUnitId معرف الوحدة المصدر
     * @param int $toUnitId معرف الوحدة الهدف
     * @return float الكمية المحولة
     */
    public function convert(float $quantity, int $fromUnitId, int $toUnitId): float
    {
        $fromUnit = Unit::find($fromUnitId);
        $toUnit = Unit::find($toUnitId);

        if (!$fromUnit || !$toUnit) {
            throw new \InvalidArgumentException('الوحدة المصدر أو الهدف غير موجودة.');
        }

        // 1. التحويل إلى الوحدة الأساسية
        $quantityInBase = $fromUnit->convertToBase($quantity);

        // 2. التحويل من الوحدة الأساسية إلى الوحدة الهدف
        $convertedQuantity = $toUnit->convertFromBase($quantityInBase);

        return $convertedQuantity;
    }

    /**
     * تطبيع بيانات الوحدة (مثل تعيين معامل التحويل 1.0 للوحدات الأساسية).
     *
     * @param array $data
     * @return array
     */
    protected function normalizeUnitData(array $data): array
    {
        if (isset($data['is_base_unit']) && $data['is_base_unit']) {
            $data['base_unit_id'] = null;
            $data['conversion_factor'] = 1.0;
        }

        return $data;
    }
}
