<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    /**
     * جلب جميع الفئات مع الفئات الأب والأبناء.
     *
     * @return Collection<int, Category>
     */
    public function getAllCategories(): Collection
    {
        return Category::with(['parent', 'children'])->get();
    }

    /**
     * جلب الفئات الرئيسية فقط (التي ليس لها أب).
     *
     * @return Collection<int, Category>
     */
    public function getRootCategories(): Collection
    {
        return Category::whereNull('parent_id')->get();
    }

    /**
     * إنشاء فئة جديدة.
     *
     * @param array $data بيانات الفئة
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        // التأكد من أن الفئة الأب موجودة إذا تم تحديدها
        if (isset($data['parent_id']) && $data['parent_id'] !== null) {
            if (!Category::where('id', $data['parent_id'])->exists()) {
                throw new \InvalidArgumentException('الفئة الأب المحددة غير موجودة.');
            }
        }

        return Category::create($data);
    }

    /**
     * تحديث فئة موجودة.
     *
     * @param Category $category نموذج الفئة
     * @param array $data البيانات الجديدة
     * @return Category
     */
    public function updateCategory(Category $category, array $data): Category
    {
        // منع الفئة من أن تكون هي نفسها الفئة الأب
        if (isset($data['parent_id']) && $data['parent_id'] == $category->id) {
            throw new \InvalidArgumentException('لا يمكن أن تكون الفئة هي نفسها الفئة الأب.');
        }

        // منع تعيين فئة فرعية كأب لفئة أعلى في الهيكل الهرمي (لتجنب الحلقات)
        if (isset($data['parent_id']) && $data['parent_id'] !== null) {
            $parentId = $data['parent_id'];
            $current = Category::find($parentId);
            while ($current) {
                if ($current->id === $category->id) {
                    throw new \InvalidArgumentException('لا يمكن تعيين فئة فرعية كأب لفئة أعلى في الهيكل الهرمي.');
                }
                $current = $current->parent;
            }
        }

        $category->update($data);
        return $category;
    }

    /**
     * حذف فئة.
     *
     * @param Category $category نموذج الفئة
     * @return bool|null
     */
    public function deleteCategory(Category $category): ?bool
    {
        // عند الحذف، سيتم تعيين parent_id للفئات الفرعية إلى NULL أو حذفها بالكامل
        // حسب إعداد onDelete('cascade') في الهجرة.
        // في هذه الحالة، onDelete('cascade') سيحذف الفئات الفرعية أيضاً.
        return $category->delete();
    }
}
