<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Holding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * HoldingController - إدارة الشركات القابضة
 */
class HoldingController extends Controller
{
    /**
     * عرض قائمة الشركات القابضة
     */
    public function index()
    {
        $holdings = Holding::with(['creator', 'updater'])
            ->withCount(['units', 'departments', 'projects'])
            ->latest()
            ->paginate(20);

        return view('organization.holdings.index', compact('holdings'));
    }

    /**
     * عرض نموذج إنشاء شركة قابضة جديدة
     */
    public function create()
    {
        return view('organization.holdings.create');
    }

    /**
     * حفظ شركة قابضة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:holdings,code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'commercial_register' => 'nullable|string|max:50',
            'legal_form' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:3',
            'fiscal_year_start' => 'nullable|string|max:5',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        $holding = Holding::create($validated);

        return redirect()
            ->route('holdings.show', $holding)
            ->with('success', 'تم إنشاء الشركة القابضة بنجاح');
    }

    /**
     * عرض تفاصيل شركة قابضة
     */
    public function show(Holding $holding)
    {
        $holding->load([
            'units' => function ($query) {
                $query->withCount(['departments', 'projects']);
            },
            'creator',
            'updater'
        ]);

        $stats = [
            'total_units' => $holding->units()->count(),
            'active_units' => $holding->units()->where('is_active', true)->count(),
            'total_departments' => $holding->departments()->count(),
            'total_projects' => $holding->projects()->count(),
            'active_projects' => $holding->projects()->where('status', 'active')->count(),
        ];

        return view('organization.holdings.show', compact('holding', 'stats'));
    }

    /**
     * عرض نموذج تعديل شركة قابضة
     */
    public function edit(Holding $holding)
    {
        return view('organization.holdings.edit', compact('holding'));
    }

    /**
     * تحديث شركة قابضة
     */
    public function update(Request $request, Holding $holding)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:holdings,code,' . $holding->id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'commercial_register' => 'nullable|string|max:50',
            'legal_form' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:3',
            'fiscal_year_start' => 'nullable|string|max:5',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        $holding->update($validated);

        return redirect()
            ->route('holdings.show', $holding)
            ->with('success', 'تم تحديث الشركة القابضة بنجاح');
    }

    /**
     * حذف شركة قابضة
     */
    public function destroy(Holding $holding)
    {
        // التحقق من عدم وجود وحدات تابعة
        if ($holding->units()->count() > 0) {
            return redirect()
                ->route('holdings.show', $holding)
                ->with('error', 'لا يمكن حذف الشركة القابضة لوجود وحدات تابعة لها');
        }

        $holding->delete();

        return redirect()
            ->route('holdings.index')
            ->with('success', 'تم حذف الشركة القابضة بنجاح');
    }
}
