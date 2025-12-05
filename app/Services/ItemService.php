<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ItemService
{
    /**
     * جلب جميع الأصناف مع علاقاتها (الفئة والوحدة).
     *
     * @return Collection
     */
    public function getAllItems(): Collection
    {
        // استخدام with لتحميل العلاقات مسبقاً لتجنب مشكلة N+1
        return Item::with(['category', 'unit'])->get();
    }

    /**
     * إنشاء صنف جديد.
     *
     * @param array $data بيانات الصنف
     * @return Item
     * @throws \Exception
     */
    public function createItem(array $data): Item
    {
        // بدء معاملة قاعدة البيانات لضمان سلامة البيانات
        DB::beginTransaction();
        try {
            // معالجة رفع الصورة إذا كانت موجودة
            if (isset($data['image'])) {
                $data['image'] = $this->uploadImage($data['image']);
            }

            $item = Item::create($data);

            DB::commit();
            return $item;
        } catch (\Exception $e) {
            DB::rollBack();
            // يمكن تسجيل الخطأ هنا
            throw new \Exception('فشل في إنشاء الصنف: ' . $e->getMessage());
        }
    }

    /**
     * تحديث صنف موجود.
     *
     * @param Item $item نموذج الصنف المراد تحديثه
     * @param array $data بيانات التحديث
     * @return Item
     * @throws \Exception
     */
    public function updateItem(Item $item, array $data): Item
    {
        DB::beginTransaction();
        try {
            // معالجة الصورة: إذا تم رفع صورة جديدة، يتم حذف القديمة ورفع الجديدة
            if (isset($data['image'])) {
                // حذف الصورة القديمة إذا كانت موجودة
                if ($item->image) {
                    $this->deleteImage($item->image);
                }
                $data['image'] = $this->uploadImage($data['image']);
            } else {
                // إذا لم يتم رفع صورة جديدة، نحافظ على الصورة القديمة
                unset($data['image']);
            }

            $item->update($data);

            DB::commit();
            return $item;
        } catch (\Exception $e) {
            DB::rollBack();
            // يمكن تسجيل الخطأ هنا
            throw new \Exception('فشل في تحديث الصنف: ' . $e->getMessage());
        }
    }

    /**
     * حذف صنف.
     *
     * @param Item $item نموذج الصنف المراد حذفه
     * @return bool
     * @throws \Exception
     */
    public function deleteItem(Item $item): bool
    {
        DB::beginTransaction();
        try {
            // حذف الصورة المرتبطة بالصنف
            if ($item->image) {
                $this->deleteImage($item->image);
            }

            $result = $item->delete();

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            // يمكن تسجيل الخطأ هنا
            throw new \Exception('فشل في حذف الصنف: ' . $e->getMessage());
        }
    }

    /**
     * رفع ملف الصورة وتخزينه.
     *
     * @param \Illuminate\Http\UploadedFile $file ملف الصورة
     * @return string مسار الصورة المخزنة
     */
    protected function uploadImage($file): string
    {
        // تخزين الملف في مجلد 'items_images' داخل 'public'
        return $file->store('items_images', 'public');
    }

    /**
     * حذف ملف الصورة من التخزين.
     *
     * @param string $path مسار الصورة
     * @return bool
     */
    protected function deleteImage(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }
}
