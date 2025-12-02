@extends('layouts.app')

@section('title', 'إدارة Cache - نظام المطور')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-orange-900 to-slate-900 pt-20 pb-10">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-memory text-orange-400 mr-3"></i>إدارة الذاكرة المؤقتة (Cache)
                </h1>
                <p class="text-gray-400">مسح وإدارة جميع أنواع الذاكرة المؤقتة</p>
            </div>
            <a href="{{ route('developer.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <i class="fas fa-arrow-right mr-2"></i>العودة
            </a>
        </div>
    </div>

    <!-- Cache Driver Info -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">محرك Cache الحالي</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $cache_driver }}</p>
                </div>
                <i class="fas fa-server text-orange-400 text-5xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Clear Actions -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <h2 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-broom text-orange-400 mr-2"></i>إجراءات المسح
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <form action="{{ route('developer.cache.clear-all') }}" method="POST" onsubmit="return confirm('هل تريد مسح جميع أنواع Cache؟')">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition">
                        <i class="fas fa-trash-alt mr-2"></i>مسح جميع Cache
                    </button>
                </form>
                
                <form action="{{ route('developer.cache.clear') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="cache">
                    <button type="submit" class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        <i class="fas fa-database mr-2"></i>مسح Application Cache
                    </button>
                </form>

                <form action="{{ route('developer.cache.clear') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="config">
                    <button type="submit" class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <i class="fas fa-cog mr-2"></i>مسح Config Cache
                    </button>
                </form>

                <form action="{{ route('developer.cache.clear') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="route">
                    <button type="submit" class="w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                        <i class="fas fa-route mr-2"></i>مسح Route Cache
                    </button>
                </form>

                <form action="{{ route('developer.cache.clear') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="view">
                    <button type="submit" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                        <i class="fas fa-eye mr-2"></i>مسح View Cache
                    </button>
                </form>

                <button onclick="window.location.reload()" class="w-full px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                    <i class="fas fa-sync-alt mr-2"></i>تحديث
                </button>
            </div>
        </div>
    </div>

    <!-- Cache Types Info -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-info-circle text-orange-400 mr-2"></i>أنواع Cache
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white/5 rounded-lg p-4">
                        <h3 class="text-white font-semibold mb-2 flex items-center">
                            <i class="fas fa-database text-red-400 mr-2"></i>Application Cache
                        </h3>
                        <p class="text-gray-400 text-sm">
                            يحتوي على البيانات المخزنة مؤقتاً من التطبيق مثل نتائج الاستعلامات والحسابات المعقدة.
                        </p>
                    </div>

                    <div class="bg-white/5 rounded-lg p-4">
                        <h3 class="text-white font-semibold mb-2 flex items-center">
                            <i class="fas fa-cog text-blue-400 mr-2"></i>Config Cache
                        </h3>
                        <p class="text-gray-400 text-sm">
                            يحتوي على ملفات الإعدادات المجمعة لتحسين سرعة تحميل التطبيق.
                        </p>
                    </div>

                    <div class="bg-white/5 rounded-lg p-4">
                        <h3 class="text-white font-semibold mb-2 flex items-center">
                            <i class="fas fa-route text-purple-400 mr-2"></i>Route Cache
                        </h3>
                        <p class="text-gray-400 text-sm">
                            يحتوي على المسارات المجمعة لتسريع عملية التوجيه في التطبيق.
                        </p>
                    </div>

                    <div class="bg-white/5 rounded-lg p-4">
                        <h3 class="text-white font-semibold mb-2 flex items-center">
                            <i class="fas fa-eye text-green-400 mr-2"></i>View Cache
                        </h3>
                        <p class="text-gray-400 text-sm">
                            يحتوي على ملفات Blade المجمعة لتحسين سرعة عرض الصفحات.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Warning Box -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-6">
            <h3 class="text-white font-semibold mb-2">
                <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>ملاحظة
            </h3>
            <p class="text-gray-400">
                مسح Cache قد يؤدي إلى بطء مؤقت في التطبيق حتى يتم إعادة بناء الذاكرة المؤقتة. 
                يُنصح بمسح Cache فقط عند الحاجة أو بعد إجراء تحديثات على الكود.
            </p>
        </div>
    </div>
</div>
@endsection
