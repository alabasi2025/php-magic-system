<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧬 Migration Generator - مولد Migrations ذكي</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50" x-data="{ showFilters: false }">
    
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold flex items-center gap-2">
                        🧬 Migration Generator
                    </h1>
                    <p class="text-blue-100 mt-1">مولد Migrations ذكي مدعوم بالذكاء الاصطناعي</p>
                </div>
                <a href="{{ route('migration-generator.create') }}" 
                   class="bg-white text-blue-600 px-6 py-3 rounded-lg font-bold hover:bg-blue-50 transition-all shadow-lg">
                    ➕ إنشاء Migration جديد
                </a>
            </div>
        </div>
    </header>

    <!-- Stats Cards -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-blue-500">
                <div class="text-gray-600 text-sm">الإجمالي</div>
                <div class="text-3xl font-bold text-blue-600">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-gray-500">
                <div class="text-gray-600 text-sm">مسودات</div>
                <div class="text-3xl font-bold text-gray-600">{{ $stats['draft'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-green-500">
                <div class="text-gray-600 text-sm">مولدة</div>
                <div class="text-3xl font-bold text-green-600">{{ $stats['generated'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-yellow-500">
                <div class="text-gray-600 text-sm">مختبرة</div>
                <div class="text-3xl font-bold text-yellow-600">{{ $stats['tested'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-purple-500">
                <div class="text-gray-600 text-sm">مطبقة</div>
                <div class="text-3xl font-bold text-purple-600">{{ $stats['applied'] }}</div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
            <p class="font-bold">✅ نجح!</p>
            <p>{{ session('success') }}</p>
        </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <button @click="showFilters = !showFilters" 
                    class="text-blue-600 font-bold flex items-center gap-2">
                🔍 البحث والفلترة
                <span x-show="!showFilters">▼</span>
                <span x-show="showFilters">▲</span>
            </button>
            
            <div x-show="showFilters" x-transition class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" placeholder="ابحث بالاسم..." 
                       class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <select class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">كل الأنواع</option>
                    <option value="create">Create</option>
                    <option value="alter">Alter</option>
                    <option value="drop">Drop</option>
                </select>
                <select class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">كل الحالات</option>
                    <option value="draft">مسودة</option>
                    <option value="generated">مولدة</option>
                    <option value="tested">مختبرة</option>
                    <option value="applied">مطبقة</option>
                </select>
            </div>
        </div>

        <!-- Migrations Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">الاسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">الجدول</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">الطريقة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($generations as $generation)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $generation->name }}</div>
                            @if($generation->description)
                            <div class="text-sm text-gray-500">{{ Str::limit($generation->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-mono bg-gray-100 px-2 py-1 rounded">{{ $generation->table_name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm px-2 py-1 rounded 
                                @if($generation->migration_type === 'create') bg-blue-100 text-blue-800
                                @elseif($generation->migration_type === 'alter') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($generation->migration_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $generation->input_method }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm px-2 py-1 rounded font-medium
                                @if($generation->status === 'draft') bg-gray-100 text-gray-800
                                @elseif($generation->status === 'generated') bg-green-100 text-green-800
                                @elseif($generation->status === 'tested') bg-yellow-100 text-yellow-800
                                @else bg-purple-100 text-purple-800
                                @endif">
                                {{ ucfirst($generation->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $generation->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('migration-generator.show', $generation->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                    👁️ عرض
                                </a>
                                <a href="{{ route('migration-generator.download', $generation->id) }}" 
                                   class="text-green-600 hover:text-green-800 font-medium text-sm">
                                    ⬇️ تحميل
                                </a>
                                <form action="{{ route('migration-generator.destroy', $generation->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('هل أنت متأكد من الحذف؟')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                        🗑️ حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="text-6xl mb-4">📭</div>
                            <p class="text-lg font-medium">لا توجد migrations بعد</p>
                            <p class="text-sm mt-2">ابدأ بإنشاء أول migration ذكي!</p>
                            <a href="{{ route('migration-generator.create') }}" 
                               class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                                إنشاء الآن
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($generations->count() > 0)
        <div class="mt-6 flex justify-center">
            <!-- يمكن إضافة pagination هنا -->
        </div>
        @endif
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm">
                🧬 Migration Generator v3.23.0 | مدعوم بالذكاء الاصطناعي
            </p>
            <p class="text-xs text-gray-400 mt-2">
                PHP Magic System © 2025
            </p>
        </div>
    </footer>

</body>
</html>
