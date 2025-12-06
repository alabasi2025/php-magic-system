<?php

namespace App\Http\Controllers;

use App\Models\AutoNumberingSetting;
use Illuminate\Http\Request;

/**
 * متحكم إعدادات الترقيم التلقائي
 */
class AutoNumberingSettingController extends Controller
{
    /**
     * عرض صفحة إعدادات الترقيم
     */
    public function index()
    {
        $settings = AutoNumberingSetting::all();
        return view('settings.auto_numbering', compact('settings'));
    }

    /**
     * تحديث أو إنشاء إعدادات الترقيم
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_type' => 'required|string',
            'prefix' => 'nullable|string|max:10',
            'pattern' => 'required|string',
            'padding' => 'required|integer|min:1|max:10',
            'reset_yearly' => 'boolean',
            'reset_monthly' => 'boolean',
            'is_active' => 'boolean',
        ]);

        AutoNumberingSetting::updateOrCreate(
            ['entity_type' => $validated['entity_type']],
            $validated
        );

        return redirect()->back()->with('success', 'تم حفظ الإعدادات بنجاح!');
    }
}
