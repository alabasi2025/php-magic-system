<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Purchase Invoice Type Model
 * أنواع فواتير المشتريات
 * 
 * @property int $id
 * @property string $name اسم نوع الفاتورة
 * @property string $code رمز النوع للترقيم
 * @property string $prefix بادئة الترقيم
 * @property string|null $description وصف النوع
 * @property bool $is_active حالة التفعيل
 * @property int $last_number آخر رقم مستخدم
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class PurchaseInvoiceType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'prefix',
        'description',
        'is_active',
        'last_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'last_number' => 'integer',
    ];

    /**
     * Get the purchase invoices for this type.
     * الحصول على فواتير المشتريات لهذا النوع
     *
     * @return HasMany
     */
    public function purchaseInvoices(): HasMany
    {
        return $this->hasMany(PurchaseInvoice::class, 'invoice_type_id');
    }

    /**
     * Get the next invoice number for this type.
     * الحصول على رقم الفاتورة التالي لهذا النوع
     *
     * @return string
     */
    public function getNextInvoiceNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        
        // زيادة آخر رقم
        $this->increment('last_number');
        $this->refresh();
        
        return "{$this->prefix}-{$year}{$month}-" . str_pad($this->last_number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope to get only active types.
     * نطاق للحصول على الأنواع النشطة فقط
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
