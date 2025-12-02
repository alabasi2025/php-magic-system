@extends('layouts.app')

@section('title', 'Laravel Pint - نظام المطور')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-cyan-900 to-slate-900 pt-20 pb-10">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-code text-cyan-400 mr-3"></i>Laravel Pint
                </h1>
                <p class="text-gray-400">أداة تنسيق الكود التلقائية لـ PHP</p>
            </div>
            <a href="{{ route('developer.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <i class="fas fa-arrow-right mr-2"></i>العودة
            </a>
        </div>
    </div>

    <!-- Status -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-white">
                    <i class="fas fa-info-circle text-cyan-400 mr-2"></i>حالة Pint
                </h2>
                <span class="px-4 py-2 rounded-full {{ $pint_installed ? 'bg-green-500/20 text-green-400 border border-green-500' : 'bg-red-500/20 text-red-400 border border-red-500' }}">
                    <i class="fas fa-circle mr-2"></i>{{ $pint_installed ? 'مثبّت' : 'غير مثبّت' }}
                </span>
            </div>

            @if($pint_installed)
                <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-4">
                    <p class="text-green-400">
                        <i class="fas fa-check-circle mr-2"></i>
                        Laravel Pint مثبّت ومتاح للاستخدام
                    </p>
                </div>
            @else
                <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4">
                    <p class="text-red-400 mb-2">
                        <i class="fas fa-times-circle mr-2"></i>
                        Laravel Pint غير مثبّت
                    </p>
                    <p class="text-gray-400 text-sm">
                        لتثبيت Pint، قم بتشغيل الأمر: <code class="bg-gray-800 px-2 py-1 rounded">composer require laravel/pint --dev</code>
                    </p>
                </div>
            @endif
        </div>
    </div>

    @if($pint_installed)
    <!-- Format Actions -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <h2 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-magic text-cyan-400 mr-2"></i>تنسيق الكود
            </h2>
            <form action="{{ route('developer.pint.format') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-white mb-2">المسار المراد تنسيقه</label>
                    <select name="path" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-cyan-500">
                        <option value="app">app/ - جميع ملفات التطبيق</option>
                        <option value="app/Http/Controllers">app/Http/Controllers - المتحكمات فقط</option>
                        <option value="app/Models">app/Models - النماذج فقط</option>
                        <option value="app/Services">app/Services - الخدمات فقط</option>
                        <option value="routes">routes/ - ملفات المسارات</option>
                        <option value="database">database/ - ملفات قاعدة البيانات</option>
                        <option value=".">. - جميع الملفات</option>
                    </select>
                </div>
                <button type="submit" class="px-6 py-3 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition">
                    <i class="fas fa-play mr-2"></i>تشغيل Pint
                </button>
            </form>
        </div>
    </div>

    @if(session('output'))
    <!-- Output -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <h2 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-terminal text-green-400 mr-2"></i>النتيجة
            </h2>
            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto font-mono text-sm">{{ session('output') }}</pre>
        </div>
    </div>
    @endif
    @endif

    <!-- Features -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <h2 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-star text-yellow-400 mr-2"></i>مميزات Laravel Pint
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-400 mt-1 mr-3"></i>
                    <div>
                        <h3 class="text-white font-semibold mb-1">تنسيق تلقائي</h3>
                        <p class="text-gray-400 text-sm">تنسيق الكود تلقائياً حسب معايير PSR-12</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-400 mt-1 mr-3"></i>
                    <div>
                        <h3 class="text-white font-semibold mb-1">سهل الاستخدام</h3>
                        <p class="text-gray-400 text-sm">لا يحتاج إلى إعدادات معقدة</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-400 mt-1 mr-3"></i>
                    <div>
                        <h3 class="text-white font-semibold mb-1">سريع وفعال</h3>
                        <p class="text-gray-400 text-sm">يعمل بسرعة على المشاريع الكبيرة</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-400 mt-1 mr-3"></i>
                    <div>
                        <h3 class="text-white font-semibold mb-1">قابل للتخصيص</h3>
                        <p class="text-gray-400 text-sm">يمكن تخصيص قواعد التنسيق</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentation -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-white font-semibold mb-2">
                        <i class="fas fa-book text-blue-400 mr-2"></i>التوثيق الرسمي
                    </h3>
                    <p class="text-gray-400">للمزيد من المعلومات حول Laravel Pint</p>
                </div>
                <a href="https://laravel.com/docs/pint" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-external-link-alt mr-2"></i>زيارة التوثيق
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
