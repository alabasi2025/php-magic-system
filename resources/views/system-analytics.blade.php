@extends('layouts.app')

@section('title', 'الإحصائيات والتقارير - SEMOP')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 pt-20 pb-10">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex items-center gap-3">
            <div class="w-1 h-10 bg-gradient-to-b from-purple-400 to-purple-600 rounded"></div>
            <div>
                <h1 class="text-4xl font-bold text-white">
                    <i class="fas fa-chart-bar text-purple-400 mr-3"></i>الإحصائيات والتقارير
                </h1>
                <p class="text-gray-400 mt-2">تحليل شامل لأداء النظام والمستخدمين</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Date Range Filter -->
        <div class="flex gap-4 mb-8">
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-calendar mr-2"></i>آخر 7 أيام
            </button>
            <button class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition border border-white/20">
                <i class="fas fa-calendar mr-2"></i>آخر 30 يوم
            </button>
            <button class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition border border-white/20">
                <i class="fas fa-calendar mr-2"></i>آخر 90 يوم
            </button>
            <button class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition border border-white/20">
                <i class="fas fa-calendar mr-2"></i>مخصص
            </button>
        </div>

        <!-- Key Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <p class="text-gray-400 text-sm mb-2">إجمالي الطلبات</p>
                <p class="text-3xl font-bold text-white">45,234</p>
                <p class="text-green-400 text-sm mt-2">↑ 12% من الأسبوع الماضي</p>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <p class="text-gray-400 text-sm mb-2">متوسط وقت الاستجابة</p>
                <p class="text-3xl font-bold text-white">45ms</p>
                <p class="text-green-400 text-sm mt-2">↓ 5% من الأسبوع الماضي</p>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <p class="text-gray-400 text-sm mb-2">معدل الأخطاء</p>
                <p class="text-3xl font-bold text-white">0.2%</p>
                <p class="text-green-400 text-sm mt-2">↓ 0.1% من الأسبوع الماضي</p>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <p class="text-gray-400 text-sm mb-2">المستخدمون النشطون</p>
                <p class="text-3xl font-bold text-white">1,234</p>
                <p class="text-green-400 text-sm mt-2">↑ 8% من الأسبوع الماضي</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Traffic Chart -->
            <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-6">
                <h3 class="text-xl font-bold text-white mb-4">
                    <i class="fas fa-chart-line text-blue-400 mr-2"></i>حركة المستخدمين
                </h3>
                <div class="bg-black/30 rounded-lg p-4 h-64 flex items-end justify-around gap-1">
                    <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-1/3 opacity-70"></div>
                    <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-2/5 opacity-70"></div>
                    <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-1/2 opacity-70"></div>
                    <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-3/5 opacity-70"></div>
                    <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-2/3 opacity-70"></div>
                    <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-3/4 opacity-70"></div>
                    <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-4/5 opacity-70"></div>
                </div>
            </div>

            <!-- Error Distribution -->
            <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-6">
                <h3 class="text-xl font-bold text-white mb-4">
                    <i class="fas fa-chart-pie text-purple-400 mr-2"></i>توزيع الأخطاء
                </h3>
                <div class="flex items-center justify-center h-64">
                    <div class="relative w-48 h-48">
                        <svg viewBox="0 0 100 100" class="w-full h-full">
                            <circle cx="50" cy="50" r="45" fill="none" stroke="#3b82f6" stroke-width="15" stroke-dasharray="70 100" stroke-dashoffset="0"/>
                            <circle cx="50" cy="50" r="45" fill="none" stroke="#ef4444" stroke-width="15" stroke-dasharray="20 100" stroke-dashoffset="-70"/>
                            <circle cx="50" cy="50" r="45" fill="none" stroke="#f59e0b" stroke-width="15" stroke-dasharray="10 100" stroke-dashoffset="-90"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-white font-bold text-2xl">0.2%</p>
                                <p class="text-gray-400 text-xs">معدل الأخطاء</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">404 Errors</span>
                        <span class="text-blue-400">70%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">500 Errors</span>
                        <span class="text-red-400">20%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">Other Errors</span>
                        <span class="text-yellow-400">10%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Pages -->
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-6 mb-8">
            <h3 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-list text-green-400 mr-2"></i>أكثر الصفحات زيارة
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="text-gray-400 font-semibold">1</span>
                        <span class="text-white">/dashboard</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-32 bg-black/30 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 100%"></div>
                        </div>
                        <span class="text-gray-400 text-sm w-16 text-right">12,345</span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="text-gray-400 font-semibold">2</span>
                        <span class="text-white">/products</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-32 bg-black/30 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 85%"></div>
                        </div>
                        <span class="text-gray-400 text-sm w-16 text-right">10,493</span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="text-gray-400 font-semibold">3</span>
                        <span class="text-white">/sales</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-32 bg-black/30 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 72%"></div>
                        </div>
                        <span class="text-gray-400 text-sm w-16 text-right">8,893</span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="text-gray-400 font-semibold">4</span>
                        <span class="text-white">/reports</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-32 bg-black/30 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 65%"></div>
                        </div>
                        <span class="text-gray-400 text-sm w-16 text-right">8,034</span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="text-gray-400 font-semibold">5</span>
                        <span class="text-white">/settings</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-32 bg-black/30 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 52%"></div>
                        </div>
                        <span class="text-gray-400 text-sm w-16 text-right">6,423</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="flex gap-3">
            <button class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition flex items-center gap-2">
                <i class="fas fa-download"></i>تحميل PDF
            </button>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center gap-2">
                <i class="fas fa-file-excel"></i>تحميل Excel
            </button>
            <button class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition flex items-center gap-2">
                <i class="fas fa-envelope"></i>إرسال بالبريد
            </button>
        </div>
    </div>
</div>

@endsection
