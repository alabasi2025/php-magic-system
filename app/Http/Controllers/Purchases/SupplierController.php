<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->get();
        return view('purchases.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $accounts = \App\Models\ChartAccount::where('is_active', true)
            ->where('account_type', 'supplier') // حسابات الموردين فقط
            ->orderBy('code')
            ->get();
        
        return view('purchases.suppliers.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'tax_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'account_id' => 'nullable|exists:chart_accounts,id',
            'opening_balance' => 'nullable|numeric',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        // Generate unique code
        $lastSupplier = Supplier::latest('id')->first();
        $nextNumber = $lastSupplier ? ($lastSupplier->id + 1) : 1;
        $validated['code'] = 'SUP-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        
        // Set defaults
        $validated['initial_balance'] = $validated['opening_balance'] ?? 0;
        $validated['balance'] = $validated['initial_balance'];
        $validated['is_active'] = $validated['status'] === 'active';
        $validated['payment_terms'] = 'cash';
        $validated['credit_limit'] = 0;
        $validated['credit_days'] = 0;

        Supplier::create($validated);

        return redirect()->route('purchases.suppliers.index')
            ->with('success', 'تم إضافة المورد بنجاح');
    }

    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('purchases.suppliers.show', compact('supplier'));
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        
        $accounts = \App\Models\ChartAccount::where('is_active', true)
            ->where('account_type', 'supplier') // حسابات الموردين فقط
            ->orderBy('code')
            ->get();
        
        return view('purchases.suppliers.edit', compact('supplier', 'accounts'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'tax_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'account_id' => 'nullable|exists:chart_accounts,id',
            'opening_balance' => 'nullable|numeric',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $validated['status'] === 'active';
        
        $supplier->update($validated);

        return redirect()->route('purchases.suppliers.index')
            ->with('success', 'تم تحديث بيانات المورد بنجاح');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('purchases.suppliers.index')
            ->with('success', 'تم حذف المورد بنجاح');
    }

    public function transactions($id)
    {
        $supplier = Supplier::findOrFail($id);
        
        // جلب المعاملات (حالياً فارغة، سيتم إضافتها لاحقاً)
        $transactions = [];
        $totalPurchases = 0;
        $totalPayments = 0;
        $transactionsCount = 0;
        
        return view('purchases.suppliers.transactions', compact(
            'supplier',
            'transactions',
            'totalPurchases',
            'totalPayments',
            'transactionsCount'
        ));
    }

    public function search(Request $request)
    {
        return response()->json([]);
    }

    public function getBalance($id)
    {
        return response()->json(['balance' => 0]);
    }
}
