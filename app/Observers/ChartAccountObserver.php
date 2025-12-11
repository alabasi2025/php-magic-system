<?php

namespace App\Observers;

use App\Models\ChartAccount;
use App\Models\AuditLog;

class ChartAccountObserver
{
    /**
     * Handle the ChartAccount "created" event.
     */
    public function created(ChartAccount $chartAccount): void
    {
        AuditLog::log(
            event: 'created',
            model: $chartAccount,
            newValues: $chartAccount->toArray(),
            description: "تم إنشاء حساب جديد: {$chartAccount->account_code} - {$chartAccount->account_name}"
        );
    }

    /**
     * Handle the ChartAccount "updated" event.
     */
    public function updated(ChartAccount $chartAccount): void
    {
        AuditLog::log(
            event: 'updated',
            model: $chartAccount,
            oldValues: $chartAccount->getOriginal(),
            newValues: $chartAccount->getChanges(),
            description: "تم تعديل الحساب: {$chartAccount->account_code} - {$chartAccount->account_name}"
        );
    }

    /**
     * Handle the ChartAccount "deleted" event.
     */
    public function deleted(ChartAccount $chartAccount): void
    {
        AuditLog::log(
            event: 'deleted',
            model: $chartAccount,
            oldValues: $chartAccount->toArray(),
            description: "تم حذف الحساب: {$chartAccount->account_code} - {$chartAccount->account_name}"
        );
    }
}
