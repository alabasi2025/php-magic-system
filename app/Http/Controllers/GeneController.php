<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientGene;
use App\Helpers\GeneHelper;

/**
 * GeneController
 * 
 * Controller لإدارة الجينات (المميزات القابلة للتفعيل/التعطيل)
 */
class GeneController extends Controller
{
    /**
     * عرض صفحة الجينات
     */
    public function index()
    {
        $clientCode = GeneHelper::getClientCode();
        
        // الحصول على الجينات المتاحة
        $availableGenes = config('system.available_genes', []);
        
        // الحصول على الجينات المفعلة
        $activeGenes = GeneHelper::getActiveGenes();
        
        // إضافة حالة التفعيل لكل جين
        $genes = collect($availableGenes)->map(function ($gene, $geneName) use ($activeGenes) {
            $gene['gene_name'] = $geneName;
            $gene['is_active'] = in_array($geneName, $activeGenes);
            $gene['configuration'] = $gene['is_active'] 
                ? GeneHelper::getConfiguration($geneName) 
                : null;
            return $gene;
        });
        
        return view('modules.genes', compact('genes', 'clientCode'));
    }

    /**
     * تفعيل جين
     */
    public function activate(Request $request, $geneName)
    {
        $request->validate([
            'configuration' => 'nullable|array',
        ]);

        $configuration = $request->input('configuration');
        
        if (GeneHelper::activate($geneName, $configuration)) {
            return response()->json([
                'success' => true,
                'message' => 'تم تفعيل الجين بنجاح',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'فشل تفعيل الجين',
        ], 500);
    }

    /**
     * تعطيل جين
     */
    public function deactivate($geneName)
    {
        if (GeneHelper::deactivate($geneName)) {
            return response()->json([
                'success' => true,
                'message' => 'تم تعطيل الجين بنجاح',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'فشل تعطيل الجين',
        ], 500);
    }

    /**
     * تحديث إعدادات جين
     */
    public function configure(Request $request, $geneName)
    {
        $request->validate([
            'configuration' => 'required|array',
        ]);

        $clientCode = GeneHelper::getClientCode();
        $clientName = config('system.client_name', 'SEMOP System');
        $configuration = $request->input('configuration');

        try {
            ClientGene::activateGene($clientCode, $clientName, $geneName, $configuration);
            GeneHelper::clearCache($geneName);
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث إعدادات الجين بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تحديث الإعدادات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الحصول على معلومات جين
     */
    public function show($geneName)
    {
        $geneInfo = GeneHelper::getGeneInfo($geneName);
        
        if (!$geneInfo) {
            return response()->json([
                'success' => false,
                'message' => 'الجين غير موجود',
            ], 404);
        }

        $isActive = GeneHelper::isActive($geneName);
        $configuration = $isActive ? GeneHelper::getConfiguration($geneName) : null;
        $requirements = GeneHelper::checkRequirements($geneName);

        return response()->json([
            'success' => true,
            'gene' => array_merge($geneInfo, [
                'gene_name' => $geneName,
                'is_active' => $isActive,
                'configuration' => $configuration,
                'requirements' => $requirements,
            ]),
        ]);
    }
}
