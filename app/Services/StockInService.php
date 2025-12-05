<?php

namespace App\Services;

use App\Models\StockIn;
use App\Models\StockInDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class StockInService
{
    /**
     * إنشاء إذن إدخال جديد وتفاصيله.
     *
     * @param array $data بيانات إذن الإدخال
     * @return StockIn
     * @throws Exception
     */
    public function createStockIn(array $data): StockIn
    {
        // استخدام المعاملات لضمان إما نجاح العملية بالكامل أو فشلها بالكامل
        return DB::transaction(function () use ($data) {
            // 1. حساب الإجماليات وتوليد رقم الإذن
            $totalAmount = $this->calculateTotalAmount($data['details']);
            $stockInNumber = $this->generateStockInNumber(); // دالة لتوليد رقم فريد

            // 2. إنشاء إذن الإدخال الرئيسي
            $stockIn = StockIn::create([
                'number' => $stockInNumber,
                'warehouse_id' => $data['warehouse_id'],
                'supplier_id' => $data['supplier_id'],
                'date' => $data['date'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'total_amount' => $totalAmount,
                'status' => 'Draft', // الحالة الافتراضية
                'created_by' => Auth::id(),
            ]);

            // 3. إنشاء تفاصيل الأصناف
            $details = collect($data['details'])->map(function ($detail) use ($stockIn) {
                $detail['stock_in_id'] = $stockIn->id;
                $detail['total_price'] = $detail['quantity'] * $detail['unit_price'];
                return new StockInDetail($detail);
            });

            $stockIn->details()->saveMany($details);

            return $stockIn;
        });
    }

    /**
     * تحديث إذن إدخال موجود وتفاصيله.
     *
     * @param StockIn $stockIn نموذج إذن الإدخال المراد تحديثه
     * @param array $data البيانات الجديدة
     * @return StockIn
     * @throws Exception
     */
    public function updateStockIn(StockIn $stockIn, array $data): StockIn
    {
        // يجب التحقق من حالة الإذن قبل التحديث (مثلاً، لا يمكن تحديث إذن مكتمل)
        if ($stockIn->status !== 'Draft') {
            throw new Exception('لا يمكن تعديل إذن إدخال ليس في حالة المسودة.');
        }

        return DB::transaction(function () use ($stockIn, $data) {
            // 1. حساب الإجماليات
            $totalAmount = $this->calculateTotalAmount($data['details']);

            // 2. تحديث إذن الإدخال الرئيسي
            $stockIn->update([
                'warehouse_id' => $data['warehouse_id'],
                'supplier_id' => $data['supplier_id'],
                'date' => $data['date'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'total_amount' => $totalAmount,
                // لا نغير الحالة هنا، يمكن أن تكون هناك دالة منفصلة لتغيير الحالة
            ]);

            // 3. تحديث تفاصيل الأصناف (حذف القديم وإضافة الجديد)
            $stockIn->details()->delete();
            
            $details = collect($data['details'])->map(function ($detail) use ($stockIn) {
                $detail['stock_in_id'] = $stockIn->id;
                $detail['total_price'] = $detail['quantity'] * $detail['unit_price'];
                return new StockInDetail($detail);
            });

            $stockIn->details()->saveMany($details);

            return $stockIn;
        });
    }

    /**
     * حذف إذن إدخال.
     *
     * @param StockIn $stockIn
     * @return bool
     * @throws Exception
     */
    public function deleteStockIn(StockIn $stockIn): bool
    {
        // يجب التحقق من حالة الإذن قبل الحذف
        if ($stockIn->status !== 'Draft') {
            throw new Exception('لا يمكن حذف إذن إدخال ليس في حالة المسودة.');
        }

        // سيتم حذف التفاصيل تلقائياً بسبب onDelete('cascade') في الهجرة
        return $stockIn->delete();
    }

    /**
     * حساب إجمالي المبلغ من تفاصيل الأصناف.
     *
     * @param array $details
     * @return float
     */
    protected function calculateTotalAmount(array $details): float
    {
        return collect($details)->sum(function ($detail) {
            return $detail['quantity'] * $detail['unit_price'];
        });
    }

    /**
     * توليد رقم إذن إدخال فريد.
     * يمكن تخصيص هذه الدالة لتناسب متطلبات الترقيم في النظام.
     *
     * @return string
     */
    protected function generateStockInNumber(): string
    {
        // مثال بسيط: استخدام التاريخ والوقت مع رقم عشوائي
        // في نظام حقيقي، يفضل استخدام تسلسل قاعدة بيانات أو خدمة ترقيم
        return 'SI-' . date('Ymd') . '-' . str_pad(StockIn::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * دالة إضافية: ترحيل إذن الإدخال (تغيير حالته إلى مكتمل).
     * هذه الدالة هي التي يجب أن تقوم بتحديث أرصدة المخزون.
     *
     * @param StockIn $stockIn
     * @return StockIn
     * @throws Exception
     */
    public function completeStockIn(StockIn $stockIn): StockIn
    {
        if ($stockIn->status !== 'Draft') {
            throw new Exception('لا يمكن ترحيل إذن إدخال إلا إذا كان في حالة المسودة.');
        }

        return DB::transaction(function () use ($stockIn) {
            // 1. تحديث أرصدة المخزون (هذا هو منطق الأعمال الأساسي)
            // يجب استدعاء خدمة أخرى هنا (مثل InventoryService) لتحديث الكميات
            // مثال:
            // foreach ($stockIn->details as $detail) {
            //     InventoryService::addStock($detail->item_id, $stockIn->warehouse_id, $detail->quantity);
            // }

            // 2. تغيير حالة الإذن
            $stockIn->status = 'Completed';
            $stockIn->save();

            return $stockIn;
        });
    }
}
