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
        $prefix = "{$this->prefix}-{$year}{$month}-";
        
        // البحث عن آخر رقم فاتورة لهذا النوع في هذا الشهر
        $lastInvoice = PurchaseInvoice::where('invoice_type_id', $this->id)
            ->where('invoice_number', 'LIKE', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->lockForUpdate()
            ->first();
        
        if ($lastInvoice) {
            // استخراج الرقم من آخر invoice_number
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        // التحقق من عدم وجود الرقم (في حالة وجود فجوات)
        $maxAttempts = 100;
        $attempts = 0;
        
        while ($attempts < $maxAttempts) {
            $invoiceNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            
            // التحقق من عدم وجود هذا الرقم
            $exists = PurchaseInvoice::where('invoice_number', $invoiceNumber)->exists();
            
            if (!$exists) {
                // تحديث last_number في جدول الأنواع
                $this->update(['last_number' => $nextNumber]);
                return $invoiceNumber;
            }
            
            // إذا كان موجوداً، جرب الرقم التالي
            $nextNumber++;
            $attempts++;
        }
        
        // في حالة فشل جميع المحاولات
        throw new \Exception('فشل في توليد رقم فاتورة فريد لنوع ' . $this->name . ' بعد ' . $maxAttempts . ' محاولة');
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
