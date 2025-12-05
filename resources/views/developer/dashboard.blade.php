@extends('layouts.app')

@section('title', 'لوحة تحكم المطور - SEMOP')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 pt-20 pb-10">
    <!-- Header Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-code text-blue-400 mr-3"></i>لوحة تحكم المطور
                </h1>
                <p class="text-gray-400">نظام المطور المتكامل v2.8.8 - Enhanced Edition</p>
            </div>
            <div class="flex gap-2">
                <button onclick="refreshDashboard()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-sync-alt mr-2"></i>تحديث
                </button>
                <button onclick="toggleFullscreen()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                    <i class="fas fa-expand mr-2"></i>ملء الشاشة
                </button>
            </div>
        </div>
    </div>

    <!-- System Overview Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- PHP Version -->
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 hover:border-blue-500/50 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">إصدار PHP</p>
                        <p class="text-2xl font-bold text-white mt-2">{{ $system_overview['php_version'] ?? 'N/A' }}</p>
                    </div>
                    <div class="text-4xl text-blue-400 opacity-20">
                        <i class="fas fa-code"></i>
                    </div>
                </div>
            </div>

            <!-- Laravel Version -->
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 hover:border-red-500/50 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">إصدار Laravel</p>
                        <p class="text-2xl font-bold text-white mt-2">{{ $system_overview['laravel_version'] ?? 'N/A' }}</p>
                    </div>
                    <div class="text-4xl text-red-400 opacity-20">
                        <i class="fab fa-laravel"></i>
                    </div>
                </div>
            </div>

            <!-- Database Tables -->
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 hover:border-green-500/50 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">جداول قاعدة البيانات</p>
                        <p class="text-2xl font-bold text-white mt-2">{{ $quick_stats['database_tables'] ?? 0 }}</p>
                    </div>
                    <div class="text-4xl text-green-400 opacity-20">
                        <i class="fas fa-database"></i>
                    </div>
                </div>
            </div>

            <!-- Environment -->
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20 hover:border-yellow-500/50 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">بيئة التطوير</p>
                        <p class="text-2xl font-bold text-white mt-2">
                            <span class="px-3 py-1 rounded-full text-sm bg-{{ $system_overview['environment'] === 'production' ? 'red' : 'green' }}-500/20 text-{{ $system_overview['environment'] === 'production' ? 'red' : 'green' }}-300">
                                {{ $system_overview['environment'] ?? 'N/A' }}
                            </span>
                        </p>
                    </div>
                    <div class="text-4xl text-yellow-400 opacity-20">
                        <i class="fas fa-server"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Tools Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section 1: Code Generation Tools -->
        <div class="mb-12">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1 h-8 bg-gradient-to-b from-blue-400 to-blue-600 rounded"></div>
                <h2 class="text-2xl font-bold text-white">
                    <i class="fas fa-wand-magic-wand text-blue-400 mr-2"></i>أدوات توليد الأكواد
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- AI Code Generator -->
                <a href="{{ route('ai.code-generator') }}" class="group">
                    <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg p-6 h-full border border-blue-500/30 hover:border-blue-400/60 transition transform hover:scale-105">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-white mb-2">مولد الأكواد بـ AI</h3>
                                <p class="text-blue-200 text-sm">توليد CRUD كامل في ثوان</p>
                            </div>
                            <i class="fas fa-robot text-3xl text-blue-300 opacity-50"></i>
                        </div>
                        <div class="flex items-center text-blue-200 text-sm">
                            <span>اضغط للدخول</span>
                            <i class="fas fa-arrow-left mr-2 group-hover:translate-x-1 transition"></i>
                        </div>
                    </div>
                </a>

                <!-- Code Generator -->
                <a href="{{ route('code-generator.index') }}" class="group">
                    <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-lg p-6 h-full border border-purple-500/30 hover:border-purple-400/60 transition transform hover:scale-105">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-white mb-2">مولد الأكواد</h3>
                                <p class="text-purple-200 text-sm">إنشاء Models, Controllers, Migrations</p>
                            </div>
                            <i class="fas fa-code text-3xl text-purple-300 opacity-50"></i>
                        </div>
                        <div class="flex items-center text-purple-200 text-sm">
                            <span>اضغط للدخول</span>
                            <i class="fas fa-arrow-left mr-2 group-hover:translate-x-1 transition"></i>
                        </div>
                    </div>
                </a>

                <!-- Database Designer -->
                <a href="{{ route('ai.database-optimizer') }}" class="group">
                    <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-lg p-6 h-full border border-indigo-500/30 hover:border-indigo-400/60 transition transform hover:scale-105">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-white mb-2">مصمم قاعدة البيانات</h3>
                                <p class="text-indigo-200 text-sm">تصميم الجداول بـ AI</p>
                            </div>
                            <i class="fas fa-diagram-project text-3xl text-indigo-300 opacity-50"></i>
                        </div>
                        <div class="flex items-center text-indigo-200 text-sm">
                            <span>اضغط للدخول</span>
                            <i class="fas fa-arrow-left mr-2 group-hover:translate-x-1 transition"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Section 2: Development Tools -->
        <div class="mb-12">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1 h-8 bg-gradient-to-b from-green-400 to-green-600 rounded"></div>
                <h2 class="text-2xl font-bold text-white">
                    <i class="fas fa-tools text-green-400 mr-2"></i>أدوات التطوير
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Artisan Commands -->
                <a href="{{ route('developer.artisan.index') }}" class="group">
                    <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-lg p-6 h-full border border-green-500/30 hover:border-green-400/60 transition transform hover:scale-105">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-white mb-2">أوامر Artisan</h3>
                                <p class="text-green-200 text-sm">تنفيذ أوامر Laravel</p>
                            </div>
                            <i class="fas fa-terminal text-3xl text-green-300 opacity-50"></i>
                        </div>
                        <div class="flex items-center text-green-200 text-sm">
                            <span>اضغط للدخول</span>
                            <i class="fas fa-arrow-left mr-2 group-hover:translate-x-1 transition"></i>
                        </div>
                    </div>
                </a>

                <!-- Database Manager -->
                <a href="{{ route('developer.database-info') }}" class="group">
                    <div class="bg-gradient-to-br from-cyan-600 to-cyan-800 rounded-lg p-6 h-full border border-cyan-500/30 hover:border-cyan-400/60 transition transform hover:scale-105">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-white mb-2">إدارة قاعدة البيانات</h3>
                                <p class="text-cyan-200 text-sm">عرض الجداول والبيانات</p>
                            </div>
                            <i class="fas fa-database text-3xl text-cyan-300 opacity-50"></i>
                        </div>
                        <div class="flex items-center text-cyan-200 text-sm">
                            <span>اضغط للدخول</span>
                            <i class="fas fa-arrow-left mr-2 group-hover:translate-x-1 transition"></i>
                        </div>
                    </div>
                </a>

                <!-- Code Review -->
                <a href="{{ route('ai.code-review') }}" class="group">
                    <div class="bg-gradient-to-br from-teal-600 to-teal-800 rounded-lg p-6 h-full border border-teal-500/30 hover:border-teal-400/60 transition transform hover:scale-105">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-white mb-2">مراجعة الأكواد بـ AI</h3>
                                <p class="text-teal-200 text-sm">تحسين جودة الكود</p>
                            </div>
                            <i class="fas fa-check-double text-3xl text-teal-300 opacity-50"></i>
                        </div>
                        <div class="flex items-center text-teal-200 text-sm">
                            <span>اضغط للدخول</span>
                            <i class="fas fa-arrow-left mr-2 group-hover:translate-x-1 transition"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Section 3: Monitoring & Maintenance -->
        <div class="mb-12">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1 h-8 bg-gradient-to-b from-yellow-400 to-yellow-600 rounded"></div>
                <h2 class="text-2xl font-bold text-white">
                    <i class="fas fa-chart-line text-yellow-400 mr-2"></i>المراقبة والصيانة
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- System Monitor -->
                <a href="{{ route('developer.server-info') }}" class="group">
                    <div class="bg-gradient-to-br from-yellow-600 to-yellow-800 rounded-lg p-6 h-full border border-yellow-500/30 hover:border-yellow-400/60 transition transform hover:scale-105">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-white mb-2">مراقبة النظام</h3>
                                <p class="text-yellow-200 text-sm">معلومات الخادم والأداء</p>
                            </div>
                            <i class="fas fa-server text-3xl text-yellow-300 opacity-50"></i>
                        </div>
                        <div class="flex items-center text-yellow-200 text-sm">
                            <span>اضغط للدخول</span>
                            <i class="fas fa-arrow-left mr-2 group-hover:translate-x-1 transition"></i>
                        </div>
                    </div>
                </a>

                <!-- Cache Manager -->
                <a href="{{ route('developer.cache') }}" class="group">
                    <div class="bg-gradient-to-br from-orange-600 to-orange-800 rounded-lg p-6 h-full border border-orange-500/30 hover:border-orange-400/60 transition transform hover:scale-105">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-white mb-2">الذاكرة المؤقتة</h3>
                                <p class="text-orange-200 text-sm">إدارة Cache والأداء</p>
                            </div>
                            <i class="fas fa-memory text-3xl text-orange-300 opacity-50"></i>
                        </div>
                        <div class="flex items-center text-orange-200 text-sm">
                            <span>اضغط للدخول</span>
                            <i class="fas fa-arrow-left mr-2 group-hover:translate-x-1 transition"></i>
                        </div>
                    </div>
                </a>

                <!-- Logs Viewer -->
                <a href="{{ route('developer.logs-viewer') }}" class="group">
                    <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-lg p-6 h-full border border-red-500/30 hover:border-red-400/60 transition transform hover:scale-105">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-white mb-2">السجلات</h3>
                                <p class="text-red-200 text-sm">عرض وتحليل السجلات</p>
                            </div>
                            <i class="fas fa-file-alt text-3xl text-red-300 opacity-50"></i>
                        </div>
                        <div class="flex items-center text-red-200 text-sm">
                            <span>اضغط للدخول</span>
                            <i class="fas fa-arrow-left mr-2 group-hover:translate-x-1 transition"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Section 4: AI Helper Tools -->
        <div class="mb-12">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1 h-8 bg-gradient-to-b from-pink-400 to-pink-600 rounded"></div>
                <h2 class="text-2xl font-bold text-white">
                    <i class="fas fa-sparkles text-pink-400 mr-2"></i>أدوات الذكاء الاصطناعي المساعدة
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Test Generator -->
                <a href="{{ route('ai.test-generator') }}" class="group">
                    <div class="bg-gradient-to-br from-pink-600 to-pink-800 rounded-lg p-6 h-full border border-pink-500/30 hover:border-pink-400/60 transition transform hover:scale-105">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-white mb-2">مولد الاختبارات</h3>
                                <p class="text-pink-200 text-sm">توليد Unit Tests تلقائياً</p>
                            </div>
                            <i class="fas fa-flask text-3xl text-pink-300 opacity-50"></i>
                        </div>
                        <div class="flex items-center text-pink-200 text-sm">
                            <span>اضغط للدخول</span>
                            <i class="fas fa-arrow-left mr-2 group-hover:translate-x-1 transition"></i>
                        </div>
                    </div>
                </a>

                <!-- Bug Fixer -->
                <a href="{{ route('ai.bug-detector') }}" class="group">
                    <div class="bg-gradient-to-br from-rose-600 to-rose-800 rounded-lg p-6 h-full border border-rose-500/30 hover:border-rose-400/60 transition transform hover:scale-105">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-white mb-2">مصحح الأخطاء</h3>
                                <p class="text-rose-200 text-sm">إصلاح الأخطاء بـ AI</p>
                            </div>
                            <i class="fas fa-bug text-3xl text-rose-300 opacity-50"></i>
                        </div>
                        <div class="flex items-center text-rose-200 text-sm">
                            <span>اضغط للدخول</span>
                            <i class="fas fa-arrow-left mr-2 group-hover:translate-x-1 transition"></i>
                        </div>
                    </div>
                </a>

                <!-- Documentation Generator -->
                <a href="{{ route('ai.documentation-generator') }}" class="group">
                    <div class="bg-gradient-to-br from-fuchsia-600 to-fuchsia-800 rounded-lg p-6 h-full border border-fuchsia-500/30 hover:border-fuchsia-400/60 transition transform hover:scale-105">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-white mb-2">مولد التوثيق</h3>
                                <p class="text-fuchsia-200 text-sm">توليد التوثيق تلقائياً</p>
                            </div>
                            <i class="fas fa-book text-3xl text-fuchsia-300 opacity-50"></i>
                        </div>
                        <div class="flex items-center text-fuchsia-200 text-sm">
                            <span>اضغط للدخول</span>
                            <i class="fas fa-arrow-left mr-2 group-hover:translate-x-1 transition"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Section 5: Quick Actions -->
        <div class="mb-12">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1 h-8 bg-gradient-to-b from-violet-400 to-violet-600 rounded"></div>
                <h2 class="text-2xl font-bold text-white">
                    <i class="fas fa-bolt text-violet-400 mr-2"></i>الإجراءات السريعة
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Clear Cache -->
                <button onclick="quickAction('cache-clear')" class="bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-lg p-4 border border-white/20 hover:border-violet-400/50 transition text-left">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-trash text-violet-400 text-xl"></i>
                        <div>
                            <p class="text-white font-semibold">مسح الذاكرة</p>
                            <p class="text-gray-400 text-xs">Clear Cache</p>
                        </div>
                    </div>
                </button>

                <!-- Run Tests -->
                <button onclick="quickAction('tests-run')" class="bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-lg p-4 border border-white/20 hover:border-violet-400/50 transition text-left">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-green-400 text-xl"></i>
                        <div>
                            <p class="text-white font-semibold">تشغيل الاختبارات</p>
                            <p class="text-gray-400 text-xs">Run Tests</p>
                        </div>
                    </div>
                </button>

                <!-- Format Code -->
                <button onclick="quickAction('pint-run')" class="bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-lg p-4 border border-white/20 hover:border-violet-400/50 transition text-left">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-wand-magic-wand text-blue-400 text-xl"></i>
                        <div>
                            <p class="text-white font-semibold">تنسيق الأكواد</p>
                            <p class="text-gray-400 text-xs">Format Code</p>
                        </div>
                    </div>
                </button>

                <!-- Database Backup -->
                <button onclick="quickAction('database-backup')" class="bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-lg p-4 border border-white/20 hover:border-violet-400/50 transition text-left">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-download text-yellow-400 text-xl"></i>
                        <div>
                            <p class="text-white font-semibold">نسخ احتياطي</p>
                            <p class="text-gray-400 text-xs">Database Backup</p>
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="bg-white/5 backdrop-blur-md rounded-lg p-6 border border-white/10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-gray-400 text-sm mb-2">إصدار النظام</p>
                    <p class="text-white font-semibold">v2.8.8 - Enhanced Edition</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm mb-2">آخر تحديث</p>
                    <p class="text-white font-semibold">{{ now()->format('Y-m-d H:i:s') }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm mb-2">حالة النظام</p>
                    <p class="text-green-400 font-semibold">
                        <i class="fas fa-circle text-green-400 text-xs mr-2"></i>يعمل بشكل طبيعي
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Quick Actions -->
<script>
function quickAction(action) {
    const actions = {
        'cache-clear': {
            url: '{{ route("cache.clear") }}',
            method: 'POST',
            message: 'جاري مسح الذاكرة المؤقتة...'
        },
        'tests-run': {
            url: '{{ route("developer.artisan.execute") }}',
            method: 'POST',
            data: { command: 'test' },
            message: 'جاري تشغيل الاختبارات...'
        },
        'pint-run': {
            url: '{{ route("developer.artisan.execute") }}',
            method: 'POST',
            data: { command: 'pint' },
            message: 'جاري تنسيق الأكواد...'
        },
        'database-backup': {
            url: '{{ route("developer.artisan.execute") }}',
            method: 'POST',
            data: { command: 'backup:run' },
            message: 'جاري إنشاء نسخة احتياطية...'
        }
    };

    const config = actions[action];
    if (!config) return;

    showNotification(config.message, 'info');
    
    fetch(config.url, {
        method: config.method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(config.data || {})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('تم بنجاح!', 'success');
        } else {
            showNotification(data.message || 'حدث خطأ', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ في الاتصال', 'error');
    });
}

function refreshDashboard() {
    location.reload();
}

function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(err => {
            alert(`خطأ في فتح ملء الشاشة: ${err.message}`);
        });
    } else {
        document.exitFullscreen();
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-20 right-4 px-6 py-4 rounded-lg text-white z-50 animate-slide-in-left ${
        type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Add animation to CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slide-in-left {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    .animate-slide-in-left {
        animation: slide-in-left 0.3s ease-out;
    }
`;
document.head.appendChild(style);
</script>

@endsection
