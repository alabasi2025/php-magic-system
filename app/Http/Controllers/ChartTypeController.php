<?php

namespace App\Http\Controllers;

use App\Models\ChartType;
use Illuminate\Http\Request;

class ChartTypeController extends Controller
{
    public function index()
    {
        $chartTypes = ChartType::ordered()->get();
        return view('chart-types.index', compact('chartTypes'));
    }

    public function create()
    {
        return view('chart-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:chart_types,code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'required|string|max:20',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        ChartType::create($validated);

        return redirect()->route('chart-types.index')
            ->with('success', 'تم إضافة نوع الدليل بنجاح');
    }

    public function edit(ChartType $chartType)
    {
        return view('chart-types.edit', compact('chartType'));
    }

    public function update(Request $request, ChartType $chartType)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:chart_types,code,' . $chartType->id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'required|string|max:20',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $chartType->update($validated);

        return redirect()->route('chart-types.index')
            ->with('success', 'تم تحديث نوع الدليل بنجاح');
    }

    public function destroy(ChartType $chartType)
    {
        // التحقق من عدم وجود أدلة مرتبطة
        if ($chartType->chartOfAccounts()->count() > 0) {
            return redirect()->route('chart-types.index')
                ->with('error', 'لا يمكن حذف هذا النوع لأنه مرتبط بأدلة محاسبية');
        }

        $chartType->delete();

        return redirect()->route('chart-types.index')
            ->with('success', 'تم حذف نوع الدليل بنجاح');
    }
}
