@extends('layouts.app')

@section('title', 'عارض السجلات - نظام المطور')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-rose-900 to-slate-900 pt-20 pb-10">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-file-alt text-rose-400 mr-3"></i>عارض السجلات (Logs)
                </h1>
                <p class="text-gray-400">عرض وإدارة ملفات سجلات التطبيق</p>
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
                    <p class="text-gray-400 text-sm">إجمالي ملفات السجلات</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $total }}</p>
                </div>
                <i class="fas fa-list-alt text-rose-400 text-5xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <div class="flex gap-3">
                <button onclick="window.location.reload()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-sync-alt mr-2"></i>تحديث
                </button>
            </div>
        </div>
    </div>

    <!-- Logs List -->
    @if(count($logs) > 0)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-list text-rose-400 mr-2"></i>قائمة ملفات السجلات ({{ count($logs) }})
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">اسم الملف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">الحجم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">آخر تعديل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($logs as $log)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="fas fa-file-alt text-rose-400 mr-3"></i>
                                    <span class="text-white font-mono text-sm">{{ $log['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-300 text-sm">{{ $log['size'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-300 text-sm">{{ $log['modified'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button onclick="viewLog('{{ $log['name'] }}')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition">
                                        <i class="fas fa-eye mr-1"></i>عرض
                                    </button>
                                    <a href="{{ route('developer.logs-viewer.file', basename($log['name'])) }}" download class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition">
                                        <i class="fas fa-download mr-1"></i>تحميل
                                    </a>
                                    <form action="{{ route('developer.logs-viewer.delete', basename($log['name'])) }}" method="POST" class="inline" onsubmit="return confirm('هل تريد حذف هذا الملف؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition">
                                            <i class="fas fa-trash mr-1"></i>حذف
                                        </button>
                                    </form>
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
        <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-6">
            <p class="text-green-400 text-center">
                <i class="fas fa-check-circle mr-2"></i>
                لا توجد ملفات سجلات حالياً. هذا يعني أن التطبيق يعمل بدون أخطاء!
            </p>
        </div>
    </div>
    @endif

    <!-- Info Boxes -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-6">
            <h3 class="text-white font-semibold mb-2">
                <i class="fas fa-info-circle text-blue-400 mr-2"></i>معلومات السجلات
            </h3>
            <p class="text-gray-400 text-sm">
                يتم حفظ السجلات في مجلد storage/logs. 
                السجلات تساعد في تتبع الأخطاء والمشاكل التي تحدث في التطبيق.
            </p>
        </div>
        <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-6">
            <h3 class="text-white font-semibold mb-2">
                <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>تحذير
            </h3>
            <p class="text-gray-400 text-sm">
                احذر من حذف ملفات السجلات إذا كنت تحقق في مشكلة حالية. 
                قد تحتاج إلى هذه المعلومات لتشخيص الأخطاء.
            </p>
        </div>
    </div>
</div>

<!-- Log Viewer Modal -->
<div id="logModal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
    <div class="bg-slate-800 rounded-lg w-full max-w-6xl max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-xl font-bold text-white">
                <i class="fas fa-file-alt text-rose-400 mr-2"></i>محتوى السجل
            </h3>
            <button onclick="closeLogModal()" class="text-gray-400 hover:text-white">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div class="p-6 overflow-auto flex-1">
            <pre id="logContent" class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm whitespace-pre-wrap"></pre>
        </div>
    </div>
</div>

<script>
function viewLog(filename) {
    document.getElementById('logModal').classList.remove('hidden');
    document.getElementById('logContent').textContent = 'جاري التحميل...';
    
    fetch(`{{ route('developer.logs-viewer') }}/${filename}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('logContent').textContent = data;
        })
        .catch(error => {
            document.getElementById('logContent').textContent = 'حدث خطأ أثناء تحميل الملف';
        });
}

function closeLogModal() {
    document.getElementById('logModal').classList.add('hidden');
}
</script>
@endsection
