@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-edit text-indigo-600"></i>
                تعديل نوع دليل محاسبي
            </h1>
            <p class="text-gray-600">قم بتعديل بيانات نوع الدليل المحاسبي</p>
        </div>
        <a href="{{ route('chart-types.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للقائمة
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('chart-types.update', $chartType) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- الكود -->
                <div>
                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-barcode text-indigo-600 ml-1"></i>
                        كود النوع <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" required 
                           placeholder="مثال: custom_type"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           value="{{ old('code', $chartType->code) }}">
                    <p class="text-gray-500 text-sm mt-1">كود باللغة الإنجليزية (حروف صغيرة وشرطة سفلية)</p>
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الاسم بالعربية -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag text-indigo-600 ml-1"></i>
                        اسم النوع (عربي) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required 
                           placeholder="مثال: نوع مخصص"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           value="{{ old('name', $chartType->name) }}">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الاسم بالإنجليزية -->
                <div>
                    <label for="name_en" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-language text-indigo-600 ml-1"></i>
                        اسم النوع (إنجليزي)
                    </label>
                    <input type="text" name="name_en" id="name_en" 
                           placeholder="مثال: Custom Type"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           value="{{ old('name_en', $chartType->name_en) }}">
                    @error('name_en')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الأيقونة -->
                <div>
                    <label for="icon" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-icons text-indigo-600 ml-1"></i>
                        الأيقونة (Font Awesome)
                    </label>
                    <input type="text" name="icon" id="icon" 
                           placeholder="مثال: fa-folder"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           value="{{ old('icon', $chartType->icon) }}">
                    <p class="text-gray-500 text-sm mt-1">اسم الأيقونة من Font Awesome</p>
                    @error('icon')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- اللون -->
                <div>
                    <label for="color" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-palette text-indigo-600 ml-1"></i>
                        اللون <span class="text-red-500">*</span>
                    </label>
                    <select name="color" id="color" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="indigo" {{ old('color', $chartType->color) == 'indigo' ? 'selected' : '' }}>نيلي (Indigo)</option>
                        <option value="blue" {{ old('color', $chartType->color) == 'blue' ? 'selected' : '' }}>أزرق (Blue)</option>
                        <option value="green" {{ old('color', $chartType->color) == 'green' ? 'selected' : '' }}>أخضر (Green)</option>
                        <option value="red" {{ old('color', $chartType->color) == 'red' ? 'selected' : '' }}>أحمر (Red)</option>
                        <option value="yellow" {{ old('color', $chartType->color) == 'yellow' ? 'selected' : '' }}>أصفر (Yellow)</option>
                        <option value="purple" {{ old('color', $chartType->color) == 'purple' ? 'selected' : '' }}>بنفسجي (Purple)</option>
                        <option value="pink" {{ old('color', $chartType->color) == 'pink' ? 'selected' : '' }}>وردي (Pink)</option>
                        <option value="orange" {{ old('color', $chartType->color) == 'orange' ? 'selected' : '' }}>برتقالي (Orange)</option>
                        <option value="gray" {{ old('color', $chartType->color) == 'gray' ? 'selected' : '' }}>رمادي (Gray)</option>
                    </select>
                    @error('color')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ترتيب العرض -->
                <div>
                    <label for="sort_order" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sort-numeric-down text-indigo-600 ml-1"></i>
                        ترتيب العرض
                    </label>
                    <input type="number" name="sort_order" id="sort_order" 
                           placeholder="0"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           value="{{ old('sort_order', $chartType->sort_order) }}">
                    <p class="text-gray-500 text-sm mt-1">الرقم الأصغر يظهر أولاً</p>
                    @error('sort_order')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- الوصف -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-align-right text-indigo-600 ml-1"></i>
                    الوصف
                </label>
                <textarea name="description" id="description" rows="4" 
                          placeholder="وصف تفصيلي عن نوع الدليل المحاسبي..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('description', $chartType->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- حالة التفعيل -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                       {{ old('is_active', $chartType->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="mr-3 text-sm font-semibold text-gray-700">
                    <i class="fas fa-toggle-on text-green-600 ml-1"></i>
                    نشط
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 space-x-reverse pt-6 border-t border-gray-200">
                <a href="{{ route('chart-types.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors">
                    <i class="fas fa-times ml-2"></i>
                    إلغاء
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition-colors shadow-lg">
                    <i class="fas fa-save ml-2"></i>
                    تحديث
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
