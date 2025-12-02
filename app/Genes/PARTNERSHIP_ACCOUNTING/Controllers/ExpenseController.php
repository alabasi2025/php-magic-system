<?php

namespace App\Genes\PARTNERSHIP_ACCOUNTING\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\SimpleExpense;

class ExpenseController extends Controller
{
    /**
     * عرض قائمة المصروفات
     */
    public function index(Request $request)
    {
        $query = SimpleExpense::query();
        
        // التصفية حسب الوحدة
        if ($request->has('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }
        
        // التصفية حسب المشروع
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        // التصفية حسب النوع
        if ($request->has('expense_type')) {
            $query->where('expense_type', $request->expense_type);
        }
        
        // التصفية حسب التاريخ
        if ($request->has('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }
        
        $expenses = $query->with(['unit', 'project'])
            ->orderBy('expense_date', 'desc')
            ->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $expenses
        ]);
    }
    
    /**
     * إنشاء مصروف جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'project_id' => 'nullable|exists:projects,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'expense_type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);
        
        $expense = SimpleExpense::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المصروف بنجاح',
            'data' => $expense
        ], 201);
    }
    
    /**
     * عرض تفاصيل مصروف
     */
    public function show(SimpleExpense $expense)
    {
        $expense->load(['unit', 'project']);
        
        return response()->json([
            'success' => true,
            'data' => $expense
        ]);
    }
    
    /**
     * تحديث مصروف
     */
    public function update(Request $request, SimpleExpense $expense)
    {
        $validated = $request->validate([
            'expense_date' => 'sometimes|required|date',
            'amount' => 'sometimes|required|numeric|min:0',
            'expense_type' => 'sometimes|required|string|max:100',
            'description' => 'nullable|string',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);
        
        $expense->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المصروف بنجاح',
            'data' => $expense
        ]);
    }
    
    /**
     * حذف مصروف
     */
    public function destroy(SimpleExpense $expense)
    {
        $expense->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'تم حذف المصروف بنجاح'
        ]);
    }
    
    /**
     * المصروفات حسب المشروع
     */
    public function byProject($projectId)
    {
        $expenses = SimpleExpense::where('project_id', $projectId)
            ->with(['unit', 'project'])
            ->orderBy('expense_date', 'desc')
            ->get();
        
        $total = $expenses->sum('amount');
        
        return response()->json([
            'success' => true,
            'data' => [
                'expenses' => $expenses,
                'total' => $total
            ]
        ]);
    }
    
    /**
     * المصروفات حسب الوحدة
     */
    public function byUnit($unitId)
    {
        $expenses = SimpleExpense::where('unit_id', $unitId)
            ->with(['unit', 'project'])
            ->orderBy('expense_date', 'desc')
            ->get();
        
        $total = $expenses->sum('amount');
        
        return response()->json([
            'success' => true,
            'data' => [
                'expenses' => $expenses,
                'total' => $total
            ]
        ]);
    }
    
    /**
     * المصروفات حسب النوع
     */
    public function byType(Request $request)
    {
        $query = SimpleExpense::query();
        
        if ($request->has('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }
        
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        $expensesByType = $query->selectRaw('expense_type, SUM(amount) as total')
            ->groupBy('expense_type')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $expensesByType
        ]);
    }
}
