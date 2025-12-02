@extends('layouts.app')

@section('title', 'معلومات الخادم - نظام المطور')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-amber-900 to-slate-900 pt-20 pb-10">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-server text-amber-400 mr-3"></i>معلومات الخادم
                </h1>
                <p class="text-gray-400">معلومات تفصيلية عن الخادم وبيئة التشغيل</p>
            </div>
            <a href="{{ route('developer.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <i class="fas fa-arrow-right mr-2"></i>العودة
            </a>
        </div>
    </div>

    <!-- Main Info Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">إصدار PHP</p>
                        <p class="text-2xl font-bold text-white mt-2">{{ $php_version }}</p>
                    </div>
                    <i class="fas fa-code text-blue-400 text-4xl opacity-20"></i>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">إصدار Laravel</p>
                        <p class="text-2xl font-bold text-white mt-2">{{ $laravel_version }}</p>
                    </div>
                    <i class="fas fa-laravel text-red-400 text-4xl opacity-20"></i>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">برنامج الخادم</p>
                        <p class="text-lg font-bold text-white mt-2">{{ $server_software }}</p>
                    </div>
                    <i class="fas fa-server text-amber-400 text-4xl opacity-20"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Server Details -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-info-circle text-amber-400 mr-2"></i>تفاصيل الخادم
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-white/10">
                            <span class="text-gray-400">عنوان IP الخادم</span>
                            <span class="text-white font-mono">{{ $server_ip }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-white/10">
                            <span class="text-gray-400">اسم الخادم</span>
                            <span class="text-white font-mono">{{ $server_name }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-white/10">
                            <span class="text-gray-400">منفذ الخادم</span>
                            <span class="text-white font-mono">{{ $server_port }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-white/10">
                            <span class="text-gray-400">المجلد الجذر</span>
                            <span class="text-white font-mono text-sm">{{ $document_root }}</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-white/10">
                            <span class="text-gray-400">حد الذاكرة</span>
                            <span class="text-white font-mono">{{ $memory_limit }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-white/10">
                            <span class="text-gray-400">وقت التنفيذ الأقصى</span>
                            <span class="text-white font-mono">{{ $max_execution_time }}s</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-white/10">
                            <span class="text-gray-400">حجم الرفع الأقصى</span>
                            <span class="text-white font-mono">{{ $upload_max_filesize }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-white/10">
                            <span class="text-gray-400">حجم POST الأقصى</span>
                            <span class="text-white font-mono">{{ $post_max_size }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Disk Space -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-hdd text-amber-400 mr-2"></i>مساحة القرص
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white/5 rounded-lg p-4">
                        <p class="text-gray-400 text-sm mb-2">المساحة المتاحة</p>
                        <p class="text-3xl font-bold text-green-400">{{ $disk_free_space }}</p>
                    </div>
                    <div class="bg-white/5 rounded-lg p-4">
                        <p class="text-gray-400 text-sm mb-2">المساحة الإجمالية</p>
                        <p class="text-3xl font-bold text-blue-400">{{ $disk_total_space }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PHP Extensions -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-puzzle-piece text-amber-400 mr-2"></i>امتدادات PHP المثبتة ({{ count($php_extensions) }})
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($php_extensions as $extension)
                        <div class="bg-white/5 rounded px-3 py-2 text-center">
                            <span class="text-gray-300 text-sm">{{ $extension }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
