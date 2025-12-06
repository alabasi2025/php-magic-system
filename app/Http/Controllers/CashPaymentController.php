<?php

namespace App\Http\Controllers;

use App\Models\CashPayment;
use App\Models\CashBox;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashPaymentController extends Controller
{
    /**
     * Display a listing of cash payments
     */
    public function index()
    {
        $payments = CashPayment::with(['account', 'creator'])
            ->orderBy('payment_date', 'desc')
            ->paginate(20);

        // Statistics
        $stats = [
            'total' => CashPayment::count(),
            'pending' => CashPayment::where('status', 'pending')->count(),
            'approved' => CashPayment::where('status', 'approved')->count(),
            'posted' => CashPayment::where('status', 'posted')->count(),
            'total_amount' => CashPayment::whereIn('status', ['approved', 'posted'])->sum('amount'),
        ];

        return view('cash-payments.index', compact('payments', 'stats'));
    }

    /**
     * Show the form for creating a new cash payment
     */
    public function create()
    {
        $cashBoxes = CashBox::where('is_active', true)->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $paymentNumber = CashPayment::generatePaymentNumber();

        return view('cash-payments.create', compact('cashBoxes', 'bankAccounts', 'paymentNumber'));
    }

    /**
     * Store a newly created cash payment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'account_type' => 'required|in:cash_box,bank_account',
            'account_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:10',
            'paid_to' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,check,transfer,card',
            'check_number' => 'nullable|string|max:100',
            'check_date' => 'nullable|date',
            'check_bank' => 'nullable|string|max:255',
            'transfer_reference' => 'nullable|string|max:255',
            'card_reference' => 'nullable|string|max:255',
            'description' => 'required|string',
            'notes' => 'nullable|string',
            'category' => 'required|in:purchases,expenses,salaries,loan,investment,other',
            'reference_number' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $validated['payment_number'] = CashPayment::generatePaymentNumber();
            $validated['status'] = 'draft';
            $validated['created_by'] = Auth::id();
            $validated['exchange_rate'] = 1; // Default
            $validated['amount_in_base_currency'] = $validated['amount'];

            $payment = CashPayment::create($validated);

            // Update account balance
            $accountModel = $validated['account_type'] === 'cash_box' ? CashBox::class : BankAccount::class;
            $account = $accountModel::find($validated['account_id']);
            
            // Check if sufficient balance
            if ($account->balance < $validated['amount']) {
                throw new \Exception('الرصيد غير كافٍ في الحساب المحدد!');
            }
            
            $account->decrement('balance', $validated['amount']);

            DB::commit();

            return redirect()->route('cash-payments.show', $payment)
                ->with('success', 'تم إنشاء سند الصرف بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء سند الصرف: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified cash payment
     */
    public function show(CashPayment $cashPayment)
    {
        $cashPayment->load(['account', 'creator', 'approver', 'poster']);

        return view('cash-payments.show', compact('cashPayment'));
    }

    /**
     * Show the form for editing the specified cash payment
     */
    public function edit(CashPayment $cashPayment)
    {
        if (!in_array($cashPayment->status, ['draft', 'pending'])) {
            return redirect()->route('cash-payments.show', $cashPayment)
                ->with('error', 'لا يمكن تعديل سند معتمد أو مرحل!');
        }

        $cashBoxes = CashBox::where('is_active', true)->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();

        return view('cash-payments.edit', compact('cashPayment', 'cashBoxes', 'bankAccounts'));
    }

    /**
     * Update the specified cash payment
     */
    public function update(Request $request, CashPayment $cashPayment)
    {
        if (!in_array($cashPayment->status, ['draft', 'pending'])) {
            return redirect()->route('cash-payments.show', $cashPayment)
                ->with('error', 'لا يمكن تعديل سند معتمد أو مرحل!');
        }

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'account_type' => 'required|in:cash_box,bank_account',
            'account_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:10',
            'paid_to' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,check,transfer,card',
            'check_number' => 'nullable|string|max:100',
            'check_date' => 'nullable|date',
            'check_bank' => 'nullable|string|max:255',
            'transfer_reference' => 'nullable|string|max:255',
            'card_reference' => 'nullable|string|max:255',
            'description' => 'required|string',
            'notes' => 'nullable|string',
            'category' => 'required|in:purchases,expenses,salaries,loan,investment,other',
            'reference_number' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Revert old balance
            $oldAccountModel = $cashPayment->account_type === 'cash_box' ? CashBox::class : BankAccount::class;
            $oldAccount = $oldAccountModel::find($cashPayment->account_id);
            $oldAccount->increment('balance', $cashPayment->amount);

            // Update payment
            $cashPayment->update($validated);

            // Update new balance
            $newAccountModel = $validated['account_type'] === 'cash_box' ? CashBox::class : BankAccount::class;
            $newAccount = $newAccountModel::find($validated['account_id']);
            
            // Check if sufficient balance
            if ($newAccount->balance < $validated['amount']) {
                throw new \Exception('الرصيد غير كافٍ في الحساب المحدد!');
            }
            
            $newAccount->decrement('balance', $validated['amount']);

            DB::commit();

            return redirect()->route('cash-payments.show', $cashPayment)
                ->with('success', 'تم تحديث سند الصرف بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث سند الصرف: ' . $e->getMessage());
        }
    }

    /**
     * Approve cash payment
     */
    public function approve(CashPayment $cashPayment)
    {
        if ($cashPayment->status !== 'pending') {
            return back()->with('error', 'يمكن اعتماد السندات المعلقة فقط!');
        }

        $cashPayment->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم اعتماد سند الصرف بنجاح!');
    }

    /**
     * Post cash payment
     */
    public function post(CashPayment $cashPayment)
    {
        if ($cashPayment->status !== 'approved') {
            return back()->with('error', 'يجب اعتماد السند أولاً!');
        }

        // TODO: Create journal entry

        $cashPayment->update([
            'status' => 'posted',
            'posted_by' => Auth::id(),
            'posted_at' => now(),
        ]);

        return back()->with('success', 'تم ترحيل سند الصرف بنجاح!');
    }

    /**
     * Cancel cash payment
     */
    public function cancel(CashPayment $cashPayment)
    {
        if ($cashPayment->status === 'posted') {
            return back()->with('error', 'لا يمكن إلغاء سند مرحل!');
        }

        DB::beginTransaction();
        try {
            // Revert balance
            $accountModel = $cashPayment->account_type === 'cash_box' ? CashBox::class : BankAccount::class;
            $account = $accountModel::find($cashPayment->account_id);
            $account->increment('balance', $cashPayment->amount);

            $cashPayment->update([
                'status' => 'cancelled',
                'cancelled_by' => Auth::id(),
                'cancelled_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'تم إلغاء سند الصرف بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إلغاء السند: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified cash payment
     */
    public function destroy(CashPayment $cashPayment)
    {
        if (!in_array($cashPayment->status, ['draft', 'cancelled'])) {
            return back()->with('error', 'يمكن حذف المسودات والسندات الملغاة فقط!');
        }

        $cashPayment->delete();

        return redirect()->route('cash-payments.index')
            ->with('success', 'تم حذف سند الصرف بنجاح!');
    }
}
