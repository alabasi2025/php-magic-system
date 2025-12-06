@extends('layouts.app')

@section('title', 'إضافة دليل محاسبي جديد')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-plus-circle text-indigo-600"></i>
                إضافة دليل محاسبي جديد
            </h1>
            <p class="text-gray-600">قم بإنشاء دليل محاسبي مبسط حسب طبيعة العمل</p>
        </div>
        <a href="{{ route('chart-of-accounts.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للقائمة
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('chart-of-accounts.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- الوحدة -->
            <div>
                <label for="unit_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-sitemap text-indigo-600 ml-1"></i>
                    الوحدة <span class="text-red-500">*</span>
                </label>
                <select name="unit_id" id="unit_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">-- اختر الوحدة --</option>
                    @foreach(\App\Models\Unit::active()->get() as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
                @error('unit_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- الكود -->
            <div>
                <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-barcode text-indigo-600 ml-1"></i>
                    كود الدليل <span class="text-red-500">*</span>
                </label>
                <input type="text" name="code" id="code" required 
                       placeholder="مثال: EMP, FIN, BUD"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       value="{{ old('code') }}">
                <p class="text-gray-500 text-sm mt-1">كود مختصر باللغة الإنجليزية (3-10 أحرف)</p>
                @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- الاسم -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-tag text-indigo-600 ml-1"></i>
                    اسم الدليل <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" required 
                       placeholder="مثال: دليل أعمال الموظفين"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       value="{{ old('name') }}">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- النوع -->
            <div>
                <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-layer-group text-indigo-600 ml-1"></i>
                    نوع الدليل <span class="text-red-500">*</span>
                </label>
                <select name="type" id="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">-- اختر النوع --</option>
                    @foreach(\App\Models\ChartType::active()->ordered()->get() as $chartType)
                        <option value="{{ $chartType->code }}">{{ $chartType->name }}</option>
                    @endforeach
                </select>
                <p class="text-gray-500 text-sm mt-1">
                    <a href="{{ route('chart-types.index') }}" class="text-indigo-600 hover:underline" target="_blank">
                        <i class="fas fa-cog ml-1"></i>
                        إدارة أنواع الأدلة
                    </a>
                </p>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- الوصف -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-align-right text-indigo-600 ml-1"></i>
                    الوصف
                </label>
                <textarea name="description" id="description" rows="3"
                          placeholder="وصف مختصر لطبيعة هذا الدليل المحاسبي..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- اللون والأيقونة -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- اللون -->
                <div>
                    <label for="color" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-palette text-indigo-600 ml-1"></i>
                        اللون
                    </label>
                    <div class="flex items-center gap-4">
                        <input type="color" name="color" id="color" 
                               value="{{ old('color', '#3B82F6') }}"
                               class="h-12 w-20 border border-gray-300 rounded-lg cursor-pointer">
                        <input type="text" id="color_text" 
                               value="{{ old('color', '#3B82F6') }}"
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               readonly>
                    </div>
                    @error('color')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الأيقونة -->
                <div>
                    <label for="icon" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-icons text-indigo-600 ml-1"></i>
                        الأيقونة (Font Awesome)
                    </label>
                    <div class="flex items-center gap-4">
                        <div id="icon_preview" class="h-12 w-12 bg-gray-100 rounded-lg flex items-center justify-center text-2xl">
                            <i class="fas fa-book"></i>
                        </div>
                        <input type="text" name="icon" id="icon" 
                               placeholder="fa-book"
                               value="{{ old('icon', 'fa-book') }}"
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    <p class="text-gray-500 text-sm mt-1">مثال: fa-book, fa-user-tie, fa-chart-pie</p>
                    @error('icon')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- الحالة -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                       class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="is_active" class="mr-3 text-sm font-medium text-gray-700">
                    تفعيل الدليل
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t">
                <a href="{{ route('chart-of-accounts.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-times ml-2"></i>
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-save ml-2"></i>
                    حفظ الدليل
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// تحديث معاينة اللون
document.getElementById('color').addEventListener('input', function() {
    document.getElementById('color_text').value = this.value;
});

// تحديث معاينة الأيقونة
document.getElementById('icon').addEventListener('input', function() {
    const iconClass = this.value.trim();
    const preview = document.getElementById('icon_preview');
    preview.innerHTML = `<i class="fas ${iconClass}"></i>`;
});
</script>
@endpush
@endsection
