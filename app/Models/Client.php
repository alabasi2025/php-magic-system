<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Client Model
 * 
 * نموذج العملاء - العملاء الذين يستخدمون النظام
 * 
 * @property int $id
 * @property string $name اسم العميل
 * @property string $code كود العميل الفريد
 * @property string|null $business_type نوع العمل
 * @property string $country الدولة
 * @property string|null $city المدينة
 * @property string|null $contact_person الشخص المسؤول
 * @property string|null $phone الهاتف
 * @property string|null $email البريد الإلكتروني
 * @property bool $is_active نشط؟
 * @property array|null $settings إعدادات خاصة
 * @property string|null $notes ملاحظات
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'name',
        'code',
        'business_type',
        'country',
        'city',
        'contact_person',
        'phone',
        'email',
        'is_active',
        'settings',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * الجينات المفعلة للعميل
     */
    public function genes(): HasMany
    {
        return $this->hasMany(ClientGene::class, 'client_code', 'code');
    }

    /**
     * الجينات النشطة فقط
     */
    public function activeGenes(): HasMany
    {
        return $this->genes()->where('is_active', true);
    }

    /**
     * التحقق من تفعيل جين معين
     */
    public function hasGene(string $geneName): bool
    {
        return $this->activeGenes()
            ->where('gene_name', $geneName)
            ->exists();
    }

    /**
     * تفعيل جين
     */
    public function activateGene(string $geneName, ?array $configuration = null): ClientGene
    {
        return ClientGene::activateGene($this->code, $this->name, $geneName, $configuration);
    }

    /**
     * تعطيل جين
     */
    public function deactivateGene(string $geneName): bool
    {
        return ClientGene::deactivateGene($this->code, $geneName);
    }

    /**
     * الحصول على كل الجينات النشطة
     */
    public function getActiveGenesList(): array
    {
        return ClientGene::getActiveGenesForClient($this->code);
    }

    /**
     * الحصول على إعدادات جين
     */
    public function getGeneConfiguration(string $geneName): ?array
    {
        return ClientGene::getGeneConfiguration($this->code, $geneName);
    }
}
