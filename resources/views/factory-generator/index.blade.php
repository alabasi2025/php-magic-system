<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏭 Factory Generator - مولد Factories تلقائي</title>
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
                        🏭 Factory Generator
                    </h1>
                    <p class="text-gray-600 mt-1">مولد Factories تلقائي وذكي v3.25.0</p>
                </div>
                <a href="{{ route('factory-generator.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    ➕ إنشاء Factory جديد
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
                        <p class="text-gray-600 text-sm">إجمالي Factories</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $generations->total() }}</p>
                    </div>
                    <div class="text-4xl">📊</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">تم التوليد</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $generations->where('status', 'generated')->count() }}</p>
                    </div>
                    <div class="text-4xl">✅</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">تم الحفظ</p>
                        <p class="text-3xl font-bold text-green-600">{{ $generations->where('status', 'saved')->count() }}</p>
                    </div>
                    <div class="text-4xl">💾</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">أخطاء</p>
                        <p class="text-3xl font-bold text-red-600">{{ $generations->where('status', 'error')->count() }}</p>
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
                <form method="GET" action="{{ route('factory-generator.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="text" name="search" placeholder="بحث..." 
                           value="{{ request('search') }}"
                           class="border rounded px-4 py-2">
                    
                    <select name="status" class="border rounded px-4 py-2">
                        <option value="">جميع الحالات</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="generated" {{ request('status') == 'generated' ? 'selected' : '' }}>تم التوليد</option>
                        <option value="saved" {{ request('status') == 'saved' ? 'selected' : '' }}>تم الحفظ</option>
                        <option value="error" {{ request('status') == 'error' ? 'selected' : '' }}>خطأ</option>
                    </select>
                    
                    <select name="input_method" class="border rounded px-4 py-2">
                        <option value="">جميع الطرق</option>
                        <option value="web" {{ request('input_method') == 'web' ? 'selected' : '' }}>واجهة الويب</option>
                        <option value="json" {{ request('input_method') == 'json' ? 'selected' : '' }}>JSON</option>
                        <option value="template" {{ request('input_method') == 'template' ? 'selected' : '' }}>قالب</option>
                        <option value="reverse" {{ request('input_method') == 'reverse' ? 'selected' : '' }}>Reverse Engineering</option>
                    </select>
                    
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        بحث
                    </button>
                </form>
            </div>
        </div>

        <!-- قائمة الـ Factories -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Model</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الجدول</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الطريقة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($generations as $generation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $generation->name }}</div>
                                @if($generation->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($generation->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900 font-mono">{{ $generation->model_name }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-600 font-mono">{{ $generation->table_name }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                    {{ $generation->input_method }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-yellow-100 text-yellow-800',
                                        'generated' => 'bg-blue-100 text-blue-800',
                                        'saved' => 'bg-green-100 text-green-800',
                                        'error' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$generation->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $generation->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $generation->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('factory-generator.show', $generation->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 ml-3">عرض</a>
                                <a href="{{ route('factory-generator.download', $generation->id) }}" 
                                   class="text-green-600 hover:text-green-900 ml-3">تحميل</a>
                                <form action="{{ route('factory-generator.destroy', $generation->id) }}" 
                                      method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('هل أنت متأكد من الحذف؟')"
                                            class="text-red-600 hover:text-red-900">حذف</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="text-6xl mb-4">🏭</div>
                                <p class="text-lg">لا توجد Factories بعد</p>
                                <a href="{{ route('factory-generator.create') }}" 
                                   class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                    إنشاء أول Factory
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($generations->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $generations->links() }}
            </div>
            @endif
        </div>
    </div>

</body>
</html>
