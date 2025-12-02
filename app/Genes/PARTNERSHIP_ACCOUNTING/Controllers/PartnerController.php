<?php

namespace App\Genes\PARTNERSHIP_ACCOUNTING\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\Partner;
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\PartnershipShare;

class PartnerController extends Controller
{
    /**
     * عرض قائمة الشركاء
     */
    public function index(Request $request)
    {
        $query = Partner::query();
        
        // البحث
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // التصفية حسب الحالة
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        $partners = $query->with('shares')->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $partners
        ]);
    }
    
    /**
     * إنشاء شريك جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        $partner = Partner::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الشريك بنجاح',
            'data' => $partner
        ], 201);
    }
    
    /**
     * عرض تفاصيل شريك
     */
    public function show(Partner $partner)
    {
        $partner->load(['shares.unit', 'shares.project']);
        
        return response()->json([
            'success' => true,
            'data' => $partner
        ]);
    }
    
    /**
     * تحديث بيانات شريك
     */
    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        $partner->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الشريك بنجاح',
            'data' => $partner
        ]);
    }
    
    /**
     * حذف شريك
     */
    public function destroy(Partner $partner)
    {
        // التحقق من عدم وجود حصص نشطة
        if ($partner->shares()->where('is_active', true)->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الشريك لوجود حصص نشطة'
            ], 422);
        }
        
        $partner->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'تم حذف الشريك بنجاح'
        ]);
    }
    
    /**
     * الحصول على حصص الشريك
     */
    public function getShares(Partner $partner)
    {
        $shares = $partner->shares()
            ->with(['unit', 'project'])
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $shares
        ]);
    }
    
    /**
     * تحديث حصص الشريك
     */
    public function updateShares(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'project_id' => 'nullable|exists:projects,id',
            'share_percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean'
        ]);
        
        // التحقق من عدم تجاوز 100%
        $existingShares = PartnershipShare::where('unit_id', $validated['unit_id'])
            ->where('project_id', $validated['project_id'] ?? null)
            ->where('partner_id', '!=', $partner->id)
            ->where('is_active', true)
            ->sum('share_percentage');
        
        if (($existingShares + $validated['share_percentage']) > 100) {
            return response()->json([
                'success' => false,
                'message' => 'مجموع الحصص يتجاوز 100%'
            ], 422);
        }
        
        $share = PartnershipShare::updateOrCreate(
            [
                'partner_id' => $partner->id,
                'unit_id' => $validated['unit_id'],
                'project_id' => $validated['project_id'] ?? null
            ],
            [
                'share_percentage' => $validated['share_percentage'],
                'is_active' => $validated['is_active'] ?? true
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الحصة بنجاح',
            'data' => $share
        ]);
    }
}
