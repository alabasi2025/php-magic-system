<?php

namespace App\Genes\CASHIERS\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Genes\CASHIERS\Models\Cashier; // افتراض وجود نموذج Cashier

class CashierController extends Controller
{
    /**
     * عرض قائمة بجميع الصرافين (Cashiers).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // التحقق مما إذا كان الطلب هو طلب API (JSON)
        if ($request->wantsJson() || $request->is('api/*')) {
            $cashiers = Cashier::paginate(15);
            return response()->json($cashiers);
        }

        // عرض واجهة المستخدم (View)
        $cashiers = Cashier::all();
        return view('cashiers.index', compact('cashiers'));
    }

    /**
     * عرض نموذج إنشاء صراف جديد.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('cashiers.create');
    }

    /**
     * تخزين صراف جديد في قاعدة البيانات.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:cashiers,email|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $cashier = Cashier::create($request->all());

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Cashier created successfully', 'data' => $cashier], 201);
        }

        return redirect()->route('cashiers.index')->with('success', 'تم إنشاء الصراف بنجاح.');
    }

    /**
     * عرض تفاصيل صراف محدد.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        $cashier = Cashier::findOrFail($id);

        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json($cashier);
        }

        return view('cashiers.show', compact('cashier'));
    }

    /**
     * عرض نموذج تعديل صراف محدد.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $cashier = Cashier::findOrFail($id);
        return view('cashiers.edit', compact('cashier'));
    }

    /**
     * تحديث صراف محدد في قاعدة البيانات.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $cashier = Cashier::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:cashiers,email,' . $id . '|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $cashier->update($request->all());

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Cashier updated successfully', 'data' => $cashier]);
        }

        return redirect()->route('cashiers.index')->with('success', 'تم تحديث بيانات الصراف بنجاح.');
    }

    /**
     * حذف صراف محدد من قاعدة البيانات.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $cashier = Cashier::findOrFail($id);
        $cashier->delete();

        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Cashier deleted successfully']);
        }

        return redirect()->route('cashiers.index')->with('success', 'تم حذف الصراف بنجاح.');
    }
}