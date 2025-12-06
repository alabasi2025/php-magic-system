@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">
            <i class="fas fa-home text-blue-500 mr-3"></i>
            مرحباً بك في نظام PHP Magic System
        </h1>
        <p class="text-gray-600 text-lg">
            نظام إدارة مالي متكامل - الإصدار <span class="font-bold text-blue-600">{{ config('version.number', '4.1.0') }}</span>
        </p>
    </div>

    <!-- System Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- PHP Version -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm mb-1">PHP Version</p>
                    <h3 class="text-3xl font-bold">{{ PHP_VERSION }}</h3>
                </div>
                <div class="text-5xl opacity-30">
                    <i class="fab fa-php"></i>
                </div>
            </div>
        </div>

        <!-- Laravel Version -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm mb-1">Laravel</p>
                    <h3 class="text-3xl font-bold">{{ app()->version() }}</h3>
                </div>
                <div class="text-5xl opacity-30">
                    <i class="fab fa-laravel"></i>
                </div>
            </div>
        </div>

        <!-- System Version -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm mb-1">إصدار النظام</p>
                    <h3 class="text-3xl font-bold">{{ config('version.number', '4.1.0') }}</h3>
                </div>
                <div class="text-5xl opacity-30">
                    <i class="fas fa-code-branch"></i>
                </div>
            </div>
        </div>

        <!-- Environment -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm mb-1">البيئة</p>
                    <h3 class="text-2xl font-bold">{{ ucfirst(config('app.env')) }}</h3>
                </div>
                <div class="text-5xl opacity-30">
                    <i class="fas fa-server"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
            الوصول السريع
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- القيود المحاسبية -->
            <a href="{{ route('journal-entries.index') }}" class="group bg-gradient-to-br from-indigo-50 to-indigo-100 hover:from-indigo-100 hover:to-indigo-200 rounded-lg p-6 transition-all duration-300 border-2 border-transparent hover:border-indigo-300">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="bg-indigo-500 text-white rounded-lg p-3">
                        <i class="fas fa-book text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 group-hover:text-indigo-600">القيود المحاسبية</h3>
                        <p class="text-sm text-gray-600">إدارة القيود اليومية</p>
                    </div>
                </div>
            </a>

            <!-- الدليل المحاسبي -->
            <a href="{{ route('chart-of-accounts.index') }}" class="group bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 rounded-lg p-6 transition-all duration-300 border-2 border-transparent hover:border-purple-300">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="bg-purple-500 text-white rounded-lg p-3">
                        <i class="fas fa-list-alt text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 group-hover:text-purple-600">الدليل المحاسبي</h3>
                        <p class="text-sm text-gray-600">شجرة الحسابات</p>
                    </div>
                </div>
            </a>

            <!-- الحسابات البنكية -->
            <a href="{{ route('bank-accounts.index') }}" class="group bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 rounded-lg p-6 transition-all duration-300 border-2 border-transparent hover:border-blue-300">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="bg-blue-500 text-white rounded-lg p-3">
                        <i class="fas fa-university text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 group-hover:text-blue-600">الحسابات البنكية</h3>
                        <p class="text-sm text-gray-600">إدارة البنوك</p>
                    </div>
                </div>
            </a>

            <!-- الصناديق النقدية -->
            <a href="{{ route('cash-boxes.index') }}" class="group bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 rounded-lg p-6 transition-all duration-300 border-2 border-transparent hover:border-green-300">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="bg-green-500 text-white rounded-lg p-3">
                        <i class="fas fa-cash-register text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 group-hover:text-green-600">الصناديق النقدية</h3>
                        <p class="text-sm text-gray-600">إدارة الصناديق</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Features Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- النظام المالي -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-chart-line text-green-500 mr-2"></i>
                النظام المالي
            </h2>
            <ul class="space-y-3">
                <li class="flex items-center text-gray-700">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    نظام محاسبي متكامل
                </li>
                <li class="flex items-center text-gray-700">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    إدارة البنوك والصناديق
                </li>
                <li class="flex items-center text-gray-700">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    سندات القبض والصرف
                </li>
                <li class="flex items-center text-gray-700">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    قوالب القيود الذكية
                </li>
            </ul>
        </div>

        <!-- أدوات التطوير -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-code text-blue-500 mr-2"></i>
                أدوات التطوير
            </h2>
            <ul class="space-y-3">
                <li class="flex items-center text-gray-700">
                    <i class="fas fa-check-circle text-blue-500 mr-3"></i>
                    لوحة تحكم المطورين
                </li>
                <li class="flex items-center text-gray-700">
                    <i class="fas fa-check-circle text-blue-500 mr-3"></i>
                    معلومات النظام والخادم
                </li>
                <li class="flex items-center text-gray-700">
                    <i class="fas fa-check-circle text-blue-500 mr-3"></i>
                    عارض السجلات (Logs)
                </li>
                <li class="flex items-center text-gray-700">
                    <i class="fas fa-check-circle text-blue-500 mr-3"></i>
                    إدارة قاعدة البيانات
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
