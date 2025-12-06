<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * نموذج إعدادات الترقيم التلقائي
 */
class AutoNumberingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_type',
        'prefix',
        'pattern',
        'padding',
        'current_number',
        'reset_yearly',
        'reset_monthly',
        'is_active',
    ];

    protected $casts = [
        'reset_yearly' => 'boolean',
        'reset_monthly' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * توليد الرقم التالي
     */
    public function generateNextNumber(): string
    {
        $this->increment('current_number');
        
        $pattern = $this->pattern;
        $number = str_pad($this->current_number, $this->padding, '0', STR_PAD_LEFT);
        
        $replacements = [
            '{PREFIX}' => $this->prefix ?? '',
            '{YEAR}' => date('Y'),
            '{MONTH}' => date('m'),
            '{NUMBER}' => $number,
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $pattern);
    }
}
