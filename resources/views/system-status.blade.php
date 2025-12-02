@extends('layouts.app')

@section('title', 'حالة النظام - SEMOP')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 pt-20 pb-10">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-1 h-10 bg-gradient-to-b from-green-400 to-green-600 rounded"></div>
                <div>
                    <h1 class="text-4xl font-bold text-white">
                        <i class="fas fa-heartbeat text-green-400 mr-3"></i>حالة النظام
                    </h1>
                    <p class="text-gray-400 mt-2">مراقبة صحة وأداء التطبيق</p>
                </div>
            </div>
            <button onclick="refreshStatus()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-sync-alt mr-2"></i>تحديث
            </button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Overall Status -->
        <div class="bg-gradient-to-r from-green-600 to-green-800 rounded-lg p-8 mb-8 border border-green-500/30">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">
                        <i class="fas fa-circle text-green-300 text-lg mr-2"></i>النظام يعمل بشكل طبيعي
                    </h2>
                    <p class="text-green-100">جميع الخدمات تعمل بكفاءة عالية</p>
                </div>
                <div class="text-6xl text-green-300 opacity-20">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Uptime -->
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-white font-semibold">وقت التشغيل</h3>
                    <i class="fas fa-clock text-blue-400 text-2xl"></i>
                </div>
                <p class="text-3xl font-bold text-white mb-2">99.9%</p>
                <p class="text-gray-400 text-sm">آخر 30 يوم</p>
            </div>

            <!-- Response Time -->
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-white font-semibold">وقت الاستجابة</h3>
                    <i class="fas fa-tachometer-alt text-green-400 text-2xl"></i>
                </div>
                <p class="text-3xl font-bold text-white mb-2">45ms</p>
                <p class="text-gray-400 text-sm">متوسط</p>
            </div>

            <!-- Active Users -->
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-white font-semibold">المستخدمون النشطون</h3>
                    <i class="fas fa-users text-purple-400 text-2xl"></i>
                </div>
                <p class="text-3xl font-bold text-white mb-2">1,234</p>
                <p class="text-gray-400 text-sm">الآن</p>
            </div>

            <!-- Database Size -->
            <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-white font-semibold">حجم قاعدة البيانات</h3>
                    <i class="fas fa-database text-yellow-400 text-2xl"></i>
                </div>
                <p class="text-3xl font-bold text-white mb-2">2.5GB</p>
                <p class="text-gray-400 text-sm">من 10GB</p>
            </div>
        </div>

        <!-- Services Status -->
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-6 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">
                <i class="fas fa-cogs text-blue-400 mr-2"></i>حالة الخدمات
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Database -->
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-database text-green-400 text-2xl"></i>
                        <div>
                            <p class="text-white font-semibold">قاعدة البيانات</p>
                            <p class="text-gray-400 text-sm">MySQL 8.0</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm font-semibold">
                        <i class="fas fa-circle text-xs mr-1"></i>يعمل
                    </span>
                </div>

                <!-- Cache -->
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-memory text-blue-400 text-2xl"></i>
                        <div>
                            <p class="text-white font-semibold">الذاكرة المؤقتة</p>
                            <p class="text-gray-400 text-sm">Redis</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm font-semibold">
                        <i class="fas fa-circle text-xs mr-1"></i>يعمل
                    </span>
                </div>

                <!-- Queue -->
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-tasks text-purple-400 text-2xl"></i>
                        <div>
                            <p class="text-white font-semibold">نظام الرسائل</p>
                            <p class="text-gray-400 text-sm">Queue System</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm font-semibold">
                        <i class="fas fa-circle text-xs mr-1"></i>يعمل
                    </span>
                </div>

                <!-- Email -->
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-envelope text-red-400 text-2xl"></i>
                        <div>
                            <p class="text-white font-semibold">خدمة البريد</p>
                            <p class="text-gray-400 text-sm">SMTP</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm font-semibold">
                        <i class="fas fa-circle text-xs mr-1"></i>يعمل
                    </span>
                </div>

                <!-- Storage -->
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-hdd text-orange-400 text-2xl"></i>
                        <div>
                            <p class="text-white font-semibold">التخزين</p>
                            <p class="text-gray-400 text-sm">File Storage</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm font-semibold">
                        <i class="fas fa-circle text-xs mr-1"></i>يعمل
                    </span>
                </div>

                <!-- API -->
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-plug text-cyan-400 text-2xl"></i>
                        <div>
                            <p class="text-white font-semibold">API Gateway</p>
                            <p class="text-gray-400 text-sm">REST API</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm font-semibold">
                        <i class="fas fa-circle text-xs mr-1"></i>يعمل
                    </span>
                </div>
            </div>
        </div>

        <!-- Performance Chart -->
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-6 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">
                <i class="fas fa-chart-line text-blue-400 mr-2"></i>الأداء (آخر 24 ساعة)
            </h2>
            
            <div class="bg-black/30 rounded-lg p-4 h-64 flex items-end justify-around gap-1">
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-1/2 opacity-70"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-3/5 opacity-70"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-2/3 opacity-70"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-3/4 opacity-70"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-4/5 opacity-70"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-3/4 opacity-70"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-2/3 opacity-70"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-3/5 opacity-70"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-1/2 opacity-70"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-2/3 opacity-70"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-3/4 opacity-70"></div>
                <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t h-4/5 opacity-70"></div>
            </div>
            
            <div class="flex justify-between mt-4 text-gray-400 text-xs">
                <span>00:00</span>
                <span>06:00</span>
                <span>12:00</span>
                <span>18:00</span>
                <span>23:59</span>
            </div>
        </div>

        <!-- Recent Alerts -->
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-6">
            <h2 class="text-2xl font-bold text-white mb-6">
                <i class="fas fa-bell text-yellow-400 mr-2"></i>التنبيهات الأخيرة
            </h2>
            
            <div class="space-y-3">
                <div class="flex items-start gap-4 p-4 bg-white/5 rounded-lg border-l-4 border-blue-500">
                    <i class="fas fa-info-circle text-blue-400 text-xl mt-1"></i>
                    <div class="flex-1">
                        <p class="text-white font-semibold">تحديث النظام</p>
                        <p class="text-gray-400 text-sm">تم تحديث النظام بنجاح إلى الإصدار 2.8.5</p>
                        <p class="text-gray-500 text-xs mt-1">منذ ساعة واحدة</p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 bg-white/5 rounded-lg border-l-4 border-green-500">
                    <i class="fas fa-check-circle text-green-400 text-xl mt-1"></i>
                    <div class="flex-1">
                        <p class="text-white font-semibold">نسخة احتياطية</p>
                        <p class="text-gray-400 text-sm">تمت النسخة الاحتياطية اليومية بنجاح</p>
                        <p class="text-gray-500 text-xs mt-1">منذ 3 ساعات</p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 bg-white/5 rounded-lg border-l-4 border-yellow-500">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-xl mt-1"></i>
                    <div class="flex-1">
                        <p class="text-white font-semibold">استخدام التخزين</p>
                        <p class="text-gray-400 text-sm">استخدام التخزين وصل إلى 75% من السعة</p>
                        <p class="text-gray-500 text-xs mt-1">منذ 6 ساعات</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshStatus() {
    location.reload();
}
</script>

@endsection
