@extends('layouts.app')

@section('title', 'تحسين قاعدة البيانات - نظام المطور')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-900 pt-20 pb-10">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-tachometer-alt text-indigo-400 mr-3"></i>تحسين قاعدة البيانات
                </h1>
                <p class="text-gray-400">تحسين وتنظيف جداول قاعدة البيانات لتحسين الأداء</p>
            </div>
            <a href="{{ route('developer.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <i class="fas fa-arrow-right mr-2"></i>العودة
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">إجمالي الجداول</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ $total_tables }}</p>
                    </div>
                    <i class="fas fa-table text-blue-400 text-4xl opacity-20"></i>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">الحجم الإجمالي</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ $total_size }}</p>
                    </div>
                    <i class="fas fa-database text-indigo-400 text-4xl opacity-20"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Optimize Action -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <h2 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-tools text-indigo-400 mr-2"></i>إجراءات التحسين
            </h2>
            <div class="flex flex-wrap gap-3">
                <form action="{{ route('developer.database-optimize.run') }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من تحسين جميع الجداول؟')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                        <i class="fas fa-magic mr-2"></i>تحسين جميع الجداول
                    </button>
                </form>
                <button onclick="window.location.reload()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-sync-alt mr-2"></i>تحديث
                </button>
            </div>
        </div>
    </div>

    <!-- Tables List -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-list text-indigo-400 mr-2"></i>قائمة الجداول ({{ count($formattedTables) }})
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">اسم الجدول</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">المحرك</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">عدد الصفوف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">حجم البيانات</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">حجم الفهارس</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">الحجم الكلي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($formattedTables as $table)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="fas fa-table text-indigo-400 mr-3"></i>
                                    <span class="text-white font-mono text-sm">{{ $table['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs">{{ $table['engine'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-300 text-sm">{{ number_format($table['rows']) }}</td>
                            <td class="px-6 py-4 text-gray-300 text-sm">{{ $table['data_size'] }}</td>
                            <td class="px-6 py-4 text-gray-300 text-sm">{{ $table['index_size'] }}</td>
                            <td class="px-6 py-4">
                                <span class="text-white font-semibold">{{ $table['total_size'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-6">
            <h3 class="text-white font-semibold mb-2">
                <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>تحذير
            </h3>
            <p class="text-gray-400">
                عملية التحسين قد تستغرق بعض الوقت حسب حجم قاعدة البيانات. 
                يُنصح بإجراء نسخة احتياطية قبل تنفيذ عملية التحسين.
            </p>
        </div>
    </div>
</div>
@endsection
