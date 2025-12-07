<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name_ar',
        'name_en',
        'icon',
        'description',
        'is_active',
        'is_system',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get active account types
     */
    public static function active()
    {
        return self::where('is_active', true)->orderBy('sort_order')->get();
    }

    /**
     * Get account type by key
     */
    public static function findByKey($key)
    {
        return self::where('key', $key)->first();
    }
}
