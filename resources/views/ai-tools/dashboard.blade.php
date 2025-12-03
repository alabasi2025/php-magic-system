@extends('layouts.app')

@section('title', 'نظام أدوات الذكاء الاصطناعي')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-4xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-brain text-purple-600 mr-3"></i>
                نظام أدوات الذكاء الاصطناعي المتقدمة
            </h1>
        </div>
        <p class="text-gray-600 text-lg">مركز التحكم المركزي لجميع أدوات الذكاء الاصطناعي لنظام المطور</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-purple-400 to-purple-600 text-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">إجمالي الأدوات</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_tools'] ?? 13 }}</p>
                </div>
                <i class="fas fa-tools fa-3x opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-400 to-green-600 text-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">الأدوات النشطة</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['active_tools'] ?? 13 }}</p>
                </div>
                <i class="fas fa-check-circle fa-3x opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-400 to-blue-600 text-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">استدعاءات API اليوم</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['api_calls_today'] ?? 0 }}</p>
                </div>
                <i class="fas fa-chart-line fa-3x opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 text-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Credits المتبقية</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['credits_remaining'] ?? 10000 }}</p>
                </div>
                <i class="fas fa-coins fa-3x opacity-50"></i>
            </div>
        </div>
    </div>

    {{-- Tools Grid --}}
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-th-large mr-2"></i>
            أدوات الذكاء الاصطناعي
        </h2>

        {{-- الفئة الأولى: الكتابة والتحرير --}}
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b-2 border-indigo-500 pb-2">
                <i class="fas fa-pen-fancy text-indigo-600 mr-2"></i>
                الذكاء الاصطناعي للكتابة والتحرير
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('ai-tools.code-assistant') }}" class="block p-4 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg hover:shadow-lg transition border-l-4 border-indigo-500">
                    <i class="fas fa-code text-indigo-600 text-2xl mb-2"></i>
                    <h4 class="font-semibold text-gray-800">مساعد الكود الذكي</h4>
                    <p class="text-sm text-gray-600">إكمال تلقائي متقدم للكود</p>
                </a>

                <a href="{{ route('ai-tools.design-to-code') }}" class="block p-4 bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg hover:shadow-lg transition border-l-4 border-pink-500">
                    <i class="fas fa-paint-brush text-pink-600 text-2xl mb-2"></i>
                    <h4 class="font-semibold text-gray-800">مولد الكود من التصاميم</h4>
                    <p class="text-sm text-gray-600">تحويل التصاميم إلى كود</p>
                </a>

                <a href="{{ route('ai-tools.nlp-code-generator') }}" class="block p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg hover:shadow-lg transition border-l-4 border-purple-500">
                    <i class="fas fa-language text-purple-600 text-2xl mb-2"></i>
                    <h4 class="font-semibold text-gray-800">مولد الكود من اللغة الطبيعية</h4>
                    <p class="text-sm text-gray-600">توليد أنظمة كاملة من الوصف</p>
                </a>
            </div>
        </div>

        {{-- الفئة الثانية: التحليل والتحسين --}}
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b-2 border-green-500 pb-2">
                <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                الذكاء الاصطناعي للتحليل والتحسين
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('ai-tools.performance-analyzer') }}" class="block p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg hover:shadow-lg transition border-l-4 border-green-500">
                    <i class="fas fa-tachometer-alt text-green-600 text-2xl mb-2"></i>
                    <h4 class="font-semibold text-gray-800">محلل الأداء الذكي</h4>
                    <p class="text-sm text-gray-600">تحليل وتحسين الأداء</p>
                </a>

                <a href="{{ route('ai-tools.security-scanner') }}" class="block p-4 bg-gradient-to-br from-red-50 to-red-100 rounded-lg hover:shadow-lg transition border-l-4 border-red-500">
                    <i class="fas fa-shield-alt text-red-600 text-2xl mb-2"></i>
                    <h4 class="font-semibold text-gray-800">مكتشف الثغرات الأمنية</h4>
                    <p class="text-sm text-gray-600">فحص الكود للثغرات الأمنية</p>
                </a>

                <a href="{{ route('ai-tools.code-refactoring') }}" class="block p-4 bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg hover:shadow-lg transition border-l-4 border-teal-500">
                    <i class="fas fa-sync-alt text-teal-600 text-2xl mb-2"></i>
                    <h4 class="font-semibold text-gray-800">مُعيد الهيكلة الذكي</h4>
                    <p class="text-sm text-gray-600">إعادة هيكلة الكود تلقائياً</p>
                </a>
            </div>
        </div>

        {{-- الفئة الثالثة: التعاون والتوثيق --}}
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b-2 border-blue-500 pb-2">
                <i class="fas fa-users text-blue-600 mr-2"></i>
                الذكاء الاصطناعي للتعاون والتوثيق
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('ai-tools.code-review-assistant') }}" class="block p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg hover:shadow-lg transition border-l-4 border-blue-500">
                    <i class="fas fa-search text-blue-600 text-2xl mb-2"></i>
                    <h4 class="font-semibold text-gray-800">مساعد المراجعة الذكي</h4>
                    <p class="text-sm text-gray-600">مراجعة الكود تلقائياً</p>
                </a>

                <a href="{{ route('ai-tools.interactive-doc-generator') }}" class="block p-4 bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-lg hover:shadow-lg transition border-l-4 border-cyan-500">
                    <i class="fas fa-book text-cyan-600 text-2xl mb-2"></i>
                    <h4 class="font-semibold text-gray-800">مولد التوثيق التفاعلي</h4>
                    <p class="text-sm text-gray-600">توليد توثيق تفاعلي</p>
                </a>

                <a href="{{ route('ai-tools.project-chatbot') }}" class="block p-4 bg-gradient-to-br from-violet-50 to-violet-100 rounded-lg hover:shadow-lg transition border-l-4 border-violet-500">
                    <i class="fas fa-comments text-violet-600 text-2xl mb-2"></i>
                    <h4 class="font-semibold text-gray-800">مساعد الدردشة الذكي</h4>
                    <p class="text-sm text-gray-600">روبوت دردشة للمشروع</p>
                </a>
            </div>
        </div>

        {{-- الفئة الرابعة: الاختبار والجودة --}}
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b-2 border-orange-500 pb-2">
                <i class="fas fa-vial text-orange-600 mr-2"></i>
                الذكاء الاصطناعي للاختبار والجودة
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('ai-tools.advanced-test-generator') }}" class="block p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg hover:shadow-lg transition border-l-4 border-orange-500">
                    <i class="fas fa-flask text-orange-600 text-2xl mb-2"></i>
                    <h4 class="font-semibold text-gray-800">مولد اختبارات ذكي متقدم</h4>
                    <p class="text-sm text-gray-600">توليد اختبارات شاملة</p>
                </a>

                <a href="{{ route('ai-tools.error-analyzer') }}" class="block p-4 bg-gradient-to-br from-rose-50 to-rose-100 rounded-lg hover:shadow-lg transition border-l-4 border-rose-500">
                    <i class="fas fa-bug text-rose-600 text-2xl mb-2"></i>
                    <h4 class="font-semibold text-gray-800">محلل الأخطاء الذكي</h4>
                    <p class="text-sm text-gray-600">تحليل الأخطاء وإصلاحها</p>
                </a>
            </div>
        </div>

        {{-- الفئة الخامسة: الإدارة والتخطيط --}}
        <div>
            <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b-2 border-amber-500 pb-2">
                <i class="fas fa-tasks text-amber-600 mr-2"></i>
                الذكاء الاصطناعي للإدارة والتخطيط
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('ai-tools.project-planning-assistant') }}" class="block p-4 bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg hover:shadow-lg transition border-l-4 border-amber-500">
                    <i class="fas fa-calendar-check text-amber-600 text-2xl mb-2"></i>
                    <h4 class="font-semibold text-gray-800">مساعد التخطيط الذكي</h4>
                    <p class="text-sm text-gray-600">تخطيط المشاريع بالذكاء الاصطناعي</p>
                </a>

                <a href="{{ route('ai-tools.productivity-analyzer') }}" class="block p-4 bg-gradient-to-br from-lime-50 to-lime-100 rounded-lg hover:shadow-lg transition border-l-4 border-lime-500">
                    <i class="fas fa-chart-pie text-lime-600 text-2xl mb-2"></i>
                    <h4 class="font-semibold text-gray-800">محلل الإنتاجية الذكي</h4>
                    <p class="text-sm text-gray-600">تحليل وتحسين الإنتاجية</p>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
