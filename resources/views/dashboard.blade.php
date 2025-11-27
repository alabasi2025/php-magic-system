@extends('layouts.app')

@section('title', 'لوحة التحكم - SEMOP')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">لوحة التحكم</h1>
                <p class="text-gray-600 mt-2">مرحباً بك في نظام SEMOP لإدارة المؤسسات</p>
            </div>
            <div class="text-left">
                <p class="text-sm text-gray-500">التاريخ</p>
                <p class="text-lg font-semibold text-gray-800">{{ date('Y-m-d') }}</p>
                <p class="text-sm text-gray-500">{{ date('H:i') }}</p>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
        <!-- Users Card -->
        <div class="bg-white rounded-lg shadow-md p-6 card-hover border-r-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">المستخدمين</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['users'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-4 rounded-full">
                    <i class="fas fa-users text-blue-500 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Customers Card -->
        <div class="bg-white rounded-lg shadow-md p-6 card-hover border-r-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">العملاء</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['customers'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-4 rounded-full">
                    <i class="fas fa-user-friends text-green-500 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Suppliers Card -->
        <div class="bg-white rounded-lg shadow-md p-6 card-hover border-r-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">الموردين</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['suppliers'] ?? 0 }}</p>
                </div>
                <div class="bg-orange-100 p-4 rounded-full">
                    <i class="fas fa-truck text-orange-500 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Items Card -->
        <div class="bg-white rounded-lg shadow-md p-6 card-hover border-r-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">الأصناف</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['items'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-full">
                    <i class="fas fa-box text-purple-500 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Projects Card -->
        <div class="bg-white rounded-lg shadow-md p-6 card-hover border-r-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">المشاريع</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['projects'] ?? 0 }}</p>
                </div>
                <div class="bg-indigo-100 p-4 rounded-full">
                    <i class="fas fa-briefcase text-indigo-500 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Tasks Card -->
        <div class="bg-white rounded-lg shadow-md p-6 card-hover border-r-4 border-pink-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">المهام</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['tasks'] ?? 0 }}</p>
                </div>
                <div class="bg-pink-100 p-4 rounded-full">
                    <i class="fas fa-tasks text-pink-500 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- System Modules -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">أنظمة SEMOP</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($modules as $module)
            <a href="{{ route($module['route']) }}" class="block">
                <div class="bg-gradient-to-br from-{{ $module['color'] }}-50 to-{{ $module['color'] }}-100 rounded-lg p-6 card-hover border-2 border-{{ $module['color'] }}-200 hover:border-{{ $module['color'] }}-400">
                    <div class="flex items-center space-x-4 space-x-reverse mb-4">
                        <div class="bg-{{ $module['color'] }}-500 text-white p-3 rounded-lg">
                            <i class="fas fa-{{ $module['icon'] }} text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">{{ $module['name'] }}</h3>
                    </div>
                    <p class="text-gray-600 text-sm">{{ $module['description'] }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">النشاطات الأخيرة</h3>
            <div class="space-y-4">
                <div class="flex items-center space-x-4 space-x-reverse p-4 bg-gray-50 rounded-lg">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-user-plus text-blue-500"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">إضافة عميل جديد</p>
                        <p class="text-sm text-gray-500">منذ 5 دقائق</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 space-x-reverse p-4 bg-gray-50 rounded-lg">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-file-invoice text-green-500"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">إصدار فاتورة مبيعات</p>
                        <p class="text-sm text-gray-500">منذ 15 دقيقة</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 space-x-reverse p-4 bg-gray-50 rounded-lg">
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-box text-orange-500"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">تحديث المخزون</p>
                        <p class="text-sm text-gray-500">منذ 30 دقيقة</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">معلومات النظام</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-600">إصدار النظام</span>
                    <span class="font-bold text-gray-800">v0.2.0</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-600">Laravel</span>
                    <span class="font-bold text-gray-800">12.40.2</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-600">قاعدة البيانات</span>
                    <span class="font-bold text-gray-800">MySQL</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg border-2 border-green-200">
                    <span class="text-gray-600">حالة النظام</span>
                    <span class="font-bold text-green-600 flex items-center">
                        <i class="fas fa-check-circle ml-2"></i>
                        يعمل بشكل طبيعي
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
