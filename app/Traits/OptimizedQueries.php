<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

/**
 * Trait OptimizedQueries
 * 
 * Provides optimized query methods for models to improve performance
 * and reduce database load through caching and efficient queries.
 * 
 * @package App\Traits
 */
trait OptimizedQueries
{
    /**
     * Scope to get only active records
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get records with eager loading
     */
    public function scopeWithRelations(Builder $query, array $relations = []): Builder
    {
        if (empty($relations) && property_exists($this, 'defaultRelations')) {
            $relations = $this->defaultRelations;
        }
        
        return $query->with($relations);
    }

    /**
     * Get cached result of a query
     * 
     * @param string $cacheKey
     * @param \Closure $callback
     * @param int $ttl Time to live in seconds (default: 1 hour)
     * @return mixed
     */
    public static function getCached(string $cacheKey, \Closure $callback, int $ttl = 3600)
    {
        return Cache::remember($cacheKey, $ttl, $callback);
    }

    /**
     * Clear cache for a specific key
     * 
     * @param string $cacheKey
     * @return bool
     */
    public static function clearCache(string $cacheKey): bool
    {
        return Cache::forget($cacheKey);
    }

    /**
     * Clear all cache for this model
     * 
     * @return void
     */
    public static function clearAllCache(): void
    {
        $modelName = class_basename(static::class);
        $pattern = strtolower($modelName) . '_*';
        
        // This is a simple implementation
        // In production, you might want to use tags or a more sophisticated approach
        Cache::flush();
    }

    /**
     * Scope to select only specific columns
     * 
     * @param Builder $query
     * @param array $columns
     * @return Builder
     */
    public function scopeSelectOptimized(Builder $query, array $columns = ['*']): Builder
    {
        if ($columns === ['*']) {
            return $query;
        }
        
        // Always include the primary key
        if (!in_array('id', $columns)) {
            array_unshift($columns, 'id');
        }
        
        return $query->select($columns);
    }

    /**
     * Scope to check if records exist (more efficient than count)
     * 
     * @param Builder $query
     * @return bool
     */
    public function scopeExistsOptimized(Builder $query): bool
    {
        return $query->exists();
    }

    /**
     * Get paginated results with caching
     * 
     * @param int $perPage
     * @param string $cacheKey
     * @param int $ttl
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getPaginatedCached(int $perPage = 15, string $cacheKey = null, int $ttl = 3600)
    {
        $page = request()->get('page', 1);
        $cacheKey = $cacheKey ?? strtolower(class_basename(static::class)) . "_paginated_{$page}_{$perPage}";
        
        return self::getCached($cacheKey, function () use ($perPage) {
            return static::query()->paginate($perPage);
        }, $ttl);
    }

    /**
     * Process large datasets in chunks
     * 
     * @param int $chunkSize
     * @param \Closure $callback
     * @return bool
     */
    public static function processInChunks(int $chunkSize = 100, \Closure $callback): bool
    {
        return static::query()->chunk($chunkSize, $callback);
    }

    /**
     * Get count with caching
     * 
     * @param string $cacheKey
     * @param int $ttl
     * @return int
     */
    public static function getCountCached(string $cacheKey = null, int $ttl = 3600): int
    {
        $cacheKey = $cacheKey ?? strtolower(class_basename(static::class)) . '_count';
        
        return self::getCached($cacheKey, function () {
            return static::query()->count();
        }, $ttl);
    }

    /**
     * Boot the trait
     * 
     * Clear cache when model is created, updated, or deleted
     */
    protected static function bootOptimizedQueries(): void
    {
        static::created(function ($model) {
            $model->clearModelCache();
        });

        static::updated(function ($model) {
            $model->clearModelCache();
        });

        static::deleted(function ($model) {
            $model->clearModelCache();
        });
    }

    /**
     * Clear cache for this specific model instance
     * 
     * @return void
     */
    protected function clearModelCache(): void
    {
        static::clearAllCache();
    }
}
