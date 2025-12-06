<?php

namespace App\Services;

use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * Purchase Invoice Service
 * منطق الأعمال لفواتير الموردين
 */
class PurchaseInvoiceService
{
    /**
     * توليد رقم فاتورة داخلي فريد
     * Generate unique internal invoice number
     *
     * @return string
     */
    public function generateInternalNumber(): string
    {
        $date = now()->format('Ymd');
        $lastInvoice = PurchaseInvoice::whereDate('created_at', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastInvoice ? (intval(substr($lastInvoice->internal_number, -4)) + 1) : 1;
        
        return 'PI-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * إنشاء فاتورة مورد جديدة
     * Create new purchase invoice
     *
     * @param array $data
     * @return PurchaseInvoice
     * @throws Exception
     */
    public function createInvoice(array $data): PurchaseInvoice
    {
        try {
            DB::beginTransaction();

            // Generate internal number if not provided
            if (!isset($data['internal_number'])) {
                $data['internal_number'] = $this->generateInternalNumber();
            }

            // Set created_by if not provided
            if (!isset($data['created_by'])) {
                $data['created_by'] = Auth::id();
            }

            // Set invoice_date if not provided
            if (!isset($data['invoice_date'])) {
                $data['invoice_date'] = now()->toDateString();
            }

            // Calculate due date if payment terms are credit
            if (!isset($data['due_date']) && isset($data['supplier_id'])) {
                $supplier = \App\Models\Supplier::find($data['supplier_id']);
                if ($supplier && $supplier->hasCreditTerms()) {
                    $data['due_date'] = now()->addDays($supplier->credit_days)->toDateString();
                }
            }

            // Create invoice
            $invoice = PurchaseInvoice::create($data);

            DB::commit();
            return $invoice;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('فشل إنشاء فاتورة المورد: ' . $e->getMessage());
        }
    }

    /**
     * إضافة صنف للفاتورة
     * Add item to invoice
     *
     * @param PurchaseInvoice $invoice
     * @param array $itemData
     * @return PurchaseInvoiceItem
     * @throws Exception
     */
    public function addItem(PurchaseInvoice $invoice, array $itemData): PurchaseInvoiceItem
    {
        try {
            DB::beginTransaction();

            // Calculate total amount for the item
            $subtotal = $itemData['quantity'] * $itemData['unit_price'];
            $discountAmount = $subtotal * ($itemData['discount_rate'] ?? 0) / 100;
            $amountAfterDiscount = $subtotal - $discountAmount;
            $taxAmount = $amountAfterDiscount * ($itemData['tax_rate'] ?? 0) / 100;
            $itemData['total_amount'] = $amountAfterDiscount + $taxAmount;

            // Add purchase_invoice_id
            $itemData['purchase_invoice_id'] = $invoice->id;

            // Create item
            $item = PurchaseInvoiceItem::create($itemData);

            // Recalculate invoice totals
            $this->calculateTotals($invoice);

            DB::commit();
            return $item;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('فشل إضافة الصنف: ' . $e->getMessage());
        }
    }

    /**
     * حساب إجماليات الفاتورة
     * Calculate invoice totals
     *
     * @param PurchaseInvoice $invoice
     * @return void
     */
    public function calculateTotals(PurchaseInvoice $invoice): void
    {
        $items = $invoice->items;

        $subtotal = 0;
        $taxAmount = 0;
        $discountAmount = 0;

        foreach ($items as $item) {
            $itemSubtotal = $item->quantity * $item->unit_price;
            $itemDiscount = $itemSubtotal * ($item->discount_rate / 100);
            $itemAfterDiscount = $itemSubtotal - $itemDiscount;
            $itemTax = $itemAfterDiscount * ($item->tax_rate / 100);

            $subtotal += $itemSubtotal;
            $discountAmount += $itemDiscount;
            $taxAmount += $itemTax;
        }

        $invoice->subtotal = $subtotal;
        $invoice->discount_amount = $discountAmount;
        $invoice->tax_amount = $taxAmount;
        $invoice->total_amount = $subtotal - $discountAmount + $taxAmount;
        $invoice->remaining_amount = $invoice->total_amount - $invoice->paid_amount;
        $invoice->save();
    }

    /**
     * اعتماد الفاتورة
     * Approve invoice
     *
     * @param PurchaseInvoice $invoice
     * @return bool
     * @throws Exception
     */
    public function approveInvoice(PurchaseInvoice $invoice): bool
    {
        try {
            DB::beginTransaction();

            if ($invoice->isApproved()) {
                throw new Exception('الفاتورة معتمدة بالفعل');
            }

            // Update invoice status
            $invoice->approved_by = Auth::id();
            $invoice->approved_at = now();
            $invoice->status = 'approved';
            $invoice->save();

            // Create journal entry (التكامل المحاسبي)
            $this->createJournalEntry($invoice);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('فشل اعتماد الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * التكامل المحاسبي: إنشاء قيد محاسبي تلقائي
     * Accounting Integration: Create journal entry
     *
     * @param PurchaseInvoice $invoice
     * @return void
     * @throws Exception
     */
    public function createJournalEntry(PurchaseInvoice $invoice): void
    {
        try {
            // Generate journal entry number
            $date = now()->format('Ymd');
            $entryNumber = 'JE-PUR-' . $date . '-' . str_pad($invoice->id, 4, '0', STR_PAD_LEFT);

            // Create journal entry
            $journalEntry = JournalEntry::create([
                'entry_number' => $entryNumber,
                'entry_date' => $invoice->invoice_date,
                'description' => "فاتورة مشتريات رقم {$invoice->invoice_number} - المورد: {$invoice->supplier->name}",
                'reference_type' => 'purchase_invoice',
                'reference_id' => $invoice->id,
                'status' => 'approved',
                'created_by' => $invoice->approved_by ?? $invoice->created_by,
            ]);

            // Get accounts from settings or use default accounts
            $purchasesAccountId = config('accounting.accounts.purchases', 1); // حساب المشتريات
            $taxInputAccountId = config('accounting.accounts.tax_input', 2); // حساب ضريبة المدخلات
            $supplierAccountId = $invoice->supplier->account_id; // حساب المورد

            // Debit: Purchases account (مدين: المشتريات)
            $amountAfterDiscount = $invoice->subtotal - $invoice->discount_amount;
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $purchasesAccountId,
                'debit' => $amountAfterDiscount,
                'credit' => 0,
                'description' => 'مشتريات',
            ]);

            // Debit: Tax input account (مدين: ضريبة المدخلات)
            if ($invoice->tax_amount > 0) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $taxInputAccountId,
                    'debit' => $invoice->tax_amount,
                    'credit' => 0,
                    'description' => 'ضريبة المدخلات',
                ]);
            }

            // Credit: Supplier account (دائن: حساب المورد)
            if ($supplierAccountId) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $supplierAccountId,
                    'debit' => 0,
                    'credit' => $invoice->total_amount,
                    'description' => 'حساب المورد',
                ]);
            }

            // Link journal entry to invoice
            $invoice->journal_entry_id = $journalEntry->id;
            $invoice->save();
        } catch (Exception $e) {
            throw new Exception('فشل إنشاء القيد المحاسبي: ' . $e->getMessage());
        }
    }

    /**
     * تسجيل دفعة للفاتورة
     * Record payment for invoice
     *
     * @param PurchaseInvoice $invoice
     * @param array $paymentData
     * @return void
     * @throws Exception
     */
    public function recordPayment(PurchaseInvoice $invoice, array $paymentData): void
    {
        try {
            DB::beginTransaction();

            if (!$invoice->isApproved()) {
                throw new Exception('لا يمكن تسجيل دفعة لفاتورة غير معتمدة');
            }

            // Create payment record
            $payment = Payment::create([
                'purchase_invoice_id' => $invoice->id,
                'payment_date' => $paymentData['payment_date'] ?? now()->toDateString(),
                'amount' => $paymentData['amount'],
                'payment_method' => $paymentData['payment_method'] ?? 'cash',
                'reference_number' => $paymentData['reference_number'] ?? null,
                'notes' => $paymentData['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Update invoice paid amount
            $invoice->paid_amount += $paymentData['amount'];
            $invoice->remaining_amount = $invoice->total_amount - $invoice->paid_amount;
            $invoice->save();

            // Update payment status
            $invoice->updatePaymentStatus();

            // Create payment journal entry
            $this->createPaymentJournalEntry($invoice, $payment);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('فشل تسجيل الدفعة: ' . $e->getMessage());
        }
    }

    /**
     * إنشاء قيد محاسبي للدفعة
     * Create journal entry for payment
     *
     * @param PurchaseInvoice $invoice
     * @param Payment $payment
     * @return void
     * @throws Exception
     */
    protected function createPaymentJournalEntry(PurchaseInvoice $invoice, Payment $payment): void
    {
        try {
            // Generate journal entry number
            $date = now()->format('Ymd');
            $entryNumber = 'JE-PAY-' . $date . '-' . str_pad($payment->id, 4, '0', STR_PAD_LEFT);

            // Create journal entry
            $journalEntry = JournalEntry::create([
                'entry_number' => $entryNumber,
                'entry_date' => $payment->payment_date,
                'description' => "دفعة لفاتورة مشتريات رقم {$invoice->invoice_number}",
                'reference_type' => 'payment',
                'reference_id' => $payment->id,
                'status' => 'approved',
                'created_by' => $payment->created_by,
            ]);

            // Get accounts
            $supplierAccountId = $invoice->supplier->account_id;
            $cashAccountId = config('accounting.accounts.cash', 3); // حساب الصندوق
            $bankAccountId = config('accounting.accounts.bank', 4); // حساب البنك

            // Debit: Supplier account (مدين: حساب المورد)
            if ($supplierAccountId) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $supplierAccountId,
                    'debit' => $payment->amount,
                    'credit' => 0,
                    'description' => 'سداد للمورد',
                ]);
            }

            // Credit: Cash/Bank account (دائن: الصندوق/البنك)
            $paymentAccountId = $payment->payment_method === 'bank' ? $bankAccountId : $cashAccountId;
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $paymentAccountId,
                'debit' => 0,
                'credit' => $payment->amount,
                'description' => $payment->payment_method === 'bank' ? 'البنك' : 'الصندوق',
            ]);
        } catch (Exception $e) {
            throw new Exception('فشل إنشاء قيد الدفعة: ' . $e->getMessage());
        }
    }

    /**
     * حساب المبلغ المتبقي
     * Calculate remaining amount
     *
     * @param PurchaseInvoice $invoice
     * @return void
     */
    public function calculateRemainingAmount(PurchaseInvoice $invoice): void
    {
        $invoice->remaining_amount = $invoice->total_amount - $invoice->paid_amount;
        $invoice->save();
        $invoice->updatePaymentStatus();
    }
}
