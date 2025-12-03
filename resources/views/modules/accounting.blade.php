@extends('layouts.app')
@section('title', 'نظام المحاسبة')
@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Hero Header with Gradient -->
    <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 rounded-3xl shadow-2xl p-12 mb-8 text-white overflow-hidden relative">
        <div class="absolute top-0 left-0 w-full h-full opacity-10">
            <i class="fas fa-calculator text-white text-9xl absolute top-10 right-10 transform rotate-12"></i>
            <i class="fas fa-chart-line text-white text-7xl absolute bottom-10 left-10 transform -rotate-12"></i>
        </div>
        <div class="relative z-10">
            <h1 class="text-5xl font-extrabold mb-4 flex items-center">
                <i class="fas fa-calculator mr-4 text-6xl"></i>
                نظام المحاسبة المتكامل
            </h1>
            <p class="text-xl text-purple-100 mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                نظام محاسبة احترافي شامل مع القيود اليومية والتقارير المالية والأدلة المحاسبية
            </p>
            <div class="flex space-x-4 space-x-reverse">
                <a href="{{ route('chart-of-accounts.index') }}" 
                   class="bg-white text-purple-600 hover:bg-purple-50 px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-book mr-2"></i>الأدلة المحاسبية
                </a>
                <a href="{{ route('intermediate-accounts.index') }}" 
                   class="bg-purple-800 hover:bg-purple-900 px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-exchange-alt mr-2"></i>الحسابات الوسيطة
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-xl p-6 text-white transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center justify-between mb-4">
                <i class="fas fa-wallet text-5xl opacity-80"></i>
                <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm font-bold">نشط</span>
            </div>
            <h3 class="text-3xl font-extrabold mb-2">0</h3>
            <p class="text-green-100 font-semibold">إجمالي الصناديق</p>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-xl p-6 text-white transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center justify-between mb-4">
                <i class="fas fa-book text-5xl opacity-80"></i>
                <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm font-bold">فعال</span>
            </div>
            <h3 class="text-3xl font-extrabold mb-2">0</h3>
            <p class="text-blue-100 font-semibold">الأدلة المحاسبية</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-xl p-6 text-white transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center justify-between mb-4">
                <i class="fas fa-exchange-alt text-5xl opacity-80"></i>
                <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm font-bold">متاح</span>
            </div>
            <h3 class="text-3xl font-extrabold mb-2">0</h3>
            <p class="text-purple-100 font-semibold">الحسابات الوسيطة</p>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl shadow-xl p-6 text-white transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center justify-between mb-4">
                <i class="fas fa-file-invoice text-5xl opacity-80"></i>
                <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm font-bold">اليوم</span>
            </div>
            <h3 class="text-3xl font-extrabold mb-2">0</h3>
            <p class="text-orange-100 font-semibold">القيود اليومية</p>
        </div>
    </div>

    <!-- Main Features Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- القيود اليومية -->
        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border-t-4 border-green-500 transform hover:-translate-y-2">
            <div class="bg-gradient-to-br from-green-100 to-emerald-100 w-16 h-16 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-file-invoice text-green-600 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-3 group-hover:text-green-600 transition-colors">القيود اليومية</h3>
            <p class="text-gray-600 mb-4">إدارة وتسجيل القيود المحاسبية اليومية بشكل دقيق ومنظم</p>
            <button class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-bold transition-colors shadow-md hover:shadow-lg w-full">
                <i class="fas fa-plus mr-2"></i>إضافة قيد جديد
            </button>
        </div>

        <!-- التقارير المالية -->
        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border-t-4 border-purple-500 transform hover:-translate-y-2">
            <div class="bg-gradient-to-br from-purple-100 to-pink-100 w-16 h-16 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-chart-bar text-purple-600 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-3 group-hover:text-purple-600 transition-colors">التقارير المالية</h3>
            <p class="text-gray-600 mb-4">تقارير مالية شاملة ودقيقة لمتابعة الأداء المالي</p>
            <button class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded-lg font-bold transition-colors shadow-md hover:shadow-lg w-full">
                <i class="fas fa-chart-line mr-2"></i>عرض التقارير
            </button>
        </div>

        <!-- الأدلة المحاسبية -->
        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border-t-4 border-indigo-500 transform hover:-translate-y-2">
            <div class="bg-gradient-to-br from-indigo-100 to-blue-100 w-16 h-16 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-book text-indigo-600 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-3 group-hover:text-indigo-600 transition-colors">الأدلة المحاسبية</h3>
            <p class="text-gray-600 mb-4">إدارة وتنظيم الأدلة المحاسبية للوحدات التنظيمية</p>
            <a href="{{ route('chart-of-accounts.index') }}" 
               class="block bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold transition-colors shadow-md hover:shadow-lg text-center">
                <i class="fas fa-folder-open mr-2"></i>إدارة الأدلة
            </a>
        </div>

        <!-- الحسابات الوسيطة -->
        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border-t-4 border-pink-500 transform hover:-translate-y-2">
            <div class="bg-gradient-to-br from-pink-100 to-rose-100 w-16 h-16 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-exchange-alt text-pink-600 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-3 group-hover:text-pink-600 transition-colors">الحسابات الوسيطة</h3>
            <p class="text-gray-600 mb-4">إدارة الحسابات الوسيطة المرتبطة بالصناديق والبنوك</p>
            <a href="{{ route('intermediate-accounts.index') }}" 
               class="block bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg font-bold transition-colors shadow-md hover:shadow-lg text-center">
                <i class="fas fa-list mr-2"></i>عرض الحسابات
            </a>
        </div>

        <!-- الصناديق النقدية -->
        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border-t-4 border-emerald-500 transform hover:-translate-y-2">
            <div class="bg-gradient-to-br from-emerald-100 to-teal-100 w-16 h-16 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-cash-register text-emerald-600 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-3 group-hover:text-emerald-600 transition-colors">الصناديق النقدية</h3>
            <p class="text-gray-600 mb-4">إدارة وتتبع الصناديق النقدية وحركاتها المالية</p>
            <a href="{{ route('cash-boxes.index') }}" 
               class="block bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2 rounded-lg font-bold transition-colors shadow-md hover:shadow-lg text-center">
                <i class="fas fa-wallet mr-2"></i>إدارة الصناديق
            </a>
        </div>

        <!-- ميزان المراجعة -->
        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border-t-4 border-blue-500 transform hover:-translate-y-2">
            <div class="bg-gradient-to-br from-blue-100 to-cyan-100 w-16 h-16 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-balance-scale text-blue-600 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-3 group-hover:text-blue-600 transition-colors">ميزان المراجعة</h3>
            <p class="text-gray-600 mb-4">عرض ميزان المراجعة والتحقق من توازن الحسابات</p>
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-bold transition-colors shadow-md hover:shadow-lg w-full">
                <i class="fas fa-calculator mr-2"></i>عرض الميزان
            </button>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-bolt text-yellow-500 mr-3"></i>
            إجراءات سريعة
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button class="bg-white hover:bg-gray-50 p-4 rounded-xl shadow-md hover:shadow-lg transition-all text-center">
                <i class="fas fa-plus-circle text-green-500 text-3xl mb-2"></i>
                <p class="text-gray-700 font-semibold text-sm">قيد جديد</p>
            </button>
            <button class="bg-white hover:bg-gray-50 p-4 rounded-xl shadow-md hover:shadow-lg transition-all text-center">
                <i class="fas fa-search text-blue-500 text-3xl mb-2"></i>
                <p class="text-gray-700 font-semibold text-sm">بحث في القيود</p>
            </button>
            <button class="bg-white hover:bg-gray-50 p-4 rounded-xl shadow-md hover:shadow-lg transition-all text-center">
                <i class="fas fa-download text-purple-500 text-3xl mb-2"></i>
                <p class="text-gray-700 font-semibold text-sm">تصدير التقارير</p>
            </button>
            <button class="bg-white hover:bg-gray-50 p-4 rounded-xl shadow-md hover:shadow-lg transition-all text-center">
                <i class="fas fa-cog text-gray-500 text-3xl mb-2"></i>
                <p class="text-gray-700 font-semibold text-sm">الإعدادات</p>
            </button>
        </div>
    </div>
</div>
@endsection
