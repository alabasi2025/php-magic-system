<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FiscalYear;

class FiscalYearPolicy
{
    /**
     * Determine if the user can view any fiscal years.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('fiscal-years.view');
    }

    /**
     * Determine if the user can view the fiscal year.
     */
    public function view(User $user, FiscalYear $fiscalYear): bool
    {
        return $user->hasPermission('fiscal-years.view');
    }

    /**
     * Determine if the user can create fiscal years.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('fiscal-years.create');
    }

    /**
     * Determine if the user can update the fiscal year.
     */
    public function update(User $user, FiscalYear $fiscalYear): bool
    {
        // لا يمكن تعديل سنة مالية مقفلة
        if ($fiscalYear->is_closed) {
            return false;
        }
        
        return $user->hasPermission('fiscal-years.update');
    }

    /**
     * Determine if the user can delete the fiscal year.
     */
    public function delete(User $user, FiscalYear $fiscalYear): bool
    {
        // لا يمكن حذف سنة مالية مقفلة أو لها فترات
        if ($fiscalYear->is_closed || $fiscalYear->periods()->count() > 0) {
            return false;
        }
        
        return $user->hasPermission('fiscal-years.delete');
    }

    /**
     * Determine if the user can close the fiscal year.
     */
    public function close(User $user, FiscalYear $fiscalYear): bool
    {
        // لا يمكن إقفال سنة مالية مقفلة
        if ($fiscalYear->is_closed) {
            return false;
        }
        
        // يجب أن تكون جميع الفترات مقفلة
        if ($fiscalYear->periods()->where('is_closed', false)->count() > 0) {
            return false;
        }
        
        return $user->hasPermission('fiscal-years.close');
    }
}
