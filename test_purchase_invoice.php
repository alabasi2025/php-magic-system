<?php

/**
 * Purchase Invoice Testing Script
 * سكريبت اختبار فاتورة المشتريات
 * 
 * يقوم هذا السكريبت باختبار شامل لنظام فاتورة المشتريات
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\PurchaseInvoiceType;
use App\Services\PurchaseInvoiceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

echo "\n";
echo "========================================\n";
echo "   اختبار نظام فاتورة المشتريات\n";
echo "========================================\n\n";

// تسجيل النتائج
$results = [
    'passed' => 0,
    'failed' => 0,
    'errors' => []
];

function testResult($testName, $passed, $message = '') {
    global $results;
    if ($passed) {
        $results['passed']++;
        echo "✓ {$testName}: نجح\n";
    } else {
        $results['failed']++;
        $results['errors'][] = "{$testName}: {$message}";
        echo "✗ {$testName}: فشل - {$message}\n";
    }
}

try {
    // الاختبار 1: التحقق من وجود الجداول المطلوبة
    echo "\n--- الاختبار 1: فحص الجداول ---\n";
    
    $tables = ['purchase_invoices', 'purchase_invoice_items', 'suppliers', 'items', 'warehouses'];
    foreach ($tables as $table) {
        $exists = DB::getSchemaBuilder()->hasTable($table);
        testResult("جدول {$table}", $exists, "الجدول غير موجود");
    }
    
    // الاختبار 2: التحقق من وجود بيانات أساسية
    echo "\n--- الاختبار 2: فحص البيانات الأساسية ---\n";
    
    $userCount = User::count();
    testResult("وجود مستخدمين", $userCount > 0, "لا يوجد مستخدمين في النظام");
    
    $supplierCount = Supplier::count();
    testResult("وجود موردين", $supplierCount > 0, "لا يوجد موردين في النظام");
    
    $itemCount = Item::count();
    testResult("وجود أصناف", $itemCount > 0, "لا يوجد أصناف في النظام");
    
    $warehouseCount = Warehouse::count();
    testResult("وجود مخازن", $warehouseCount > 0, "لا يوجد مخازن في النظام");
    
    // إذا لم تكن هناك بيانات أساسية، نتوقف
    if ($userCount == 0 || $supplierCount == 0 || $itemCount == 0 || $warehouseCount == 0) {
        echo "\n⚠ تحذير: لا يمكن إكمال الاختبارات بدون بيانات أساسية\n";
        echo "يرجى تشغيل seeders أولاً\n\n";
        printSummary();
        exit(1);
    }
    
    // الحصول على بيانات للاختبار
    $user = User::first();
    $supplier = Supplier::where('status', 'active')->first() ?? Supplier::first();
    $items = Item::where('status', 'active')->limit(3)->get();
    if ($items->count() == 0) {
        $items = Item::limit(3)->get();
    }
    $warehouse = Warehouse::where('status', 'active')->first() ?? Warehouse::first();
    $invoiceType = PurchaseInvoiceType::first();
    
    // تسجيل دخول المستخدم
    Auth::loginUsingId($user->id);
    
    // الاختبار 3: إنشاء فاتورة مشتريات جديدة
    echo "\n--- الاختبار 3: إنشاء فاتورة مشتريات ---\n";
    
    DB::beginTransaction();
    
    try {
        $service = new PurchaseInvoiceService();
        
        // إنشاء الفاتورة
        $invoiceData = [
            'invoice_type_id' => $invoiceType ? $invoiceType->id : null,
            'invoice_number' => 'TEST-INV-' . time(),
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'invoice_date' => now()->toDateString(),
            'payment_method' => 'cash',
            'status' => 'draft',
            'notes' => 'فاتورة اختبار'
        ];
        
        $invoice = $service->createInvoice($invoiceData);
        testResult("إنشاء الفاتورة", $invoice && $invoice->id > 0, "فشل إنشاء الفاتورة");
        testResult("توليد الرقم الداخلي", !empty($invoice->internal_number), "لم يتم توليد رقم داخلي");
        
        // الاختبار 4: إضافة أصناف للفاتورة
        echo "\n--- الاختبار 4: إضافة أصناف للفاتورة ---\n";
        
        $totalItems = 0;
        foreach ($items as $item) {
            $itemData = [
                'item_id' => $item->id,
                'quantity' => 10,
                'unit_price' => 100.00,
                'tax_rate' => 15.00,
                'discount_rate' => 5.00
            ];
            
            $invoiceItem = $service->addItem($invoice, $itemData);
            if ($invoiceItem && $invoiceItem->id > 0) {
                $totalItems++;
            }
        }
        
        testResult("إضافة أصناف", $totalItems == $items->count(), "تمت إضافة {$totalItems} من {$items->count()}");
        
        // الاختبار 5: التحقق من الحسابات
        echo "\n--- الاختبار 5: فحص الحسابات والإجماليات ---\n";
        
        $invoice->refresh();
        
        testResult("حساب المجموع الفرعي", $invoice->subtotal > 0, "المجموع الفرعي = {$invoice->subtotal}");
        testResult("حساب الخصم", $invoice->discount_amount >= 0, "الخصم = {$invoice->discount_amount}");
        testResult("حساب الضريبة", $invoice->tax_amount > 0, "الضريبة = {$invoice->tax_amount}");
        testResult("حساب المجموع الكلي", $invoice->total_amount > 0, "المجموع الكلي = {$invoice->total_amount}");
        
        // التحقق من صحة الحسابات يدوياً
        $expectedSubtotal = 0;
        $expectedDiscount = 0;
        $expectedTax = 0;
        
        foreach ($invoice->items as $item) {
            $itemSubtotal = $item->quantity * $item->unit_price;
            $itemDiscount = $itemSubtotal * ($item->discount_rate / 100);
            $itemAfterDiscount = $itemSubtotal - $itemDiscount;
            $itemTax = $itemAfterDiscount * ($item->tax_rate / 100);
            
            $expectedSubtotal += $itemSubtotal;
            $expectedDiscount += $itemDiscount;
            $expectedTax += $itemTax;
        }
        
        $expectedTotal = $expectedSubtotal - $expectedDiscount + $expectedTax;
        
        $subtotalMatch = abs($invoice->subtotal - $expectedSubtotal) < 0.01;
        $discountMatch = abs($invoice->discount_amount - $expectedDiscount) < 0.01;
        $taxMatch = abs($invoice->tax_amount - $expectedTax) < 0.01;
        $totalMatch = abs($invoice->total_amount - $expectedTotal) < 0.01;
        
        testResult("دقة المجموع الفرعي", $subtotalMatch, 
            sprintf("متوقع: %.2f، فعلي: %.2f", $expectedSubtotal, $invoice->subtotal));
        testResult("دقة الخصم", $discountMatch, 
            sprintf("متوقع: %.2f، فعلي: %.2f", $expectedDiscount, $invoice->discount_amount));
        testResult("دقة الضريبة", $taxMatch, 
            sprintf("متوقع: %.2f، فعلي: %.2f", $expectedTax, $invoice->tax_amount));
        testResult("دقة المجموع الكلي", $totalMatch, 
            sprintf("متوقع: %.2f، فعلي: %.2f", $expectedTotal, $invoice->total_amount));
        
        // الاختبار 6: اختبار حالة الدفع
        echo "\n--- الاختبار 6: فحص حالة الدفع ---\n";
        
        testResult("حالة الدفع الافتراضية", $invoice->payment_status == 'unpaid', 
            "حالة الدفع: {$invoice->payment_status}");
        testResult("المبلغ المدفوع", $invoice->paid_amount == 0, 
            "المبلغ المدفوع: {$invoice->paid_amount}");
        testResult("المبلغ المتبقي", $invoice->remaining_amount == $invoice->total_amount, 
            "المتبقي: {$invoice->remaining_amount}، الكلي: {$invoice->total_amount}");
        
        // الاختبار 7: اختبار الاعتماد
        echo "\n--- الاختبار 7: اختبار عملية الاعتماد ---\n";
        
        $wasApproved = $invoice->isApproved();
        testResult("الحالة قبل الاعتماد", !$wasApproved && $invoice->status == 'draft', 
            "الحالة: {$invoice->status}");
        
        try {
            $approved = $service->approveInvoice($invoice);
            $invoice->refresh();
            
            testResult("عملية الاعتماد", $approved, "فشل الاعتماد");
            testResult("تحديث الحالة", $invoice->status == 'approved', 
                "الحالة بعد الاعتماد: {$invoice->status}");
            testResult("تسجيل المعتمد", $invoice->approved_by == $user->id, 
                "المعتمد: {$invoice->approved_by}");
            testResult("تسجيل تاريخ الاعتماد", !is_null($invoice->approved_at), 
                "تاريخ الاعتماد: {$invoice->approved_at}");
            
            // التحقق من القيد المحاسبي
            $hasJournalEntry = !is_null($invoice->journal_entry_id);
            testResult("إنشاء القيد المحاسبي", $hasJournalEntry, 
                "لم يتم إنشاء قيد محاسبي");
            
        } catch (Exception $e) {
            testResult("عملية الاعتماد", false, $e->getMessage());
        }
        
        // الاختبار 8: اختبار منع التعديل بعد الاعتماد
        echo "\n--- الاختبار 8: اختبار منع التعديل بعد الاعتماد ---\n";
        
        try {
            $invoice->subtotal = 9999;
            $invoice->save();
            testResult("منع التعديل بعد الاعتماد", false, "تم السماح بالتعديل!");
        } catch (Exception $e) {
            // من المفترض أن يفشل
            testResult("منع التعديل بعد الاعتماد", true);
        }
        
        // الاختبار 9: اختبار العلاقات
        echo "\n--- الاختبار 9: فحص العلاقات ---\n";
        
        $invoice->refresh();
        
        testResult("علاقة المورد", $invoice->supplier && $invoice->supplier->id == $supplier->id);
        testResult("علاقة المخزن", $invoice->warehouse && $invoice->warehouse->id == $warehouse->id);
        testResult("علاقة الأصناف", $invoice->items->count() == $totalItems);
        testResult("علاقة المستخدم المنشئ", $invoice->creator && $invoice->creator->id == $user->id);
        testResult("علاقة المستخدم المعتمد", $invoice->approver && $invoice->approver->id == $user->id);
        
        DB::rollBack();
        echo "\n✓ تم التراجع عن جميع التغييرات (Rollback)\n";
        
    } catch (Exception $e) {
        DB::rollBack();
        testResult("الاختبار الشامل", false, $e->getMessage());
        echo "\nتفاصيل الخطأ:\n";
        echo $e->getTraceAsString();
    }
    
} catch (Exception $e) {
    echo "\n✗ خطأ فادح: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    $results['failed']++;
}

function printSummary() {
    global $results;
    
    echo "\n";
    echo "========================================\n";
    echo "           ملخص النتائج\n";
    echo "========================================\n";
    echo "الاختبارات الناجحة: {$results['passed']}\n";
    echo "الاختبارات الفاشلة: {$results['failed']}\n";
    echo "المجموع: " . ($results['passed'] + $results['failed']) . "\n";
    
    if ($results['failed'] > 0) {
        echo "\n--- الأخطاء المكتشفة ---\n";
        foreach ($results['errors'] as $error) {
            echo "• {$error}\n";
        }
    }
    
    $successRate = $results['passed'] + $results['failed'] > 0 
        ? round(($results['passed'] / ($results['passed'] + $results['failed'])) * 100, 2)
        : 0;
    
    echo "\nنسبة النجاح: {$successRate}%\n";
    echo "========================================\n\n";
}

printSummary();

// رمز الخروج
exit($results['failed'] > 0 ? 1 : 0);
