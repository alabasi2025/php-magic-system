<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌱 Seeder Generator - مولد Seeders تلقائي</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50" x-data="{ showFilters: false }">
    
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        🌱 Seeder Generator
                    </h1>
                    <p class="text-gray-600 mt-1">مولد Seeders تلقائي وذكي v3.24.0</p>
                </div>
                <a href="{{ route('seeder-generator.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    ➕ إنشاء Seeder جديد
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- رسائل النجاح/الخطأ -->
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            ✅ {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            ❌ {{ session('error') }}
        </div>
        @endif

        <!-- الإحصائيات -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">إجمالي Seeders</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="text-4xl">📊</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">تم التوليد</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['generated'] }}</p>
                    </div>
                    <div class="text-4xl">✅</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">تم التنفيذ</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['executed'] }}</p>
                    </div>
                    <div class="text-4xl">🚀</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">فشل</p>
                        <p class="text-3xl font-bold text-red-600">{{ $stats['failed'] }}</p>
                    </div>
                    <div class="text-4xl">❌</div>
                </div>
            </div>
        </div>

        <!-- البحث والفلترة -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b">
                <button @click="showFilters = !showFilters" class="text-gray-700 font-semibold">
                    🔍 البحث والفلترة
                </button>
            </div>
            <div x-show="showFilters" class="p-4">
                <form method="GET" action="{{ route('seeder-generator.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="text" name="search" placeholder="بحث..." 
                           value="{{ request('search') }}"
                           class="border rounded px-4 py-2">
                    
                    <select name="status" class="border rounded px-4 py-2">
                        <option value="">كل الحالات</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="generated" {{ request('status') == 'generated' ? 'selected' : '' }}>تم التوليد</option>
                        <option value="executed" {{ request('status') == 'executed' ? 'selected' : '' }}>تم التنفيذ</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل</option>
                    </select>

                    <select name="input_method" class="border rounded px-4 py-2">
                        <option value="">كل الطرق</option>
                        <option value="web" {{ request('input_method') == 'web' ? 'selected' : '' }}>واجهة الويب</option>
                        <option value="json" {{ request('input_method') == 'json' ? 'selected' : '' }}>JSON</option>
                        <option value="template" {{ request('input_method') == 'template' ? 'selected' : '' }}>قالب</option>
                        <option value="reverse" {{ request('input_method') == 'reverse' ? 'selected' : '' }}>من جدول</option>
                    </select>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        بحث
                    </button>
                </form>
            </div>
        </div>

        <!-- الجدول -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الجدول</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">العدد</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الطريقة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($seeders as $seeder)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $seeder->id }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="font-semibold">{{ $seeder->name }}</div>
                            @if($seeder->description)
                            <div class="text-gray-500 text-xs mt-1">{{ Str::limit($seeder->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <code class="bg-gray-100 px-2 py-1 rounded">{{ $seeder->table_name }}</code>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($seeder->count) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                                {{ $seeder->input_method }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($seeder->status == 'draft')
                                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">مسودة</span>
                            @elseif($seeder->status == 'generated')
                                <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">تم التوليد</span>
                            @elseif($seeder->status == 'executed')
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">تم التنفيذ</span>
                            @elseif($seeder->status == 'failed')
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">فشل</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $seeder->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2 space-x-reverse">
                            <a href="{{ route('seeder-generator.show', $seeder->id) }}" 
                               class="text-blue-600 hover:text-blue-800">عرض</a>
                            <a href="{{ route('seeder-generator.download', $seeder->id) }}" 
                               class="text-green-600 hover:text-green-800">تحميل</a>
                            <form action="{{ route('seeder-generator.destroy', $seeder->id) }}" 
                                  method="POST" class="inline"
                                  onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">حذف</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <div class="text-6xl mb-4">🌱</div>
                            <p class="text-xl font-semibold mb-2">لا توجد Seeders بعد</p>
                            <p class="text-gray-400">ابدأ بإنشاء أول Seeder لك!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($seeders->hasPages())
        <div class="mt-6">
            {{ $seeders->links() }}
        </div>
        @endif

    </div>

</body>
</html>
