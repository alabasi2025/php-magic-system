<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category; // نفترض وجود نموذج الفئة
use App\Models\Unit;     // نفترض وجود نموذج الوحدة
use App\Services\ItemService;
use App\Http\Requests\ItemRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemController extends Controller
{
    protected ItemService $itemService;

    /**
     * تهيئة المتحكم.
     *
     * @param ItemService $itemService
     */
    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
        // تطبيق سياسة الأمان (Authorization)
        // $this->authorizeResource(Item::class, 'item');
    }

    /**
     * عرض قائمة بجميع الأصناف (index).
     *
     * @return View
     */
    public function index(): View
    {
        // جلب الأصناف باستخدام طبقة الخدمة
        $items = $this->itemService->getAllItems();

        return view('items.index', compact('items'));
    }

    /**
     * عرض نموذج إنشاء صنف جديد (create).
     *
     * @return View
     */
    public function create(): View
    {
        // جلب البيانات اللازمة لملء القوائم المنسدلة
        $categories = Category::all();
        $units = Unit::all();

        return view('items.create', compact('categories', 'units'));
    }

    /**
     * تخزين صنف جديد في قاعدة البيانات (store).
     *
     * @param ItemRequest $request
     * @return RedirectResponse
     */
    public function store(ItemRequest $request): RedirectResponse
    {
        try {
            // استخدام طبقة الخدمة لتنفيذ منطق الأعمال
            $this->itemService->createItem($request->validated());

            return redirect()->route('items.index')
                             ->with('success', 'تم إنشاء الصنف بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->withInput()
                         ->with('error', 'فشل في إنشاء الصنف: ' . $e->getMessage());
        }
    }

    /**
     * عرض صنف محدد (show).
     *
     * @param Item $item
     * @return View
     */
    public function show(Item $item): View
    {
        // تحميل العلاقات قبل العرض
        $item->load(['category', 'unit']);

        return view('items.show', compact('item'));
    }

    /**
     * عرض نموذج تعديل صنف موجود (edit).
     *
     * @param Item $item
     * @return View
     */
    public function edit(Item $item): View
    {
        // جلب البيانات اللازمة لملء القوائم المنسدلة
        $categories = Category::all();
        $units = Unit::all();

        return view('items.edit', compact('item', 'categories', 'units'));
    }

    /**
     * تحديث صنف محدد في قاعدة البيانات (update).
     *
     * @param ItemRequest $request
     * @param Item $item
     * @return RedirectResponse
     */
    public function update(ItemRequest $request, Item $item): RedirectResponse
    {
        try {
            // استخدام طبقة الخدمة لتنفيذ منطق الأعمال
            $this->itemService->updateItem($item, $request->validated());

            return redirect()->route('items.index')
                             ->with('success', 'تم تحديث الصنف بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->withInput()
                         ->with('error', 'فشل في تحديث الصنف: ' . $e->getMessage());
        }
    }

    /**
     * حذف صنف محدد من قاعدة البيانات (destroy).
     *
     * @param Item $item
     * @return RedirectResponse
     */
    public function destroy(Item $item): RedirectResponse
    {
        try {
            // استخدام طبقة الخدمة لتنفيذ منطق الأعمال
            $this->itemService->deleteItem($item);

            return redirect()->route('items.index')
                             ->with('success', 'تم حذف الصنف بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->with('error', 'فشل في حذف الصنف: ' . $e->getMessage());
        }
    }
}
