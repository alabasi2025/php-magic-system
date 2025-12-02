@extends('layouts.app')

@section('title', 'Laravel Debugbar - نظام المطور')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 pt-20 pb-10">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-bug text-purple-400 mr-3"></i>Laravel Debugbar
                </h1>
                <p class="text-gray-400">أداة تصحيح الأخطاء والمراقبة المباشرة</p>
            </div>
            <a href="{{ route('developer.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <i class="fas fa-arrow-right mr-2"></i>العودة
            </a>
        </div>
    </div>

    <!-- Status Card -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white">
                    <i class="fas fa-info-circle text-blue-400 mr-2"></i>حالة Debugbar
                </h2>
                <span class="px-4 py-2 rounded-full {{ $debugbar_enabled ? 'bg-green-500/20 text-green-400 border border-green-500' : 'bg-red-500/20 text-red-400 border border-red-500' }}">
                    <i class="fas fa-circle mr-2"></i>{{ $debugbar_enabled ? 'مفعّل' : 'معطّل' }}
                </span>
            </div>

            @if($debugbar_enabled)
                <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-4 mb-4">
                    <p class="text-green-400">
                        <i class="fas fa-check-circle mr-2"></i>
                        Laravel Debugbar مفعّل ويعمل بشكل صحيح. يمكنك رؤية شريط التصحيح في أسفل الصفحة.
                    </p>
                </div>
            @else
                <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4 mb-4">
                    <p class="text-yellow-400">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Debugbar معطّل حالياً. لتفعيله، قم بتعيين <code class="bg-gray-800 px-2 py-1 rounded">APP_DEBUG=true</code> في ملف .env
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Collectors Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-white mb-4">
            <i class="fas fa-layer-group text-purple-400 mr-2"></i>المجمّعات المتاحة
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($debugbar_collectors as $key => $name)
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 hover:border-purple-500/50 transition">
                    <div class="flex items-center mb-3">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-database text-purple-400 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold">{{ $name }}</h3>
                            <p class="text-gray-400 text-sm">{{ $key }}</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm">يعرض معلومات تفصيلية عن {{ $name }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Features Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <h2 class="text-2xl font-bold text-white mb-4">
                <i class="fas fa-star text-yellow-400 mr-2"></i>المميزات الرئيسية
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-400 mt-1 mr-3"></i>
                    <div>
                        <h3 class="text-white font-semibold mb-1">مراقبة الاستعلامات</h3>
                        <p class="text-gray-400 text-sm">عرض جميع استعلامات قاعدة البيانات مع الوقت المستغرق</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-400 mt-1 mr-3"></i>
                    <div>
                        <h3 class="text-white font-semibold mb-1">تتبع المسارات</h3>
                        <p class="text-gray-400 text-sm">معلومات تفصيلية عن المسار الحالي والمتحكم</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-400 mt-1 mr-3"></i>
                    <div>
                        <h3 class="text-white font-semibold mb-1">عرض الواجهات</h3>
                        <p class="text-gray-400 text-sm">قائمة بجميع الواجهات المستخدمة في الصفحة</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-400 mt-1 mr-3"></i>
                    <div>
                        <h3 class="text-white font-semibold mb-1">تسجيل الأحداث</h3>
                        <p class="text-gray-400 text-sm">مراقبة جميع الأحداث التي تحدث في التطبيق</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentation Link -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-white font-semibold mb-2">
                        <i class="fas fa-book text-blue-400 mr-2"></i>التوثيق الرسمي
                    </h3>
                    <p class="text-gray-400">للمزيد من المعلومات حول استخدام Laravel Debugbar</p>
                </div>
                <a href="https://github.com/barryvdh/laravel-debugbar" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-external-link-alt mr-2"></i>زيارة GitHub
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
