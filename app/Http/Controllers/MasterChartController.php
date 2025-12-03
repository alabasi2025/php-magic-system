<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Services\MasterChartService;
use Illuminate\Http\Request;

/**
 * MasterChartController
 * 
 * Controller لإدارة الدليل الرئيسي ودليل الحسابات الوسيطة
 * 
 * @package App\Http\Controllers
 */
class MasterChartController extends Controller
{
    protected $masterChartService;

    public function __construct(MasterChartService $masterChartService)
    {
        $this->masterChartService = $masterChartService;
    }

    /**
     * عرض الدليل الرئيسي للوحدة
     *
     * @param int $unitId
     * @return \Illuminate\View\View
     */
    public function show($unitId)
    {
        try {
            $unit = Unit::findOrFail($unitId);
            
            // الحصول على أو إنشاء الدليل الرئيسي
            $masterChart = $this->masterChartService->getOrCreateMasterChart($unitId);
            
            // الحصول على دليل الحسابات الوسيطة
            $intermediateMaster = $this->masterChartService->getOrCreateIntermediateMaster($unitId);
            
            // الحصول على جميع الأدلة الفرعية
            $subCharts = $this->masterChartService->getSubCharts($unitId);
            
            // الحصول على فروع دليل الحسابات الوسيطة
            $intermediateBranches = $this->masterChartService->getIntermediateBranches($unitId);
            
            return view('master-chart.show', compact(
                'unit',
                'masterChart',
                'intermediateMaster',
                'subCharts',
                'intermediateBranches'
            ));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحميل الدليل الرئيسي: ' . $e->getMessage());
        }
    }

    /**
     * عرض دليل الحسابات الوسيطة
     *
     * @param int $unitId
     * @return \Illuminate\View\View
     */
    public function showIntermediateMaster($unitId)
    {
        try {
            $unit = Unit::findOrFail($unitId);
            
            // الحصول على دليل الحسابات الوسيطة
            $intermediateMaster = $this->masterChartService->getOrCreateIntermediateMaster($unitId);
            
            // الحصول على جميع الفروع
            $branches = $this->masterChartService->getIntermediateBranches($unitId);
            
            return view('master-chart.intermediate-master', compact(
                'unit',
                'intermediateMaster',
                'branches'
            ));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحميل دليل الحسابات الوسيطة: ' . $e->getMessage());
        }
    }

    /**
     * إنشاء الدليل الرئيسي ودليل الحسابات الوسيطة للوحدة
     *
     * @param int $unitId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function initialize($unitId)
    {
        try {
            $unit = Unit::findOrFail($unitId);
            
            // إنشاء الدليل الرئيسي
            $masterChart = $this->masterChartService->getOrCreateMasterChart($unitId);
            
            // إنشاء دليل الحسابات الوسيطة
            $intermediateMaster = $this->masterChartService->getOrCreateIntermediateMaster($unitId);
            
            return redirect()->route('master-chart.show', $unitId)
                ->with('success', 'تم إنشاء الدليل الرئيسي ودليل الحسابات الوسيطة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء الدليل الرئيسي: ' . $e->getMessage());
        }
    }
}
