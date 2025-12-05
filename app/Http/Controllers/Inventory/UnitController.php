<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\UnitRequest;
use App\Models\Inventory\Unit;
use App\Services\Inventory\UnitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    protected UnitService $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
        // تطبيق سياسة الأمان (Authorization)
        $this->authorizeResource(Unit::class, 'unit');
    }

    /**
     * عرض قائمة بجميع الوحدات.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $units = $this->unitService->getAllUnits();
        return view('inventory.units.index', compact('units'));
    }

    /**
     * عرض نموذج إنشاء وحدة جديدة.
     *
     * @return View
     */
    public function create(): View
    {
        $baseUnits = $this->unitService->getBaseUnits();
        return view('inventory.units.create', compact('baseUnits'));
    }

    /**
     * تخزين وحدة جديدة في قاعدة البيانات.
     *
     * @param UnitRequest $request
     * @return RedirectResponse
     */
    public function store(UnitRequest $request): RedirectResponse
    {
        try {
            $this->unitService->createUnit($request->validated());
            return redirect()->route('inventory.units.index')
                             ->with('success', 'تم إنشاء الوحدة بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->withInput()->with('error', 'فشل إنشاء الوحدة: ' . $e->getMessage());
        }
    }

    /**
     * عرض وحدة محددة.
     *
     * @param Unit $unit
     * @return View
     */
    public function show(Unit $unit): View
    {
        $unit->load(['baseUnit', 'derivedUnits']);
        return view('inventory.units.show', compact('unit'));
    }

    /**
     * عرض نموذج تعديل وحدة موجودة.
     *
     * @param Unit $unit
     * @return View
     */
    public function edit(Unit $unit): View
    {
        $baseUnits = $this->unitService->getBaseUnits();
        return view('inventory.units.edit', compact('unit', 'baseUnits'));
    }

    /**
     * تحديث وحدة موجودة في قاعدة البيانات.
     *
     * @param UnitRequest $request
     * @param Unit $unit
     * @return RedirectResponse
     */
    public function update(UnitRequest $request, Unit $unit): RedirectResponse
    {
        try {
            $this->unitService->updateUnit($unit, $request->validated());
            return redirect()->route('inventory.units.index')
                             ->with('success', 'تم تحديث الوحدة بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->withInput()->with('error', 'فشل تحديث الوحدة: ' . $e->getMessage());
        }
    }

    /**
     * حذف وحدة من قاعدة البيانات.
     *
     * @param Unit $unit
     * @return RedirectResponse
     */
    public function destroy(Unit $unit): RedirectResponse
    {
        try {
            $this->unitService->deleteUnit($unit);
            return redirect()->route('inventory.units.index')
                             ->with('success', 'تم حذف الوحدة بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->with('error', 'فشل حذف الوحدة: ' . $e->getMessage());
        }
    }

    /**
     * عرض نموذج تحويل الوحدات.
     *
     * @return View
     */
    public function conversionForm(): View
    {
        $units = $this->unitService->getAllUnits();
        return view('inventory.units.convert', compact('units'));
    }

    /**
     * تنفيذ عملية تحويل الوحدات.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function convert(Request $request): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.0001',
            'from_unit_id' => 'required|integer|exists:units,id',
            'to_unit_id' => 'required|integer|exists:units,id|different:from_unit_id',
        ], [
            'quantity.required' => 'الكمية مطلوبة.',
            'from_unit_id.required' => 'وحدة المصدر مطلوبة.',
            'to_unit_id.required' => 'وحدة الهدف مطلوبة.',
            'to_unit_id.different' => 'يجب أن تكون وحدة المصدر مختلفة عن وحدة الهدف.',
        ]);

        try {
            $convertedQuantity = $this->unitService->convert(
                $request->input('quantity'),
                $request->input('from_unit_id'),
                $request->input('to_unit_id')
            );

            $fromUnit = Unit::find($request->input('from_unit_id'));
            $toUnit = Unit::find($request->input('to_unit_id'));

            $message = sprintf(
                'تم تحويل %.4f %s إلى %.4f %s بنجاح.',
                $request->input('quantity'),
                $fromUnit->symbol,
                $convertedQuantity,
                $toUnit->symbol
            );

            return back()->with('success', $message)
                         ->withInput()
                         ->with('converted_quantity', $convertedQuantity);

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'فشل عملية التحويل: ' . $e->getMessage());
        }
    }
}
