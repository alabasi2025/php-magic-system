<?php

namespace App\Http\Controllers;

use App\Models\CashReceipt;
use App\Models\CashBox;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashReceiptController extends Controller
{
    /**
     * Display a listing of cash receipts
     */
    public function index()
    {
        $receipts = CashReceipt::with(['account', 'creator'])
            ->orderBy('receipt_date', 'desc')
            ->paginate(20);

        // Statistics
        $stats = [
            'total' => CashReceipt::count(),
            'pending' => CashReceipt::where('status', 'pending')->count(),
            'approved' => CashReceipt::where('status', 'approved')->count(),
            'posted' => CashReceipt::where('status', 'posted')->count(),
            'total_amount' => CashReceipt::whereIn('status', ['approved', 'posted'])->sum('amount'),
        ];

        return view('cash-receipts.index', compact('receipts', 'stats'));
    }

    /**
     * Show the form for creating a new cash receipt
     */
    public function create()
    {
        $cashBoxes = CashBox::where('is_active', true)->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $receiptNumber = CashReceipt::generateReceiptNumber();

        return view('cash-receipts.create', compact('cashBoxes', 'bankAccounts', 'receiptNumber'));
    }

    /**
     * Store a newly created cash receipt
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receipt_date' => 'required|date',
            'account_type' => 'required|in:cash_box,bank_account',
            'account_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:10',
            'received_from' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,check,transfer,card',
            'check_number' => 'nullable|string|max:100',
            'check_date' => 'nullable|date',
            'check_bank' => 'nullable|string|max:255',
            'transfer_reference' => 'nullable|string|max:255',
            'card_reference' => 'nullable|string|max:255',
            'description' => 'required|string',
            'notes' => 'nullable|string',
            'category' => 'required|in:sales,services,loan,investment,other',
            'reference_number' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $validated['receipt_number'] = CashReceipt::generateReceiptNumber();
            $validated['status'] = 'draft';
            $validated['created_by'] = Auth::id();
            $validated['exchange_rate'] = 1; // Default
            $validated['amount_in_base_currency'] = $validated['amount'];

            $receipt = CashReceipt::create($validated);

            // Update account balance
            $accountModel = $validated['account_type'] === 'cash_box' ? CashBox::class : BankAccount::class;
            $account = $accountModel::find($validated['account_id']);
            $account->increment('balance', $validated['amount']);

            DB::commit();

            return redirect()->route('cash-receipts.show', $receipt)
                ->with('success', 'تم إنشاء سند القبض بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء سند القبض: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified cash receipt
     */
    public function show(CashReceipt $cashReceipt)
    {
        $cashReceipt->load(['account', 'creator', 'approver', 'poster']);

        return view('cash-receipts.show', compact('cashReceipt'));
    }

    /**
     * Show the form for editing the specified cash receipt
     */
    public function edit(CashReceipt $cashReceipt)
    {
        if (!in_array($cashReceipt->status, ['draft', 'pending'])) {
            return redirect()->route('cash-receipts.show', $cashReceipt)
                ->with('error', 'لا يمكن تعديل سند معتمد أو مرحل!');
        }

        $cashBoxes = CashBox::where('is_active', true)->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();

        return view('cash-receipts.edit', compact('cashReceipt', 'cashBoxes', 'bankAccounts'));
    }

    /**
     * Update the specified cash receipt
     */
    public function update(Request $request, CashReceipt $cashReceipt)
    {
        if (!in_array($cashReceipt->status, ['draft', 'pending'])) {
            return redirect()->route('cash-receipts.show', $cashReceipt)
                ->with('error', 'لا يمكن تعديل سند معتمد أو مرحل!');
        }

        $validated = $request->validate([
            'receipt_date' => 'required|date',
            'account_type' => 'required|in:cash_box,bank_account',
            'account_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:10',
            'received_from' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,check,transfer,card',
            'check_number' => 'nullable|string|max:100',
            'check_date' => 'nullable|date',
            'check_bank' => 'nullable|string|max:255',
            'transfer_reference' => 'nullable|string|max:255',
            'card_reference' => 'nullable|string|max:255',
            'description' => 'required|string',
            'notes' => 'nullable|string',
            'category' => 'required|in:sales,services,loan,investment,other',
            'reference_number' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Revert old balance
            $oldAccountModel = $cashReceipt->account_type === 'cash_box' ? CashBox::class : BankAccount::class;
            $oldAccount = $oldAccountModel::find($cashReceipt->account_id);
            $oldAccount->decrement('balance', $cashReceipt->amount);

            // Update receipt
            $cashReceipt->update($validated);

            // Update new balance
            $newAccountModel = $validated['account_type'] === 'cash_box' ? CashBox::class : BankAccount::class;
            $newAccount = $newAccountModel::find($validated['account_id']);
            $newAccount->increment('balance', $validated['amount']);

            DB::commit();

            return redirect()->route('cash-receipts.show', $cashReceipt)
                ->with('success', 'تم تحديث سند القبض بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث سند القبض: ' . $e->getMessage());
        }
    }

    /**
     * Approve cash receipt
     */
    public function approve(CashReceipt $cashReceipt)
    {
        if ($cashReceipt->status !== 'pending') {
            return back()->with('error', 'يمكن اعتماد السندات المعلقة فقط!');
        }

        $cashReceipt->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم اعتماد سند القبض بنجاح!');
    }

    /**
     * Post cash receipt
     */
    public function post(CashReceipt $cashReceipt)
    {
        if ($cashReceipt->status !== 'approved') {
            return back()->with('error', 'يجب اعتماد السند أولاً!');
        }

        // TODO: Create journal entry

        $cashReceipt->update([
            'status' => 'posted',
            'posted_by' => Auth::id(),
            'posted_at' => now(),
        ]);

        return back()->with('success', 'تم ترحيل سند القبض بنجاح!');
    }

    /**
     * Cancel cash receipt
     */
    public function cancel(CashReceipt $cashReceipt)
    {
        if ($cashReceipt->status === 'posted') {
            return back()->with('error', 'لا يمكن إلغاء سند مرحل!');
        }

        DB::beginTransaction();
        try {
            // Revert balance
            $accountModel = $cashReceipt->account_type === 'cash_box' ? CashBox::class : BankAccount::class;
            $account = $accountModel::find($cashReceipt->account_id);
            $account->decrement('balance', $cashReceipt->amount);

            $cashReceipt->update([
                'status' => 'cancelled',
                'cancelled_by' => Auth::id(),
                'cancelled_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'تم إلغاء سند القبض بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إلغاء السند: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified cash receipt
     */
    public function destroy(CashReceipt $cashReceipt)
    {
        if (!in_array($cashReceipt->status, ['draft', 'cancelled'])) {
            return back()->with('error', 'يمكن حذف المسودات والسندات الملغاة فقط!');
        }

        $cashReceipt->delete();

        return redirect()->route('cash-receipts.index')
            ->with('success', 'تم حذف سند القبض بنجاح!');
    }
}
