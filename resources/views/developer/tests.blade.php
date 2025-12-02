@extends('layouts.app')

@section('title', 'الاختبارات - نظام المطور')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-emerald-900 to-slate-900 pt-20 pb-10">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-vial text-emerald-400 mr-3"></i>تشغيل الاختبارات
                </h1>
                <p class="text-gray-400">تشغيل وإدارة اختبارات PHPUnit</p>
            </div>
            <a href="{{ route('developer.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <i class="fas fa-arrow-right mr-2"></i>العودة
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">إجمالي ملفات الاختبار</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $total }}</p>
                </div>
                <i class="fas fa-flask text-emerald-400 text-5xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Run Tests -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <h2 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-play-circle text-emerald-400 mr-2"></i>تشغيل الاختبارات
            </h2>
            <form action="{{ route('developer.tests.execute') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-white mb-2">فلتر الاختبارات (اختياري)</label>
                    <input type="text" name="filter" placeholder="اسم الاختبار أو الفئة" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-emerald-500">
                    <p class="text-gray-400 text-sm mt-1">اترك فارغاً لتشغيل جميع الاختبارات</p>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                        <i class="fas fa-play mr-2"></i>تشغيل الاختبارات
                    </button>
                    <button type="button" onclick="window.location.reload()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <i class="fas fa-sync-alt mr-2"></i>تحديث
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('output'))
    <!-- Test Output -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <h2 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-terminal text-emerald-400 mr-2"></i>نتيجة الاختبارات
            </h2>
            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto font-mono text-sm max-h-96">{{ session('output') }}</pre>
        </div>
    </div>
    @endif

    <!-- Tests List -->
    @if(count($tests) > 0)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-list text-emerald-400 mr-2"></i>قائمة ملفات الاختبار ({{ count($tests) }})
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">اسم الملف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">الحجم</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($tests as $test)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="fas fa-file-code text-emerald-400 mr-3"></i>
                                    <span class="text-white font-mono text-sm">{{ $test['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm {{ $test['type'] == 'Feature' ? 'bg-blue-500/20 text-blue-400' : 'bg-purple-500/20 text-purple-400' }}">
                                    {{ $test['type'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-300 text-sm">{{ $test['size'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-6">
            <p class="text-yellow-400 text-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                لا توجد ملفات اختبار متاحة حالياً
            </p>
        </div>
    </div>
    @endif

    <!-- Test Types Info -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <h2 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-info-circle text-blue-400 mr-2"></i>أنواع الاختبارات
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white/5 rounded-lg p-4">
                    <h3 class="text-white font-semibold mb-2 flex items-center">
                        <i class="fas fa-layer-group text-blue-400 mr-2"></i>Feature Tests
                    </h3>
                    <p class="text-gray-400 text-sm">
                        اختبارات المميزات الكاملة التي تختبر عدة أجزاء من التطبيق معاً، مثل HTTP requests والتفاعل مع قاعدة البيانات.
                    </p>
                </div>

                <div class="bg-white/5 rounded-lg p-4">
                    <h3 class="text-white font-semibold mb-2 flex items-center">
                        <i class="fas fa-cube text-purple-400 mr-2"></i>Unit Tests
                    </h3>
                    <p class="text-gray-400 text-sm">
                        اختبارات الوحدات التي تختبر أجزاء صغيرة ومعزولة من الكود، مثل دوال محددة أو فئات منفردة.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
