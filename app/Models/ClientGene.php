<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ClientGene Model
 * 
 * نموذج لإدارة الجينات (المميزات) وتفعيلها/تعطيلها حسب الحاجة
 * كل تثبيت للنظام يمكنه تفعيل الجينات التي يحتاجها فقط
 * 
 * @property int $id
 * @property string $client_name اسم العميل/المؤسسة
 * @property string $client_code كود العميل الفريد
 * @property string $gene_name اسم الجين
 * @property bool $is_active هل الجين مفعل؟
 * @property array|null $configuration إعدادات الجين
 * @property string|null $notes ملاحظات
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class ClientGene extends Model
{
    use HasFactory;

    protected $table = 'client_genes';

    protected $fillable = [
        'client_name',
        'client_code',
        'gene_name',
        'is_active',
        'configuration',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'configuration' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * التحقق من تفعيل جين لعميل معين
     */
    public static function isGeneActiveForClient(string $clientCode, string $geneName): bool
    {
        return static::where('client_code', $clientCode)
            ->where('gene_name', $geneName)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * الحصول على إعدادات جين لعميل معين
     */
    public static function getGeneConfiguration(string $clientCode, string $geneName): ?array
    {
        $gene = static::where('client_code', $clientCode)
            ->where('gene_name', $geneName)
            ->where('is_active', true)
            ->first();

        return $gene?->configuration;
    }

    /**
     * تفعيل جين لعميل
     */
    public static function activateGene(string $clientCode, string $clientName, string $geneName, ?array $configuration = null): self
    {
        return static::updateOrCreate(
            [
                'client_code' => $clientCode,
                'gene_name' => $geneName,
            ],
            [
                'client_name' => $clientName,
                'is_active' => true,
                'configuration' => $configuration,
            ]
        );
    }

    /**
     * تعطيل جين لعميل
     */
    public static function deactivateGene(string $clientCode, string $geneName): bool
    {
        return static::where('client_code', $clientCode)
            ->where('gene_name' => $geneName)
            ->update(['is_active' => false]) > 0;
    }

    /**
     * الحصول على كل الجينات المفعلة لعميل
     */
    public static function getActiveGenesForClient(string $clientCode): array
    {
        return static::where('client_code', $clientCode)
            ->where('is_active', true)
            ->pluck('gene_name')
            ->toArray();
    }

    /**
     * الحصول على كل الجينات (مفعلة ومعطلة)
     */
    public static function getAllGenesForClient(string $clientCode): array
    {
        return static::where('client_code', $clientCode)
            ->get()
            ->map(function ($gene) {
                return [
                    'name' => $gene->gene_name,
                    'is_active' => $gene->is_active,
                    'configuration' => $gene->configuration,
                    'notes' => $gene->notes,
                ];
            })
            ->toArray();
    }
}
