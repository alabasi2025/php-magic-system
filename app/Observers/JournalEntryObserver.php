<?php

namespace App\Observers;

use App\Models\JournalEntry;
use App\Models\AuditLog;

class JournalEntryObserver
{
    /**
     * Handle the JournalEntry "created" event.
     */
    public function created(JournalEntry $journalEntry): void
    {
        AuditLog::log(
            event: 'created',
            model: $journalEntry,
            newValues: $journalEntry->toArray(),
            description: "تم إنشاء قيد محاسبي جديد: {$journalEntry->entry_number}"
        );
    }

    /**
     * Handle the JournalEntry "updated" event.
     */
    public function updated(JournalEntry $journalEntry): void
    {
        AuditLog::log(
            event: 'updated',
            model: $journalEntry,
            oldValues: $journalEntry->getOriginal(),
            newValues: $journalEntry->getChanges(),
            description: "تم تعديل القيد المحاسبي: {$journalEntry->entry_number}"
        );
    }

    /**
     * Handle the JournalEntry "deleted" event.
     */
    public function deleted(JournalEntry $journalEntry): void
    {
        AuditLog::log(
            event: 'deleted',
            model: $journalEntry,
            oldValues: $journalEntry->toArray(),
            description: "تم حذف القيد المحاسبي: {$journalEntry->entry_number}"
        );
    }
}
