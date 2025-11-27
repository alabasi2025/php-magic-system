@extends('layouts.app')

@section('title', 'لوحة التحكم - SEMOP')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold">لوحة التحكم</h1>
                <p class="text-blue-100 mt-2 text-lg">مرحباً بك في نظام SEMOP لإدارة المؤسسات - الإصدار 1.0.0</p>
            </div>
            <div class="text-left bg-white/10 backdrop-blur-sm rounded-lg p-4">
                <p class="text-sm text-blue-100">التاريخ</p>
                <p class="text-2xl font-bold">{{ date('Y-m-d') }}</p>
                <p class="text-sm text-blue-100">{{ date('H:i') }}</p>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
        <!-- Users Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">المستخدمين</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['users'] ?? 0 }}</p>
                    <p class="text-xs text-blue-100 mt-1">+12% هذا الشهر</p>
                </div>
                <div class="bg-white/20 p-4 rounded-full">
                    <i class="fas fa-users text-3xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Customers Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">العملاء</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['customers'] ?? 0 }}</p>
                    <p class="text-xs text-green-100 mt-1">+8% هذا الشهر</p>
                </div>
                <div class="bg-white/20 p-4 rounded-full">
                    <i class="fas fa-user-friends text-3xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Suppliers Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">الموردين</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['suppliers'] ?? 0 }}</p>
                    <p class="text-xs text-orange-100 mt-1">+5% هذا الشهر</p>
                </div>
                <div class="bg-white/20 p-4 rounded-full">
                    <i class="fas fa-truck text-3xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Items Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">الأصناف</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['items'] ?? 0 }}</p>
                    <p class="text-xs text-purple-100 mt-1">+15% هذا الشهر</p>
                </div>
                <div class="bg-white/20 p-4 rounded-full">
                    <i class="fas fa-box text-3xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Projects Card -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm">المشاريع</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['projects'] ?? 0 }}</p>
                    <p class="text-xs text-indigo-100 mt-1">+3% هذا الشهر</p>
                </div>
                <div class="bg-white/20 p-4 rounded-full">
                    <i class="fas fa-briefcase text-3xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Tasks Card -->
        <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-pink-100 text-sm">المهام</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['tasks'] ?? 0 }}</p>
                    <p class="text-xs text-pink-100 mt-1">+20% هذا الشهر</p>
                </div>
                <div class="bg-white/20 p-4 rounded-full">
                    <i class="fas fa-tasks text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-line text-blue-500 ml-2"></i>
                مبيعات آخر 7 أيام
            </h3>
            <div class="h-64 flex items-end justify-between space-x-2 space-x-reverse">
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t-lg hover:from-blue-600 hover:to-blue-500 transition-all duration-200" style="height: 60%;" title="السبت: 45,000 ريال"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t-lg hover:from-blue-600 hover:to-blue-500 transition-all duration-200" style="height: 75%;" title="الأحد: 60,000 ريال"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t-lg hover:from-blue-600 hover:to-blue-500 transition-all duration-200" style="height: 50.75%;" title="الاثنين: 38,000 ريال"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t-lg hover:from-blue-600 hover:to-blue-500 transition-all duration-200" style="height: 85%;" title="الثلاثاء: 72,000 ريال"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t-lg hover:from-blue-600 hover:to-blue-500 transition-all duration-200" style="height: 65%;" title="الأربعاء: 52,000 ريال"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t-lg hover:from-blue-600 hover:to-blue-500 transition-all duration-200" style="height: 90%;" title="الخميس: 78,000 ريال"></div>
                <div class="flex-1 bg-gradient-to-t from-green-500 to-green-400 rounded-t-lg hover:from-green-600 hover:to-green-500 transition-all duration-200" style="height: 100%;" title="الجمعة: 85,000 ريال"></div>
            </div>
            <div class="flex justify-between mt-4 text-sm text-gray-600">
                <span>السبت</span>
                <span>الأحد</span>
                <span>الاثنين</span>
                <span>الثلاثاء</span>
                <span>الأربعاء</span>
                <span>الخميس</span>
                <span class="font-bold text-green-600">الجمعة</span>
            </div>
        </div>

        <!-- Tasks Progress Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-tasks text-purple-500 ml-2"></i>
                حالة المهام
            </h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700">مكتملة</span>
                        <span class="font-bold text-green-600">65%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-500" style="width: 65%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700">قيد التنفيذ</span>
                        <span class="font-bold text-blue-600">25%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500" style="width: 25%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700">متأخرة</span>
                        <span class="font-bold text-red-600">10%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-red-500 to-red-600 h-3 rounded-full transition-all duration-500" style="width: 10%"></div>
                    </div>
                </div>
            </div>
            <div class="mt-6 grid grid-cols-3 gap-4">
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">130</p>
                    <p class="text-xs text-gray-600">مكتملة</p>
                </div>
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">50</p>
                    <p class="text-xs text-gray-600">قيد التنفيذ</p>
                </div>
                <div class="text-center p-3 bg-red-50 rounded-lg">
                    <p class="text-2xl font-bold text-red-600">20</p>
                    <p class="text-xs text-gray-600">متأخرة</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Systems Grid -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-th-large text-blue-500 ml-3"></i>
            أنظمة SEMOP
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($modules as $module)
            <a href="#" class="group block">
                <div class="bg-gradient-to-br from-{{ $module['color'] }}-50 to-{{ $module['color'] }}-100 border-2 border-{{ $module['color'] }}-200 rounded-lg p-6 hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-{{ $module['color'] }}-500 p-3 rounded-lg group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-{{ $module['icon'] }} text-white text-2xl"></i>
                        </div>
                        <i class="fas fa-arrow-left text-{{ $module['color'] }}-400 group-hover:text-{{ $module['color'] }}-600 transition-colors"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $module['name'] }}</h3>
                    <p class="text-sm text-gray-600">{{ $module['description'] }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Recent Activities & System Info Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Activities -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-history text-blue-500 ml-2"></i>
                النشاطات الأخيرة
            </h3>
            <div class="space-y-4">
                <div class="flex items-start space-x-4 space-x-reverse p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <div class="bg-blue-500 p-2 rounded-full">
                        <i class="fas fa-user-plus text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">إضافة عميل جديد</p>
                        <p class="text-sm text-gray-600">تم إضافة العميل "شركة النور للتجارة"</p>
                        <p class="text-xs text-gray-500 mt-1">منذ 5 دقائق</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-4 space-x-reverse p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <div class="bg-green-500 p-2 rounded-full">
                        <i class="fas fa-file-invoice text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">إصدار فاتورة مبيعات</p>
                        <p class="text-sm text-gray-600">فاتورة رقم #1234 بقيمة 15,000 ريال</p>
                        <p class="text-xs text-gray-500 mt-1">منذ 15 دقيقة</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-4 space-x-reverse p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                    <div class="bg-orange-500 p-2 rounded-full">
                        <i class="fas fa-box text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">تحديث المخزون</p>
                        <p class="text-sm text-gray-600">تم استلام 50 صنف جديد</p>
                        <p class="text-xs text-gray-500 mt-1">منذ 30 دقيقة</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4 space-x-reverse p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <div class="bg-purple-500 p-2 rounded-full">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">إنجاز مهمة</p>
                        <p class="text-sm text-gray-600">تم إنجاز مهمة "مراجعة الحسابات"</p>
                        <p class="text-xs text-gray-500 mt-1">منذ ساعة</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Info -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-info-circle text-blue-500 ml-2"></i>
                معلومات النظام
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg">
                    <span class="text-gray-700 font-medium">إصدار النظام</span>
                    <span class="font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">v1.1.0</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-700 font-medium">Laravel</span>
                    <span class="font-bold text-gray-800">12.40.2</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-700 font-medium">قاعدة البيانات</span>
                    <span class="font-bold text-gray-800">MySQL</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                    <span class="text-gray-700 font-medium">حالة النظام</span>
                    <span class="flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full ml-2 animate-pulse"></span>
                        <span class="font-bold text-green-600">يعمل بشكل طبيعي</span>
                    </span>
                </div>

                <div class="p-4 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg text-white">
                    <p class="text-sm mb-2">إجمالي الملفات المنشأة</p>
                    <p class="text-3xl font-bold">497</p>
                    <p class="text-xs mt-1 text-blue-100">125 Services • 102 Controllers • 81 Models</p>
                </div>

                <div class="p-4 bg-gradient-to-r from-green-500 to-teal-500 rounded-lg text-white">
                    <p class="text-sm mb-2">التقدم الإجمالي</p>
                    <div class="flex items-center justify-between">
                        <p class="text-3xl font-bold">50.75%</p>
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <p class="text-xs mt-1 text-green-100">1015 من 2000 مهمة</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.card-hover {
    animation: fadeIn 0.5s ease-out;
}
</style>
@endsection
