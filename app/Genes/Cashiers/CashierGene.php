<?php

namespace App\Genes\Cashiers;

use App\Models\Cashier;
use Illuminate\Database\Eloquent\Collection;

/**
 * @trait CashierGene
 *
 * واجهة (Gene) تحدد العمليات الأساسية لمنطق أعمال الصرافين.
 * يجب أن تطبق هذه الواجهة في الخدمات (Services) لضمان التزامها بالعقد.
 */
trait CashierGene
{
    /**
     * معالجة عملية مالية جديدة (إيداع، سحب، إلخ).
     *
     * @param Cashier $cashier الصراف الذي يقوم بالعملية.
     * @param array $data بيانات العملية (المبلغ، النوع، الوصف، إلخ).
     * @return \App\Models\Transaction العملية المنجزة.
     */
    abstract public function processTransaction(Cashier $cashier, array $data): \App\Models\Transaction;

    /**
     * الحصول على سجل العمليات لصراف معين.
     *
     * @param Cashier $cashier الصراف المطلوب سجل عملياته.
     * @return Collection|array قائمة بالعمليات.
     */
    abstract public function getTransactionHistory(Cashier $cashier): Collection|array;

    /**
     * تحديث حالة الصراف (متاح، غير متاح، في استراحة).
     *
     * @param Cashier $cashier الصراف المراد تحديث حالته.
     * @param string $status الحالة الجديدة.
     * @return Cashier الصراف بعد تحديث حالته.
     */
    abstract public function updateCashierStatus(Cashier $cashier, string $status): Cashier;
}