<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>📦 Resource Generator - مولد API Resources ذكي</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold flex items-center gap-2">
                        📦 Resource Generator
                    </h1>
                    <p class="text-blue-100 mt-1">مولد API Resources ذكي مدعوم بالذكاء الاصطناعي v3.30.0</p>
                </div>
                <a href="{{ route('resource-generator.create') }}" 
                   class="bg-white text-blue-600 px-6 py-3 rounded-lg font-bold hover:bg-blue-50 transition-all shadow-lg">
                    ➕ إنشاء Resource جديد
                </a>
            </div>
        </div>
    </header>

    <!-- Quick Actions -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">🚀 إجراءات سريعة</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <a href="{{ route('resource-generator.create') }}?type=single" 
                   class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 text-center hover:bg-blue-100 transition-all">
                    <div class="text-3xl mb-2">📄</div>
                    <div class="font-bold text-blue-700">Single Resource</div>
                    <div class="text-xs text-gray-600 mt-1">تنسيق عنصر واحد</div>
                </a>
                <a href="{{ route('resource-generator.create') }}?type=collection" 
                   class="bg-green-50 border-2 border-green-200 rounded-lg p-4 text-center hover:bg-green-100 transition-all">
                    <div class="text-3xl mb-2">📚</div>
                    <div class="font-bold text-green-700">Collection Resource</div>
                    <div class="text-xs text-gray-600 mt-1">تنسيق مجموعة</div>
                </a>
                <a href="{{ route('resource-generator.create') }}?type=nested" 
                   class="bg-purple-50 border-2 border-purple-200 rounded-lg p-4 text-center hover:bg-purple-100 transition-all">
                    <div class="text-3xl mb-2">🔗</div>
                    <div class="font-bold text-purple-700">Nested Resource</div>
                    <div class="text-xs text-gray-600 mt-1">مع العلاقات</div>
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-blue-500">
                <div class="text-gray-600 text-sm">إجمالي Resources</div>
                <div class="text-3xl font-bold text-blue-600">{{ $statistics['total'] ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-green-500">
                <div class="text-gray-600 text-sm">ناجح</div>
                <div class="text-3xl font-bold text-green-600">{{ $statistics['successful'] ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-red-500">
                <div class="text-gray-600 text-sm">فاشل</div>
                <div class="text-3xl font-bold text-red-600">{{ $statistics['failed'] ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-yellow-500">
                <div class="text-gray-600 text-sm">معلق</div>
                <div class="text-3xl font-bold text-yellow-600">{{ $statistics['pending'] ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-purple-500">
                <div class="text-gray-600 text-sm">مولد بالـ AI</div>
                <div class="text-3xl font-bold text-purple-600">{{ $statistics['ai_generated'] ?? 0 }}</div>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
            <p class="font-bold">✅ نجح!</p>
            <p>{{ session('success') }}</p>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
            <p class="font-bold">❌ خطأ!</p>
            <p>{{ session('error') }}</p>
        </div>
        @endif

        <!-- Resources List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-100 px-6 py-4 border-b">
                <h2 class="text-xl font-bold text-gray-800">📋 Resources المولدة</h2>
            </div>

            @if($generations->isEmpty())
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">📦</div>
                <p class="text-gray-600 text-lg mb-4">لم يتم توليد أي Resources بعد</p>
                <a href="{{ route('resource-generator.create') }}" 
                   class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition-all">
                    إنشاء أول Resource
                </a>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Model</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">AI</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($generations as $generation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $generation->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $generation->name }}</div>
                                <div class="text-xs text-gray-500">{{ basename($generation->file_path) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($generation->type === 'single')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    📄 Single
                                </span>
                                @elseif($generation->type === 'collection')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    📚 Collection
                                </span>
                                @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    🔗 Nested
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $generation->model ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($generation->status === 'success')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    ✅ ناجح
                                </span>
                                @elseif($generation->status === 'failed')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    ❌ فاشل
                                </span>
                                @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    ⏳ معلق
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($generation->ai_generated)
                                <span class="text-purple-600 text-xl" title="مولد بالذكاء الاصطناعي">🤖</span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $generation->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('resource-generator.show', $generation->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 ml-3">عرض</a>
                                <form action="{{ route('resource-generator.destroy', $generation->id) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12 py-6">
        <div class="container mx-auto px-4 text-center">
            <p>📦 Resource Generator v3.30.0 - مدعوم بالذكاء الاصطناعي</p>
            <p class="text-gray-400 text-sm mt-2">تم التطوير بواسطة Manus AI</p>
        </div>
    </footer>

</body>
</html>
