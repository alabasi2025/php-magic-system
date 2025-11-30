@extends('layouts.app')
@section('title', 'التقارير')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">تقارير محاسبة الشراكات</h1>

        <!-- أنواع التقارير -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- تقرير الإيرادات -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
                <div class="flex items-center mb-4">
                    <div class="bg-green-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mr-3">تقرير الإيرادات</h3>
                </div>
                <p class="text-gray-600 mb-4">تقرير شامل لجميع الإيرادات حسب الفترة والمحطة</p>
                <button onclick="alert('تقرير الإيرادات')" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    عرض التقرير
                </button>
            </div>

            <!-- تقرير المصروفات -->
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-6 border border-red-200">
                <div class="flex items-center mb-4">
                    <div class="bg-red-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mr-3">تقرير المصروفات</h3>
                </div>
                <p class="text-gray-600 mb-4">تقرير مفصل للمصروفات حسب النوع والفترة</p>
                <button onclick="alert('تقرير المصروفات')" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                    عرض التقرير
                </button>
            </div>

            <!-- تقرير الأرباح -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 border border-purple-200">
                <div class="flex items-center mb-4">
                    <div class="bg-purple-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mr-3">تقرير الأرباح</h3>
                </div>
                <p class="text-gray-600 mb-4">تقرير الأرباح وتوزيعها على الشركاء</p>
                <button onclick="alert('تقرير الأرباح')" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                    عرض التقرير
                </button>
            </div>

            <!-- تقرير الشركاء -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mr-3">تقرير الشركاء</h3>
                </div>
                <p class="text-gray-600 mb-4">كشف حساب شامل لكل شريك</p>
                <button onclick="alert('تقرير الشركاء')" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    عرض التقرير
                </button>
            </div>

            <!-- ملخص الشراكة -->
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-6 border border-indigo-200">
                <div class="flex items-center mb-4">
                    <div class="bg-indigo-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mr-3">ملخص الشراكة</h3>
                </div>
                <p class="text-gray-600 mb-4">ملخص شامل للشراكة في محطة معينة</p>
                <button onclick="alert('ملخص الشراكة')" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                    عرض التقرير
                </button>
            </div>

            <!-- مقارنة المحطات -->
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-6 border border-orange-200">
                <div class="flex items-center mb-4">
                    <div class="bg-orange-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mr-3">مقارنة المحطات</h3>
                </div>
                <p class="text-gray-600 mb-4">مقارنة الأداء المالي بين المحطات</p>
                <button onclick="alert('مقارنة المحطات')" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">
                    عرض التقرير
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
