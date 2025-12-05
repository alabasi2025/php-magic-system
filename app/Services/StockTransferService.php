<?php

namespace App\Services;

use App\Models\StockTransfer;
use App\Models\StockTransferDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Exceptions\StockTransferException; // افتراض وجود استثناء مخصص

/**
 * خدمة منطق الأعمال لتحويلات المخزون.
 */
class StockTransferService
{
    protected $stockService;

    /**
     * تهيئة الخدمة.
     * نفترض حقن خدمة المخزون (StockService) للتعامل مع حركات المخزون الفعلية.
     */
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * إنشاء طلب تحويل مخزون جديد.
     *
     * @param array $data بيانات التحويل (تشمل details)
     * @param int $userId معرف المستخدم المنشئ
     * @return StockTransfer
     * @throws \Exception
     */
    public function createTransfer(array $data, int $userId): StockTransfer
    {
        // استخدام معاملة قاعدة بيانات لضمان سلامة البيانات
        return DB::transaction(function () use ($data, $userId) {
            // توليد رقم تحويل فريد (مثال بسيط)
            $data['number'] = 'ST-' . Str::upper(Str::random(8));
            $data['created_by'] = $userId;
            $data['status'] = 'pending'; // الحالة الافتراضية

            // 1. إنشاء سجل التحويل الرئيسي
            $transfer = StockTransfer::create($data);

            // 2. إضافة تفاصيل التحويل
            $details = collect($data['details'])->map(function ($detail) use ($transfer) {
                return new StockTransferDetail([
                    'item_id' => $detail['item_id'],
                    'quantity' => $detail['quantity'],
                ]);
            });

            $transfer->details()->saveMany($details);

            return $transfer;
        });
    }

    /**
     * الموافقة على طلب تحويل المخزون.
     *
     * @param StockTransfer $transfer نموذج التحويل
     * @param int $approverId معرف المستخدم الموافق
     * @return StockTransfer
     * @throws StockTransferException
     */
    public function approveTransfer(StockTransfer $transfer, int $approverId): StockTransfer
    {
        if ($transfer->status !== 'pending') {
            throw new StockTransferException('لا يمكن الموافقة على تحويل ليس في حالة "قيد الانتظار".');
        }

        // استخدام معاملة قاعدة بيانات لضمان سلامة البيانات
        return DB::transaction(function () use ($transfer, $approverId) {
            // 1. تحديث حالة التحويل وبيانات الموافق
            $transfer->status = 'approved';
            $transfer->approved_by = $approverId;
            $transfer->save();

            // 2. خصم الكميات من المخزن المصدر وإضافتها للمخزن المستقبل
            foreach ($transfer->details as $detail) {
                $itemId = $detail->item_id;
                $quantity = $detail->quantity;
                $fromWarehouseId = $transfer->from_warehouse_id;
                $toWarehouseId = $transfer->to_warehouse_id;

                // خصم من المصدر
                $this->stockService->deductStock($fromWarehouseId, $itemId, $quantity);

                // إضافة للمستقبل
                $this->stockService->addStock($toWarehouseId, $itemId, $quantity);
            }

            // يمكن تغيير الحالة إلى 'completed' هنا أو في عملية منفصلة
            $transfer->status = 'completed';
            $transfer->save();

            return $transfer;
        });
    }

    /**
     * إلغاء أو رفض طلب تحويل المخزون.
     *
     * @param StockTransfer $transfer نموذج التحويل
     * @return StockTransfer
     * @throws StockTransferException
     */
    public function rejectTransfer(StockTransfer $transfer): StockTransfer
    {
        if ($transfer->status !== 'pending') {
            throw new StockTransferException('لا يمكن رفض تحويل ليس في حالة "قيد الانتظار".');
        }

        $transfer->status = 'rejected';
        $transfer->save();

        return $transfer;
    }
}
