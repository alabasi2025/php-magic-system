@extends('layouts.app')

@section('title', 'النسخ الاحتياطي - نظام المطور')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-teal-900 to-slate-900 pt-20 pb-10">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-save text-teal-400 mr-3"></i>النسخ الاحتياطي لقاعدة البيانات
                </h1>
                <p class="text-gray-400">إنشاء وإدارة النسخ الاحتياطية لقاعدة البيانات</p>
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
                    <p class="text-gray-400 text-sm">إجمالي النسخ الاحتياطية</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $total }}</p>
                </div>
                <i class="fas fa-database text-teal-400 text-5xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Create Backup -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <h2 class="text-xl font-bold text-white mb-4">
                <i class="fas fa-plus-circle text-teal-400 mr-2"></i>إنشاء نسخة احتياطية جديدة
            </h2>
            <form action="{{ route('developer.database-backup.create') }}" method="POST" onsubmit="return confirm('هل تريد إنشاء نسخة احتياطية جديدة؟')">
                @csrf
                <button type="submit" class="px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition">
                    <i class="fas fa-download mr-2"></i>إنشاء نسخة احتياطية الآن
                </button>
            </form>
        </div>
    </div>

    <!-- Backups List -->
    @if(count($backups) > 0)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-list text-teal-400 mr-2"></i>النسخ الاحتياطية المتاحة ({{ count($backups) }})
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">اسم الملف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">الحجم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">التاريخ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($backups as $backup)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="fas fa-file-archive text-teal-400 mr-3"></i>
                                    <span class="text-white font-mono text-sm">{{ $backup['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-300 text-sm">{{ $backup['size'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-300 text-sm">{{ $backup['date'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('developer.database-backup.download', basename($backup['name'])) }}" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition">
                                        <i class="fas fa-download mr-1"></i>تحميل
                                    </a>
                                </div>
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
                لا توجد نسخ احتياطية متاحة حالياً. قم بإنشاء نسخة احتياطية جديدة.
            </p>
        </div>
    </div>
    @endif

    <!-- Info Boxes -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-6">
            <h3 class="text-white font-semibold mb-2">
                <i class="fas fa-info-circle text-blue-400 mr-2"></i>معلومات مهمة
            </h3>
            <p class="text-gray-400 text-sm">
                يتم حفظ النسخ الاحتياطية في مجلد storage/app/backups. 
                تأكد من تحميل النسخ الاحتياطية إلى مكان آمن خارج الخادم.
            </p>
        </div>
        <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-6">
            <h3 class="text-white font-semibold mb-2">
                <i class="fas fa-check-circle text-green-400 mr-2"></i>أفضل الممارسات
            </h3>
            <p class="text-gray-400 text-sm">
                يُنصح بإنشاء نسخ احتياطية منتظمة، خاصة قبل إجراء أي تحديثات كبيرة على النظام أو قاعدة البيانات.
            </p>
        </div>
    </div>
</div>
@endsection
