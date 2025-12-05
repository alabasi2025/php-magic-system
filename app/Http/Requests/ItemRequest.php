<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولاً لإجراء هذا الطلب (الأمان - Authorization).
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // نفترض أن هناك سياسة (Policy) مطبقة للتحقق من الصلاحيات
        // يمكن استخدامها هنا: $this->user()->can('manage-items');
        // أو ببساطة السماح إذا كان المستخدم مسجلاً للدخول
        return true; // يجب تعديل هذا بناءً على منطق الأمان الفعلي
    }

    /**
     * قواعد التحقق التي تنطبق على الطلب.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // تحديد معرف الصنف الحالي إذا كان الطلب تحديثاً
        $itemId = $this->route('item') ? $this->route('item')->id : null;

        return [
            // رمز الصنف: مطلوب، سلسلة نصية، فريد في جدول 'items' باستثناء الصنف الحالي
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('items', 'code')->ignore($itemId),
            ],
            // الباركود: يمكن أن يكون فارغاً، فريد إذا وجد
            'barcode' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('items', 'barcode')->ignore($itemId),
            ],
            // الاسم: مطلوب، سلسلة نصية
            'name' => 'required|string|max:255',
            // الوصف: يمكن أن يكون فارغاً
            'description' => 'nullable|string',
            // الفئة: مطلوب، يجب أن يكون موجوداً في جدول 'categories'
            'category_id' => 'required|exists:categories,id',
            // الوحدة: مطلوب، يجب أن يكون موجوداً في جدول 'units'
            'unit_id' => 'required|exists:units,id',

            // مستويات المخزون: أرقام صحيحة موجبة
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',

            // الأسعار: أرقام عشرية موجبة
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:cost_price', // سعر البيع يجب أن يكون أكبر من أو يساوي سعر التكلفة

            // حالة التفعيل: قيمة منطقية
            'is_active' => 'boolean',

            // الصورة: يمكن أن تكون فارغة، أو ملف صورة (jpeg, png, jpg, gif, svg) بحجم أقصى 2 ميجابايت
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    /**
     * تخصيص أسماء الحقول في رسائل الخطأ.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'code' => 'رمز الصنف',
            'barcode' => 'الباركود',
            'name' => 'اسم الصنف',
            'description' => 'الوصف',
            'category_id' => 'الفئة',
            'unit_id' => 'الوحدة',
            'min_stock' => 'الحد الأدنى للمخزون',
            'max_stock' => 'الحد الأقصى للمخزون',
            'reorder_level' => 'مستوى إعادة الطلب',
            'cost_price' => 'سعر التكلفة',
            'selling_price' => 'سعر البيع',
            'is_active' => 'حالة التفعيل',
            'image' => 'الصورة',
        ];
    }
}
