<?php

namespace App\Policies;

use App\Models\User;
use App\Models\JournalEntry;

class JournalEntryPolicy
{
    /**
     * Determine if the user can view any journal entries.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('journal-entries.view');
    }

    /**
     * Determine if the user can view the journal entry.
     */
    public function view(User $user, JournalEntry $journalEntry): bool
    {
        return $user->hasPermission('journal-entries.view');
    }

    /**
     * Determine if the user can create journal entries.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('journal-entries.create');
    }

    /**
     * Determine if the user can update the journal entry.
     */
    public function update(User $user, JournalEntry $journalEntry): bool
    {
        // لا يمكن تعديل قيد معتمد أو مرحّل
        if (in_array($journalEntry->status, ['approved', 'posted'])) {
            return false;
        }
        
        return $user->hasPermission('journal-entries.update');
    }

    /**
     * Determine if the user can delete the journal entry.
     */
    public function delete(User $user, JournalEntry $journalEntry): bool
    {
        // لا يمكن حذف قيد معتمد أو مرحّل
        if (in_array($journalEntry->status, ['approved', 'posted'])) {
            return false;
        }
        
        return $user->hasPermission('journal-entries.delete');
    }

    /**
     * Determine if the user can approve the journal entry.
     */
    public function approve(User $user, JournalEntry $journalEntry): bool
    {
        // فقط القيود بحالة "قيد المراجعة"
        if ($journalEntry->status !== 'pending') {
            return false;
        }
        
        return $user->hasPermission('journal-entries.approve');
    }

    /**
     * Determine if the user can post the journal entry.
     */
    public function post(User $user, JournalEntry $journalEntry): bool
    {
        // فقط القيود المعتمدة
        if ($journalEntry->status !== 'approved') {
            return false;
        }
        
        return $user->hasPermission('journal-entries.post');
    }

    /**
     * Determine if the user can reject the journal entry.
     */
    public function reject(User $user, JournalEntry $journalEntry): bool
    {
        // فقط القيود بحالة "قيد المراجعة"
        if ($journalEntry->status !== 'pending') {
            return false;
        }
        
        return $user->hasPermission('journal-entries.reject');
    }
}
