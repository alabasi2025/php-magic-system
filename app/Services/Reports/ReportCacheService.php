<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ReportCacheService
{
    protected $ttl = 3600; // 1 hour default

    public function get(string $key)
    {
        return Cache::get($this->getCacheKey($key));
    }

    public function put(string $key, $data, ?int $ttl = null): bool
    {
        return Cache::put($this->getCacheKey($key), $data, $ttl ?? $this->ttl);
    }

    public function forget(string $key): bool
    {
        return Cache::forget($this->getCacheKey($key));
    }

    public function flush(string $prefix = 'reports'): bool
    {
        return Cache::flush(); // Or use tags if supported
    }

    protected function getCacheKey(string $key): string
    {
        return 'reports:' . $key;
    }

    public function remember(string $key, callable $callback, ?int $ttl = null)
    {
        return Cache::remember($this->getCacheKey($key), $ttl ?? $this->ttl, $callback);
    }
}
