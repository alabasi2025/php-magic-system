<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\CategoryRequest;
use App\Models\Inventory\Category;
use App\Services\Inventory\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        // تطبيق سياسة الأمان (Authorization)
        $this->authorizeResource(Category::class, 'category');
    }

    /**
     * عرض قائمة بجميع الفئات.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $categories = $this->categoryService->getAllCategories();
        return view('inventory.categories.index', compact('categories'));
    }

    /**
     * عرض نموذج إنشاء فئة جديدة.
     *
     * @return View
     */
    public function create(): View
    {
        $parentCategories = $this->categoryService->getRootCategories();
        return view('inventory.categories.create', compact('parentCategories'));
    }

    /**
     * تخزين فئة جديدة في قاعدة البيانات.
     *
     * @param CategoryRequest $request
     * @return RedirectResponse
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        try {
            $this->categoryService->createCategory($request->validated());
            return redirect()->route('inventory.categories.index')
                             ->with('success', 'تم إنشاء الفئة بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->withInput()->with('error', 'فشل إنشاء الفئة: ' . $e->getMessage());
        }
    }

    /**
     * عرض فئة محددة.
     *
     * @param Category $category
     * @return View
     */
    public function show(Category $category): View
    {
        // تحميل الفئة الأب والأبناء
        $category->load(['parent', 'children']);
        return view('inventory.categories.show', compact('category'));
    }

    /**
     * عرض نموذج تعديل فئة موجودة.
     *
     * @param Category $category
     * @return View
     */
    public function edit(Category $category): View
    {
        $parentCategories = $this->categoryService->getAllCategories()->except($category->id);
        return view('inventory.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * تحديث فئة موجودة في قاعدة البيانات.
     *
     * @param CategoryRequest $request
     * @param Category $category
     * @return RedirectResponse
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        try {
            $this->categoryService->updateCategory($category, $request->validated());
            return redirect()->route('inventory.categories.index')
                             ->with('success', 'تم تحديث الفئة بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->withInput()->with('error', 'فشل تحديث الفئة: ' . $e->getMessage());
        }
    }

    /**
     * حذف فئة من قاعدة البيانات.
     *
     * @param Category $category
     * @return RedirectResponse
     */
    public function destroy(Category $category): RedirectResponse
    {
        try {
            $this->categoryService->deleteCategory($category);
            return redirect()->route('inventory.categories.index')
                             ->with('success', 'تم حذف الفئة بنجاح.');
        } catch (\Exception $e) {
            // معالجة الأخطاء
            return back()->with('error', 'فشل حذف الفئة: ' . $e->getMessage());
        }
    }
}
