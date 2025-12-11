<?php

namespace App\Providers;

use App\Models\JournalEntry;
use App\Observers\JournalEntryObserver;
use App\Models\ChartAccount;
use App\Observers\ChartAccountObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        JournalEntry::observe(JournalEntryObserver::class);
        ChartAccount::observe(ChartAccountObserver::class);
    }
}
