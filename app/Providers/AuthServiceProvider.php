<?php

namespace App\Providers;

use App\Models\StockIn;
use App\Policies\StockInPolicy;
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
    ];

    /**
     * تسجيل أي خدمات مصادقة/تخويل.
     */
    public function boot(): void
    {
        //
    }
}
