<?php

namespace App\Helpers;

use App\Models\ClientGene;
use Illuminate\Support\Facades\Cache;

/**
 * Gene Helper
 * 
 * مساعد للتحقق من تفعيل الجينات واستخدامها
 */
class GeneHelper
{
    /**
     * الحصول على كود العميل من الإعدادات
     */
    public static function getClientCode(): string
    {
        return config('system.client_code', 'SEMOP');
    }

    /**
     * التحقق من تفعيل جين
     */
    public static function isActive(string $geneName): bool
    {
        $clientCode = static::getClientCode();
        
        // استخدام Cache لتحسين الأداء
        $cacheKey = "gene_active_{$clientCode}_{$geneName}";
        
        return Cache::remember($cacheKey, 3600, function () use ($clientCode, $geneName) {
            return ClientGene::isGeneActiveForClient($clientCode, $geneName);
        });
    }

    /**
     * الحصول على إعدادات جين
     */
    public static function getConfiguration(string $geneName): ?array
    {
        $clientCode = static::getClientCode();
        
        $cacheKey = "gene_config_{$clientCode}_{$geneName}";
        
        return Cache::remember($cacheKey, 3600, function () use ($clientCode, $geneName) {
            return ClientGene::getGeneConfiguration($clientCode, $geneName);
        });
    }

    /**
     * تفعيل جين
     */
    public static function activate(string $geneName, ?array $configuration = null): bool
    {
        $clientCode = static::getClientCode();
        $clientName = config('system.client_name', 'SEMOP System');
        
        try {
            ClientGene::activateGene($clientCode, $clientName, $geneName, $configuration);
            static::clearCache($geneName);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * تعطيل جين
     */
    public static function deactivate(string $geneName): bool
    {
        $clientCode = static::getClientCode();
        
        $result = ClientGene::deactivateGene($clientCode, $geneName);
        
        if ($result) {
            static::clearCache($geneName);
        }
        
        return $result;
    }

    /**
     * الحصول على كل الجينات المفعلة
     */
    public static function getActiveGenes(): array
    {
        $clientCode = static::getClientCode();
        
        $cacheKey = "active_genes_{$clientCode}";
        
        return Cache::remember($cacheKey, 3600, function () use ($clientCode) {
            return ClientGene::getActiveGenesForClient($clientCode);
        });
    }

    /**
     * الحصول على معلومات جين من الإعدادات
     */
    public static function getGeneInfo(string $geneName): ?array
    {
        $availableGenes = config('system.available_genes', []);
        
        return $availableGenes[$geneName] ?? null;
    }

    /**
     * التحقق من توفر جين
     */
    public static function isAvailable(string $geneName): bool
    {
        return static::getGeneInfo($geneName) !== null;
    }

    /**
     * مسح الكاش لجين معين
     */
    public static function clearCache(?string $geneName = null): void
    {
        $clientCode = static::getClientCode();
        
        if ($geneName) {
            Cache::forget("gene_active_{$clientCode}_{$geneName}");
            Cache::forget("gene_config_{$clientCode}_{$geneName}");
        }
        
        Cache::forget("active_genes_{$clientCode}");
    }

    /**
     * التحقق من المتطلبات (الجينات المطلوبة)
     */
    public static function checkRequirements(string $geneName): array
    {
        $geneInfo = static::getGeneInfo($geneName);
        
        if (!$geneInfo) {
            return [
                'satisfied' => false,
                'missing' => [],
                'message' => 'الجين غير متوفر',
            ];
        }
        
        $requires = $geneInfo['requires'] ?? [];
        
        if (empty($requires)) {
            return [
                'satisfied' => true,
                'missing' => [],
                'message' => 'لا توجد متطلبات',
            ];
        }
        
        $missing = [];
        foreach ($requires as $requiredGene) {
            if (!static::isActive($requiredGene)) {
                $missing[] = $requiredGene;
            }
        }
        
        return [
            'satisfied' => empty($missing),
            'missing' => $missing,
            'message' => empty($missing) 
                ? 'كل المتطلبات متوفرة' 
                : 'يتطلب تفعيل: ' . implode(', ', $missing),
        ];
    }
}
