@extends('layouts.app')

@section('title', 'إدارة الميزات - SEMOP')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 space-x-reverse">
                <div class="bg-white/20 p-4 rounded-lg">
                    <i class="fas fa-puzzle-piece text-4xl"></i>
                </div>
                <div>
                    <h1 class="text-4xl font-bold">إدارة الميزات</h1>
                    <p class="text-purple-100 mt-2">تفعيل وإدارة ميزات النظام المتقدمة</p>
                </div>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <button class="bg-white text-purple-600 px-6 py-3 rounded-lg font-bold hover:bg-purple-50 transition-colors">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة ميزة
                </button>
                <button class="bg-green-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-green-600 transition-colors">
                    <i class="fas fa-check-double ml-2"></i>
                    تفعيل الكل
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">إجمالي الميزات</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">264</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-puzzle-piece text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">مفعّلة</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">198</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">معطّلة</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">66</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">نسبة التفعيل</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">75%</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-percentage text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
        $categories = [
            ['name' => 'الأساسية', 'count' => 45, 'enabled' => 42, 'color' => 'blue', 'icon' => 'star'],
            ['name' => 'المحاسبة', 'count' => 38, 'enabled' => 35, 'color' => 'green', 'icon' => 'calculator'],
            ['name' => 'المخزون', 'count' => 32, 'enabled' => 28, 'color' => 'orange', 'icon' => 'warehouse'],
            ['name' => 'الموارد البشرية', 'count' => 28, 'enabled' => 25, 'color' => 'teal', 'icon' => 'users'],
            ['name' => 'المبيعات', 'count' => 35, 'enabled' => 30, 'color' => 'purple', 'icon' => 'shopping-cart'],
            ['name' => 'التقارير', 'count' => 42, 'enabled' => 38, 'color' => 'indigo', 'icon' => 'chart-bar'],
            ['name' => 'الأمان', 'count' => 22, 'enabled' => 22, 'color' => 'red', 'icon' => 'shield-alt'],
            ['name' => 'التكامل', 'count' => 18, 'enabled' => 15, 'color' => 'pink', 'icon' => 'plug'],
            ['name' => 'متقدمة', 'count' => 24, 'enabled' => 18, 'color' => 'gray', 'icon' => 'cogs'],
        ];
        @endphp

        @foreach($categories as $category)
        <div class="bg-gradient-to-br from-{{ $category['color'] }}-500 to-{{ $category['color'] }}-600 rounded-lg shadow-lg p-6 text-white hover:scale-105 transition-transform cursor-pointer">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-{{ $category['icon'] }} text-2xl"></i>
                </div>
                <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-bold">
                    {{ $category['count'] }} ميزة
                </span>
            </div>
            <h3 class="text-2xl font-bold mb-2">{{ $category['name'] }}</h3>
            <div class="flex items-center justify-between">
                <span class="text-sm">{{ $category['enabled'] }} مفعّلة</span>
                <span class="text-sm">{{ round(($category['enabled']/$category['count'])*100) }}%</span>
            </div>
            <div class="mt-3 bg-white/20 rounded-full h-2">
                <div class="bg-white rounded-full h-2" style="width: {{ round(($category['enabled']/$category['count'])*100) }}%"></div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Features List -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">قائمة الميزات</h2>
                <div class="flex items-center space-x-2 space-x-reverse">
                    <input type="text" placeholder="بحث..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option>جميع الفئات</option>
                        <option>الأساسية</option>
                        <option>المحاسبة</option>
                        <option>المخزون</option>
                    </select>
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option>الكل</option>
                        <option>مفعّلة</option>
                        <option>معطّلة</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-purple-600 text-white">
                    <tr>
                        <th class="px-6 py-4 text-right">#</th>
                        <th class="px-6 py-4 text-right">اسم الميزة</th>
                        <th class="px-6 py-4 text-right">الفئة</th>
                        <th class="px-6 py-4 text-right">الإصدار</th>
                        <th class="px-6 py-4 text-right">الحالة</th>
                        <th class="px-6 py-4 text-right">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @for($i = 1; $i <= 15; $i++)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">{{ $i }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <i class="fas fa-puzzle-piece text-purple-600"></i>
                                <span class="font-semibold">Feature {{ str_pad($i, 3, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                الأساسية
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">v1.0.0</td>
                        <td class="px-6 py-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" {{ $i % 3 != 0 ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <button class="text-blue-600 hover:text-blue-800 transition-colors" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-800 transition-colors" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-purple-600 hover:text-purple-800 transition-colors" title="إعدادات">
                                    <i class="fas fa-cog"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t">
            <div class="text-sm text-gray-600">
                عرض 1 إلى 15 من 264 ميزة
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    السابق
                </button>
                <button class="px-4 py-2 bg-purple-600 text-white rounded-lg">1</button>
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">2</button>
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">3</button>
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    التالي
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
