<?php

namespace App\Providers;

use App\Models\StockIn;
use App\Policies\StockInPolicy;
use App\Models\JournalEntry;
use App\Policies\JournalEntryPolicy;
use App\Models\ChartAccount;
use App\Policies\ChartAccountPolicy;
use App\Models\FiscalYear;
use App\Policies\FiscalYearPolicy;
use App\Models\FiscalPeriod;
use App\Policies\FiscalPeriodPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * خريطة ربط النماذج بالسياسات الخاصة بها.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        StockIn::class => StockInPolicy::class,
        JournalEntry::class => JournalEntryPolicy::class,
        ChartAccount::class => ChartAccountPolicy::class,
        FiscalYear::class => FiscalYearPolicy::class,
        FiscalPeriod::class => FiscalPeriodPolicy::class,
    ];

    /**
     * تسجيل أي خدمات مصادقة/تخويل.
     */
    public function boot(): void
    {
        //
    }
}
