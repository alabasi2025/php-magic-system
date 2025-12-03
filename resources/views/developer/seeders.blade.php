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
                <button onclick="runAllSeeders()" class="px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-lg transition">
                    <i class="fas fa-play mr-2"></i>تشغيل جميع Seeders
                </button>
                <button onclick="window.location.reload()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-sync-alt mr-2"></i>تحديث
                </button>
            </div>
        </div>
    </div>

    <!-- Result Box -->
    <div id="resultBox" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 hidden">
        <div id="resultContent" class="rounded-lg p-6 border"></div>
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
                                <button onclick="runSeeder('{{ $seeder['name'] }}')" class="px-3 py-1 bg-pink-600 hover:bg-pink-700 text-white rounded text-sm transition">
                                    <i class="fas fa-play mr-1"></i>تشغيل
                                </button>
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

<script>
function showResult(success, message, output = null, duration = null) {
    const resultBox = document.getElementById('resultBox');
    const resultContent = document.getElementById('resultContent');
    
    resultBox.classList.remove('hidden');
    
    if (success) {
        resultContent.className = 'rounded-lg p-6 border bg-green-500/10 border-green-500/30';
        let html = `
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-400 text-2xl mr-3"></i>
                <div class="flex-1">
                    <h3 class="text-white font-semibold mb-2">${message}</h3>
                    ${duration ? `<p class="text-gray-400 text-sm mb-2">المدة: ${duration} ثانية</p>` : ''}
                    ${output ? `<pre class="bg-black/30 p-4 rounded text-gray-300 text-xs overflow-x-auto mt-3">${output}</pre>` : ''}
                </div>
            </div>
        `;
        resultContent.innerHTML = html;
    } else {
        resultContent.className = 'rounded-lg p-6 border bg-red-500/10 border-red-500/30';
        let html = `
            <div class="flex items-start">
                <i class="fas fa-times-circle text-red-400 text-2xl mr-3"></i>
                <div class="flex-1">
                    <h3 class="text-white font-semibold mb-2">فشل التشغيل</h3>
                    <p class="text-gray-400">${message}</p>
                    ${output ? `<pre class="bg-black/30 p-4 rounded text-gray-300 text-xs overflow-x-auto mt-3">${output}</pre>` : ''}
                </div>
            </div>
        `;
        resultContent.innerHTML = html;
    }
    
    // Scroll to result
    resultBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function showLoading(message) {
    const resultBox = document.getElementById('resultBox');
    const resultContent = document.getElementById('resultContent');
    
    resultBox.classList.remove('hidden');
    resultContent.className = 'rounded-lg p-6 border bg-blue-500/10 border-blue-500/30';
    resultContent.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-spinner fa-spin text-blue-400 text-2xl mr-3"></i>
            <div>
                <h3 class="text-white font-semibold">${message}</h3>
                <p class="text-gray-400 text-sm mt-1">الرجاء الانتظار...</p>
            </div>
        </div>
    `;
}

async function runSeeder(seederName) {
    if (!confirm(`هل أنت متأكد من تشغيل Seeder: ${seederName}؟\n\nقد يؤدي ذلك إلى تعديل البيانات في قاعدة البيانات.`)) {
        return;
    }
    
    showLoading(`جاري تشغيل ${seederName}...`);
    
    try {
        const response = await fetch('{{ route("developer.seeders.run") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                seeder: seederName
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showResult(true, data.message, data.output, data.duration);
        } else {
            showResult(false, data.message, data.trace);
        }
    } catch (error) {
        showResult(false, 'حدث خطأ في الاتصال: ' + error.message);
    }
}

async function runAllSeeders() {
    if (!confirm('هل أنت متأكد من تشغيل جميع Seeders؟\n\nقد يستغرق ذلك وقتاً طويلاً ويؤدي إلى تعديل البيانات في قاعدة البيانات.')) {
        return;
    }
    
    showLoading('جاري تشغيل جميع Seeders...');
    
    try {
        const response = await fetch('{{ route("developer.seeders.run-all") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showResult(true, data.message, data.output, data.duration);
        } else {
            showResult(false, data.message, data.trace);
        }
    } catch (error) {
        showResult(false, 'حدث خطأ في الاتصال: ' + error.message);
    }
}
</script>
@endsection
