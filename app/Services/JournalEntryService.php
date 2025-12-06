<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\CashReceipt;
use App\Models\CashPayment;
use App\Models\IntermediateAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class JournalEntryService
{
    /**
     * Create journal entry from cash receipt
     *
     * @param CashReceipt $cashReceipt
     * @return JournalEntry
     */
    public function createFromCashReceipt(CashReceipt $cashReceipt): JournalEntry
    {
        return DB::transaction(function () use ($cashReceipt) {
            // Get account intermediate account
            $account = $cashReceipt->account;
            $intermediateAccount = $account->intermediateAccount ?? $this->getDefaultIntermediateAccount();

            // Create journal entry
            $journalEntry = JournalEntry::create([
                'entry_number' => $this->generateEntryNumber(),
                'entry_date' => $cashReceipt->receipt_date,
                'description' => "سند قبض رقم: {$cashReceipt->receipt_number} - {$cashReceipt->received_from}",
                'reference' => "CR-{$cashReceipt->receipt_number}",
                'unit_id' => $cashReceipt->unit_id,
                'user_id' => Auth::id(),
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            // Debit: Cash Box or Bank Account (المدين)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'intermediate_account_id' => $intermediateAccount->id,
                'description' => "استلام من: {$cashReceipt->received_from}",
                'debit' => $cashReceipt->amount,
                'credit' => 0,
            ]);

            // Credit: Revenue or Customer Account (الدائن)
            // يمكن تحديد الحساب بناءً على التصنيف أو من قالب ذكي
            $creditAccount = $this->getCreditAccountForReceipt($cashReceipt);
            
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'intermediate_account_id' => $creditAccount->id,
                'description' => $cashReceipt->description,
                'debit' => 0,
                'credit' => $cashReceipt->amount,
            ]);

            // Update totals
            $journalEntry->update([
                'total_debit' => $cashReceipt->amount,
                'total_credit' => $cashReceipt->amount,
                'is_balanced' => true,
            ]);

            // Link journal entry to cash receipt
            $cashReceipt->update([
                'journal_entry_id' => $journalEntry->id,
            ]);

            return $journalEntry;
        });
    }

    /**
     * Create journal entry from cash payment
     *
     * @param CashPayment $cashPayment
     * @return JournalEntry
     */
    public function createFromCashPayment(CashPayment $cashPayment): JournalEntry
    {
        return DB::transaction(function () use ($cashPayment) {
            // Get account intermediate account
            $account = $cashPayment->account;
            $intermediateAccount = $account->intermediateAccount ?? $this->getDefaultIntermediateAccount();

            // Create journal entry
            $journalEntry = JournalEntry::create([
                'entry_number' => $this->generateEntryNumber(),
                'entry_date' => $cashPayment->payment_date,
                'description' => "سند صرف رقم: {$cashPayment->payment_number} - {$cashPayment->paid_to}",
                'reference' => "CP-{$cashPayment->payment_number}",
                'unit_id' => $cashPayment->unit_id,
                'user_id' => Auth::id(),
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            // Debit: Expense or Supplier Account (المدين)
            $debitAccount = $this->getDebitAccountForPayment($cashPayment);
            
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'intermediate_account_id' => $debitAccount->id,
                'description' => $cashPayment->description,
                'debit' => $cashPayment->amount,
                'credit' => 0,
            ]);

            // Credit: Cash Box or Bank Account (الدائن)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'intermediate_account_id' => $intermediateAccount->id,
                'description' => "دفع إلى: {$cashPayment->paid_to}",
                'debit' => 0,
                'credit' => $cashPayment->amount,
            ]);

            // Update totals
            $journalEntry->update([
                'total_debit' => $cashPayment->amount,
                'total_credit' => $cashPayment->amount,
                'is_balanced' => true,
            ]);

            // Link journal entry to cash payment
            $cashPayment->update([
                'journal_entry_id' => $journalEntry->id,
            ]);

            return $journalEntry;
        });
    }

    /**
     * Generate unique entry number
     *
     * @return string
     */
    protected function generateEntryNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        
        $lastEntry = JournalEntry::whereYear('entry_date', $year)
            ->whereMonth('entry_date', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEntry && preg_match('/JE-(\d{4})(\d{2})-(\d{4})/', $lastEntry->entry_number, $matches)) {
            $sequence = intval($matches[3]) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('JE-%s%s-%04d', $year, $month, $sequence);
    }

    /**
     * Get credit account for cash receipt
     *
     * @param CashReceipt $cashReceipt
     * @return IntermediateAccount
     */
    protected function getCreditAccountForReceipt(CashReceipt $cashReceipt): IntermediateAccount
    {
        // يمكن تحسين هذا لاحقاً باستخدام القوالب الذكية
        // حالياً نستخدم حساب افتراضي للإيرادات
        
        $account = IntermediateAccount::where('account_code', 'LIKE', '4%') // حسابات الإيرادات
            ->where('is_active', true)
            ->first();

        if (!$account) {
            // إنشاء حساب افتراضي إذا لم يكن موجوداً
            $account = IntermediateAccount::create([
                'account_code' => '4001',
                'account_name' => 'إيرادات متنوعة',
                'account_type' => 'revenue',
                'is_active' => true,
                'created_by' => Auth::id(),
            ]);
        }

        return $account;
    }

    /**
     * Get debit account for cash payment
     *
     * @param CashPayment $cashPayment
     * @return IntermediateAccount
     */
    protected function getDebitAccountForPayment(CashPayment $cashPayment): IntermediateAccount
    {
        // يمكن تحسين هذا لاحقاً باستخدام القوالب الذكية
        // حالياً نستخدم حساب افتراضي للمصروفات
        
        $account = IntermediateAccount::where('account_code', 'LIKE', '5%') // حسابات المصروفات
            ->where('is_active', true)
            ->first();

        if (!$account) {
            // إنشاء حساب افتراضي إذا لم يكن موجوداً
            $account = IntermediateAccount::create([
                'account_code' => '5001',
                'account_name' => 'مصروفات متنوعة',
                'account_type' => 'expense',
                'is_active' => true,
                'created_by' => Auth::id(),
            ]);
        }

        return $account;
    }

    /**
     * Get default intermediate account
     *
     * @return IntermediateAccount
     */
    protected function getDefaultIntermediateAccount(): IntermediateAccount
    {
        $account = IntermediateAccount::where('account_code', '1001') // النقدية
            ->first();

        if (!$account) {
            $account = IntermediateAccount::create([
                'account_code' => '1001',
                'account_name' => 'النقدية',
                'account_type' => 'asset',
                'is_active' => true,
                'created_by' => Auth::id(),
            ]);
        }

        return $account;
    }

    /**
     * Approve journal entry
     *
     * @param JournalEntry $journalEntry
     * @return void
     */
    public function approveJournalEntry(JournalEntry $journalEntry): void
    {
        $journalEntry->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
    }

    /**
     * Post journal entry (ترحيل)
     *
     * @param JournalEntry $journalEntry
     * @return void
     */
    public function postJournalEntry(JournalEntry $journalEntry): void
    {
        DB::transaction(function () use ($journalEntry) {
            // Update journal entry status
            $journalEntry->update([
                'status' => 'posted',
            ]);

            // Update account balances
            foreach ($journalEntry->details as $detail) {
                $this->updateAccountBalance($detail);
            }
        });
    }

    /**
     * Update account balance
     *
     * @param JournalEntryDetail $detail
     * @return void
     */
    protected function updateAccountBalance(JournalEntryDetail $detail): void
    {
        $account = $detail->intermediateAccount;
        
        if (!$account) {
            return;
        }

        // Update balance based on account type
        $debitBalance = $detail->debit - $detail->credit;
        
        // For asset and expense accounts, debit increases balance
        // For liability, equity, and revenue accounts, credit increases balance
        if (in_array($account->account_type, ['asset', 'expense'])) {
            $account->increment('balance', $debitBalance);
        } else {
            $account->decrement('balance', $debitBalance);
        }
    }

    /**
     * Cancel journal entry
     *
     * @param JournalEntry $journalEntry
     * @return void
     */
    public function cancelJournalEntry(JournalEntry $journalEntry): void
    {
        DB::transaction(function () use ($journalEntry) {
            // If posted, reverse the account balances
            if ($journalEntry->status === 'posted') {
                foreach ($journalEntry->details as $detail) {
                    $this->reverseAccountBalance($detail);
                }
            }

            // Update status
            $journalEntry->update([
                'status' => 'cancelled',
            ]);
        });
    }

    /**
     * Reverse account balance
     *
     * @param JournalEntryDetail $detail
     * @return void
     */
    protected function reverseAccountBalance(JournalEntryDetail $detail): void
    {
        $account = $detail->intermediateAccount;
        
        if (!$account) {
            return;
        }

        // Reverse the balance update
        $debitBalance = $detail->debit - $detail->credit;
        
        if (in_array($account->account_type, ['asset', 'expense'])) {
            $account->decrement('balance', $debitBalance);
        } else {
            $account->increment('balance', $debitBalance);
        }
    }
}
