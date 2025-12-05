<?php

namespace App\Services;

use App\Models\StockOut;
use App\Models\StockOutDetail;
use App\Models\Stock; // افتراض وجود نموذج لإدارة المخزون
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StockOutService
{
    /**
     * إنشاء إذن إخراج جديد والقيام بخصم الكميات من المخزون.
     *
     * @param array $data بيانات إذن الإخراج والتفاصيل
     * @return StockOut
     * @throws ValidationException
     */
    public function createStockOut(array $data): StockOut
    {
        // 1. التحقق من الصحة (Validation) - سيتم تنفيذه بشكل أولي في المتحكم، ولكن يمكن تكراره هنا لمنطق الأعمال المعقد
        // 2. التحقق من توفر الكميات
        $this->checkAvailability($data['details']);

        // بدء عملية قاعدة البيانات لضمان التناسق
        return DB::transaction(function () use ($data) {
            // إعداد بيانات إذن الإخراج الرئيسية
            $stockOutData = [
                'number' => $this->generateStockOutNumber(), // توليد رقم فريد
                'warehouse_id' => $data['warehouse_id'],
                'customer_id' => $data['customer_id'],
                'date' => $data['date'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'total_amount' => $this->calculateTotalAmount($data['details']),
                'status' => 'completed', // يتم اعتباره مكتمل بمجرد الخصم من المخزون
                'created_by' => Auth::id(),
            ];

            // إنشاء إذن الإخراج
            $stockOut = StockOut::create($stockOutData);

            // إنشاء تفاصيل إذن الإخراج وخصم الكميات
            foreach ($data['details'] as $detail) {
                $stockOut->details()->create([
                    'item_id' => $detail['item_id'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                    'total_price' => $detail['quantity'] * $detail['unit_price'],
                ]);

                // خصم الكمية من المخزون
                $this->deductStock($detail['item_id'], $data['warehouse_id'], $detail['quantity']);
            }

            return $stockOut;
        });
    }

    /**
     * تحديث إذن إخراج موجود.
     *
     * ملاحظة: تحديث إذن إخراج يؤثر على المخزون وهو عملية معقدة.
     * يتطلب عكس الخصم القديم وتطبيق الخصم الجديد.
     *
     * @param StockOut $stockOut
     * @param array $data
     * @return StockOut
     * @throws ValidationException
     */
    public function updateStockOut(StockOut $stockOut, array $data): StockOut
    {
        // في نظام حقيقي، يجب أن تكون هذه العملية مقيدة جداً أو غير مسموح بها بعد اكتمال الإخراج.
        // لأغراض هذا المثال، سنفترض أننا نلغي الإخراج القديم وننشئ واحداً جديداً منطقياً.
        throw new \Exception('تحديث إذن الإخراج غير مدعوم مباشرة بعد الخصم من المخزون. يرجى إنشاء إذن جديد أو إلغاء الحالي.');
    }

    /**
     * إلغاء إذن إخراج وإعادة الكميات إلى المخزون.
     *
     * @param StockOut $stockOut
     * @return bool
     */
    public function cancelStockOut(StockOut $stockOut): bool
    {
        if ($stockOut->status === 'canceled') {
            return true; // تم الإلغاء مسبقاً
        }

        return DB::transaction(function () use ($stockOut) {
            // 1. إعادة الكميات إلى المخزون
            foreach ($stockOut->details as $detail) {
                $this->returnStock($detail->item_id, $stockOut->warehouse_id, $detail->quantity);
            }

            // 2. تحديث حالة الإذن
            $stockOut->status = 'canceled';
            $stockOut->save();

            return true;
        });
    }

    /**
     * التحقق من توفر الكميات المطلوبة للإخراج.
     *
     * @param array $details تفاصيل الإخراج (item_id, quantity)
     * @throws ValidationException
     */
    protected function checkAvailability(array $details): void
    {
        foreach ($details as $detail) {
            // افتراض أن لدينا دالة تجلب الكمية المتوفرة
            $availableQuantity = $this->getAvailableStock($detail['item_id'], $detail['warehouse_id']);

            if ($availableQuantity < $detail['quantity']) {
                throw ValidationException::withMessages([
                    'details' => "الكمية المطلوبة للصنف رقم {$detail['item_id']} ({$detail['quantity']}) تتجاوز الكمية المتوفرة ({$availableQuantity}).",
                ]);
            }
        }
    }

    /**
     * خصم الكمية من المخزون.
     *
     * @param int $itemId
     * @param int $warehouseId
     * @param float $quantity
     */
    protected function deductStock(int $itemId, int $warehouseId, float $quantity): void
    {
        // منطق خصم الكمية من جدول المخزون (Stock)
        // هذا مثال مبسط، في نظام حقيقي قد يتضمن تعقيدات مثل FIFO/LIFO
        Stock::where('item_id', $itemId)
             ->where('warehouse_id', $warehouseId)
             ->decrement('quantity', $quantity);
    }

    /**
     * إعادة الكمية إلى المخزون (عند الإلغاء).
     *
     * @param int $itemId
     * @param int $warehouseId
     * @param float $quantity
     */
    protected function returnStock(int $itemId, int $warehouseId, float $quantity): void
    {
        // منطق إضافة الكمية إلى جدول المخزون (Stock)
        Stock::where('item_id', $itemId)
             ->where('warehouse_id', $warehouseId)
             ->increment('quantity', $quantity);
    }

    /**
     * حساب إجمالي المبلغ من تفاصيل الإخراج.
     *
     * @param array $details
     * @return float
     */
    protected function calculateTotalAmount(array $details): float
    {
        $total = 0;
        foreach ($details as $detail) {
            $total += $detail['quantity'] * $detail['unit_price'];
        }
        return $total;
    }

    /**
     * توليد رقم إذن إخراج فريد.
     *
     * @return string
     */
    protected function generateStockOutNumber(): string
    {
        // مثال: SO-20251205-0001
        return 'SO-' . now()->format('Ymd') . '-' . str_pad(StockOut::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * جلب الكمية المتوفرة من المخزون.
     *
     * @param int $itemId
     * @param int $warehouseId
     * @return float
     */
    protected function getAvailableStock(int $itemId, int $warehouseId): float
    {
        // افتراض أن جدول Stock يحتوي على item_id, warehouse_id, و quantity
        return Stock::where('item_id', $itemId)
                    ->where('warehouse_id', $warehouseId)
                    ->value('quantity') ?? 0;
    }
}
