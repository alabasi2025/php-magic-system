<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\ChartAccount;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    /**
     * Display a listing of bank accounts
     */
    public function index()
    {
        $bankAccounts = BankAccount::with(['intermediateAccount', 'unit'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('bank-accounts.index', compact('bankAccounts'));
    }

    /**
     * Show the form for creating a new bank account
     */
    public function create()
    {
        $intermediateAccounts = ChartAccount::orderBy('code')->get();
        $units = Unit::where('is_active', true)->get();

        return view('bank-accounts.create', compact('intermediateAccounts', 'units'));
    }

    /**
     * Store a newly created bank account
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:100',
            'iban' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:20',
            'branch' => 'nullable|string|max:255',
            'currency' => 'required|string|max:10',
            'balance' => 'required|numeric|min:0',
            'intermediate_account_id' => 'required|exists:chart_accounts,id',
            'unit_id' => 'nullable|exists:units,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        BankAccount::create($validated);

        return redirect()->route('bank-accounts.index')
            ->with('success', 'تم إضافة الحساب البنكي بنجاح!');
    }

    /**
     * Display the specified bank account
     */
    public function show(BankAccount $bankAccount)
    {
        $bankAccount->load(['intermediateAccount', 'unit', 'receipts', 'payments']);

        return view('bank-accounts.show', compact('bankAccount'));
    }

    /**
     * Show the form for editing the specified bank account
     */
    public function edit(BankAccount $bankAccount)
    {
        $intermediateAccounts = ChartAccount::orderBy('code')->get();
        $units = Unit::where('is_active', true)->get();

        return view('bank-accounts.edit', compact('bankAccount', 'intermediateAccounts', 'units'));
    }

    /**
     * Update the specified bank account
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:100',
            'iban' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:20',
            'branch' => 'nullable|string|max:255',
            'currency' => 'required|string|max:10',
            'balance' => 'required|numeric|min:0',
            'intermediate_account_id' => 'required|exists:chart_accounts,id',
            'unit_id' => 'nullable|exists:units,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        $bankAccount->update($validated);

        return redirect()->route('bank-accounts.index')
            ->with('success', 'تم تحديث الحساب البنكي بنجاح!');
    }

    /**
     * Remove the specified bank account
     */
    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return redirect()->route('bank-accounts.index')
            ->with('success', 'تم حذف الحساب البنكي بنجاح!');
    }
}
