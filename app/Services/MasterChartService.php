<?php

namespace App\Services;

use App\Models\ChartGroup;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * MasterChartService
 * 
 * خدمة إدارة الدليل الرئيسي ودليل الحسابات الوسيطة
 * 
 * @package App\Services
 */
class MasterChartService
{
    /**
     * إنشاء أو الحصول على الدليل الرئيسي للوحدة
     *
     * @param int $unitId
     * @return ChartGroup
     */
    public function getOrCreateMasterChart(int $unitId): ChartGroup
    {
        $masterChart = ChartGroup::where('unit_id', $unitId)
            ->where('type', 'master_chart')
            ->where('is_master', true)
            ->first();

        if (!$masterChart) {
            $unit = Unit::findOrFail($unitId);
            
            $masterChart = ChartGroup::create([
                'unit_id' => $unitId,
                'code' => $unit->code . '-MASTER',
                'name' => 'الدليل الرئيسي - ' . $unit->name,
                'name_en' => 'Master Chart - ' . $unit->name_en,
                'type' => 'master_chart',
                'is_master' => true,
                'description' => 'الدليل الرئيسي للوحدة التنظيمية',
                'icon' => 'fas fa-sitemap',
                'color' => 'teal',
                'is_active' => true,
                'sort_order' => 0,
                'created_by' => Auth::id(),
            ]);
        }

        return $masterChart;
    }

    /**
     * إنشاء أو الحصول على دليل الحسابات الوسيطة للوحدة
     *
     * @param int $unitId
     * @return ChartGroup
     */
    public function getOrCreateIntermediateMaster(int $unitId): ChartGroup
    {
        $masterChart = $this->getOrCreateMasterChart($unitId);

        $intermediateMaster = ChartGroup::where('unit_id', $unitId)
            ->where('parent_group_id', $masterChart->id)
            ->where('type', 'intermediate_master')
            ->first();

        if (!$intermediateMaster) {
            $unit = Unit::findOrFail($unitId);
            
            $intermediateMaster = ChartGroup::create([
                'unit_id' => $unitId,
                'parent_group_id' => $masterChart->id,
                'code' => $unit->code . '-INT',
                'name' => 'دليل الحسابات الوسيطة',
                'name_en' => 'Intermediate Accounts Chart',
                'type' => 'intermediate_master',
                'description' => 'دليل الحسابات الوسيطة للصناديق والبنوك والمحافظ',
                'icon' => 'fas fa-exchange-alt',
                'color' => 'cyan',
                'is_active' => true,
                'sort_order' => 1,
                'created_by' => Auth::id(),
            ]);
        }

        return $intermediateMaster;
    }

    /**
     * إنشاء فرع جديد في دليل الحسابات الوسيطة عند إنشاء دليل جديد
     *
     * @param ChartGroup $sourceGroup الدليل الأصلي
     * @return ChartGroup|null
     */
    public function createIntermediateBranch(ChartGroup $sourceGroup): ?ChartGroup
    {
        // تحقق من أن الدليل ليس master أو intermediate_master
        if (in_array($sourceGroup->type, ['master_chart', 'intermediate_master'])) {
            return null;
        }

        // الحصول على دليل الحسابات الوسيطة
        $intermediateMaster = $this->getOrCreateIntermediateMaster($sourceGroup->unit_id);

        // تحقق من عدم وجود فرع مسبق
        $existingBranch = ChartGroup::where('parent_group_id', $intermediateMaster->id)
            ->where('source_group_id', $sourceGroup->id)
            ->first();

        if ($existingBranch) {
            return $existingBranch;
        }

        // إنشاء الفرع الجديد
        $branch = ChartGroup::create([
            'unit_id' => $sourceGroup->unit_id,
            'parent_group_id' => $intermediateMaster->id,
            'source_group_id' => $sourceGroup->id,
            'code' => $sourceGroup->code . '-INT',
            'name' => 'فرع: ' . $sourceGroup->name,
            'name_en' => 'Branch: ' . $sourceGroup->name_en,
            'type' => $sourceGroup->type, // نفس النوع
            'description' => 'فرع الحسابات الوسيطة لـ ' . $sourceGroup->name,
            'icon' => $sourceGroup->icon,
            'color' => $sourceGroup->color,
            'is_active' => true,
            'sort_order' => $sourceGroup->sort_order,
            'created_by' => Auth::id(),
        ]);

        return $branch;
    }

    /**
     * إنشاء دليل فرعي جديد تحت الدليل الرئيسي
     *
     * @param int $unitId
     * @param array $data
     * @return ChartGroup
     */
    public function createSubChart(int $unitId, array $data): ChartGroup
    {
        DB::beginTransaction();

        try {
            // الحصول على الدليل الرئيسي
            $masterChart = $this->getOrCreateMasterChart($unitId);

            // إنشاء الدليل الفرعي
            $subChart = ChartGroup::create(array_merge($data, [
                'unit_id' => $unitId,
                'parent_group_id' => $masterChart->id,
                'is_master' => false,
                'created_by' => Auth::id(),
            ]));

            // إنشاء فرع تلقائي في دليل الحسابات الوسيطة
            $this->createIntermediateBranch($subChart);

            DB::commit();

            return $subChart;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * الحصول على جميع الأدلة الفرعية للوحدة (بدون الدليل الرئيسي ودليل الحسابات الوسيطة)
     *
     * @param int $unitId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSubCharts(int $unitId)
    {
        $masterChart = $this->getOrCreateMasterChart($unitId);

        return ChartGroup::where('parent_group_id', $masterChart->id)
            ->where('type', '!=', 'intermediate_master')
            ->with(['accounts', 'intermediateBranches'])
            ->ordered()
            ->get();
    }

    /**
     * الحصول على فروع دليل الحسابات الوسيطة
     *
     * @param int $unitId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getIntermediateBranches(int $unitId)
    {
        $intermediateMaster = $this->getOrCreateIntermediateMaster($unitId);

        return ChartGroup::where('parent_group_id', $intermediateMaster->id)
            ->with(['sourceGroup', 'accounts'])
            ->ordered()
            ->get();
    }
}
