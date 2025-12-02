@extends('layouts.app')

@section('title', 'Migrations - نظام المطور')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-green-900 to-slate-900 pt-20 pb-10">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-database text-green-400 mr-3"></i>إدارة Migrations
                </h1>
                <p class="text-gray-400">إدارة وتشغيل ملفات الهجرة لقاعدة البيانات</p>
            </div>
            <a href="{{ route('developer.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <i class="fas fa-arrow-right mr-2"></i>العودة
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">إجمالي Migrations</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ $total }}</p>
                    </div>
                    <i class="fas fa-file-code text-blue-400 text-4xl opacity-20"></i>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">تم التنفيذ</p>
                        <p class="text-3xl font-bold text-green-400 mt-2">{{ count($ran) }}</p>
                    </div>
                    <i class="fas fa-check-circle text-green-400 text-4xl opacity-20"></i>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">قيد الانتظار</p>
                        <p class="text-3xl font-bold text-yellow-400 mt-2">{{ count($pending) }}</p>
                    </div>
                    <i class="fas fa-clock text-yellow-400 text-4xl opacity-20"></i>
                </div>
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
                <form action="{{ route('developer.migrations.run') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                        <i class="fas fa-play mr-2"></i>تشغيل Migrations
                    </button>
                </form>
                <button onclick="refreshMigrations()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-sync-alt mr-2"></i>تحديث
                </button>
            </div>
        </div>
    </div>

    <!-- Ran Migrations -->
    @if(count($ran) > 0)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-check-circle text-green-400 mr-2"></i>Migrations المنفذة ({{ count($ran) }})
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">اسم الملف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Batch</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($ran as $migration)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="fas fa-file-code text-blue-400 mr-3"></i>
                                    <span class="text-white font-mono text-sm">{{ $migration['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-sm">
                                    Batch {{ $migration['batch'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm">
                                    <i class="fas fa-check mr-1"></i>منفذ
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Pending Migrations -->
    @if(count($pending) > 0)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-clock text-yellow-400 mr-2"></i>Migrations قيد الانتظار ({{ count($pending) }})
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">اسم الملف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($pending as $migration)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="fas fa-file-code text-yellow-400 mr-3"></i>
                                    <span class="text-white font-mono text-sm">{{ $migration['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-sm">
                                    <i class="fas fa-clock mr-1"></i>قيد الانتظار
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function refreshMigrations() {
    window.location.reload();
}
</script>
@endsection
