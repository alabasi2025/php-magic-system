@extends('layouts.app')

@section('title', 'Seeders - نظام المطور')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-pink-900 to-slate-900 pt-20 pb-10">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-seedling text-pink-400 mr-3"></i>إدارة Seeders
                </h1>
                <p class="text-gray-400">تشغيل ملفات البذور لملء قاعدة البيانات بالبيانات الأولية</p>
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
                    <p class="text-gray-400 text-sm">إجمالي Seeders</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $total }}</p>
                </div>
                <i class="fas fa-seedling text-pink-400 text-5xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <h2 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-cog text-blue-400 mr-2"></i>الإجراءات
            </h2>
            <div class="flex flex-wrap gap-3">
                <form action="{{ route('developer.seeders.run') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-lg transition">
                        <i class="fas fa-play mr-2"></i>تشغيل جميع Seeders
                    </button>
                </form>
                <button onclick="window.location.reload()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-sync-alt mr-2"></i>تحديث
                </button>
            </div>
        </div>
    </div>

    <!-- Seeders List -->
    @if(count($seeders) > 0)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-list text-pink-400 mr-2"></i>قائمة Seeders ({{ count($seeders) }})
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">اسم Seeder</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">حجم الملف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($seeders as $seeder)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="fas fa-file-code text-pink-400 mr-3"></i>
                                    <span class="text-white font-mono text-sm">{{ $seeder['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-400 text-sm">{{ number_format($seeder['size'] / 1024, 2) }} KB</span>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('developer.seeders.run') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="class" value="{{ $seeder['name'] }}">
                                    <button type="submit" class="px-3 py-1 bg-pink-600 hover:bg-pink-700 text-white rounded text-sm transition">
                                        <i class="fas fa-play mr-1"></i>تشغيل
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-6">
            <p class="text-yellow-400 text-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                لا توجد ملفات Seeders متاحة حالياً
            </p>
        </div>
    </div>
    @endif

    <!-- Info Box -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-6">
            <h3 class="text-white font-semibold mb-2">
                <i class="fas fa-info-circle text-blue-400 mr-2"></i>ملاحظة هامة
            </h3>
            <p class="text-gray-400">
                Seeders تستخدم لملء قاعدة البيانات بالبيانات الأولية أو بيانات الاختبار. 
                تأكد من فهم محتوى كل Seeder قبل تشغيله لتجنب الكتابة فوق البيانات الموجودة.
            </p>
        </div>
    </div>
</div>
@endsection
