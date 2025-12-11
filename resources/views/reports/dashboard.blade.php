<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التقارير المحاسبية</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">📊 التقارير المحاسبية</h1>
            <p class="text-gray-300">إدارة وعرض جميع التقارير المالية</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-300 text-sm">التقارير المتاحة</p>
                        <h3 class="text-3xl font-bold text-white mt-2">12</h3>
                    </div>
                    <div class="text-blue-400 text-4xl">📄</div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-300 text-sm">تم إنشاؤها اليوم</p>
                        <h3 class="text-3xl font-bold text-white mt-2">8</h3>
                    </div>
                    <div class="text-green-400 text-4xl">✅</div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-300 text-sm">التقارير المجدولة</p>
                        <h3 class="text-3xl font-bold text-white mt-2">5</h3>
                    </div>
                    <div class="text-purple-400 text-4xl">⏰</div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-300 text-sm">التصدير هذا الشهر</p>
                        <h3 class="text-3xl font-bold text-white mt-2">24</h3>
                    </div>
                    <div class="text-yellow-400 text-4xl">📥</div>
                </div>
            </div>
        </div>

        <!-- Reports Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Trial Balance -->
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300 cursor-pointer">
                <div class="flex items-start justify-between mb-4">
                    <div class="text-4xl">⚖️</div>
                    <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full text-xs">أساسي</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">ميزان المراجعة</h3>
                <p class="text-gray-300 text-sm mb-4">عرض أرصدة جميع الحسابات</p>
                <div class="flex gap-2">
                    <a href="{{ route('accounting-reports.trial-balance') }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center text-sm transition-colors">
                        عرض
                    </a>
                    <button class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                        PDF
                    </button>
                </div>
            </div>

            <!-- Income Statement -->
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300 cursor-pointer">
                <div class="flex items-start justify-between mb-4">
                    <div class="text-4xl">💰</div>
                    <span class="bg-green-500/20 text-green-300 px-3 py-1 rounded-full text-xs">أساسي</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">قائمة الدخل</h3>
                <p class="text-gray-300 text-sm mb-4">الإيرادات والمصروفات وصافي الربح</p>
                <div class="flex gap-2">
                    <a href="{{ route('accounting-reports.income-statement') }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center text-sm transition-colors">
                        عرض
                    </a>
                    <button class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                        PDF
                    </button>
                </div>
            </div>

            <!-- Balance Sheet -->
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300 cursor-pointer">
                <div class="flex items-start justify-between mb-4">
                    <div class="text-4xl">📊</div>
                    <span class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded-full text-xs">أساسي</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">الميزانية العمومية</h3>
                <p class="text-gray-300 text-sm mb-4">الأصول والخصوم وحقوق الملكية</p>
                <div class="flex gap-2">
                    <a href="{{ route('accounting-reports.balance-sheet') }}" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center text-sm transition-colors">
                        عرض
                    </a>
                    <button class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                        PDF
                    </button>
                </div>
            </div>

            <!-- General Ledger -->
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300 cursor-pointer">
                <div class="flex items-start justify-between mb-4">
                    <div class="text-4xl">📖</div>
                    <span class="bg-yellow-500/20 text-yellow-300 px-3 py-1 rounded-full text-xs">أساسي</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">دفتر الأستاذ العام</h3>
                <p class="text-gray-300 text-sm mb-4">حركات الحسابات التفصيلية</p>
                <div class="flex gap-2">
                    <a href="{{ route('accounting-reports.general-ledger') }}" class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-center text-sm transition-colors">
                        عرض
                    </a>
                    <button class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                        PDF
                    </button>
                </div>
            </div>

            <!-- Journal Entries -->
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300 cursor-pointer">
                <div class="flex items-start justify-between mb-4">
                    <div class="text-4xl">📝</div>
                    <span class="bg-red-500/20 text-red-300 px-3 py-1 rounded-full text-xs">أساسي</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">القيود اليومية</h3>
                <p class="text-gray-300 text-sm mb-4">تقرير شامل بجميع القيود</p>
                <div class="flex gap-2">
                    <a href="{{ route('accounting-reports.journal-entries') }}" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-center text-sm transition-colors">
                        عرض
                    </a>
                    <button class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                        PDF
                    </button>
                </div>
            </div>

            <!-- Cash Flow -->
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300 cursor-pointer">
                <div class="flex items-start justify-between mb-4">
                    <div class="text-4xl">💵</div>
                    <span class="bg-cyan-500/20 text-cyan-300 px-3 py-1 rounded-full text-xs">متقدم</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">التدفقات النقدية</h3>
                <p class="text-gray-300 text-sm mb-4">تحليل التدفقات النقدية</p>
                <div class="flex gap-2">
                    <a href="{{ route('accounting-reports.cash-flow') }}" class="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg text-center text-sm transition-colors">
                        عرض
                    </a>
                    <button class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                        PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
