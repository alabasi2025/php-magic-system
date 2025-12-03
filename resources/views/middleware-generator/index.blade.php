<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🛡️ Middleware Generator - مولد Middleware ذكي</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    
    <!-- Header -->
    <header class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold flex items-center gap-2">
                        🛡️ Middleware Generator
                    </h1>
                    <p class="text-indigo-100 mt-1">مولد Middleware ذكي مدعوم بالذكاء الاصطناعي v3.28.0</p>
                </div>
                <a href="{{ route('middleware-generator.create') }}" 
                   class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-bold hover:bg-indigo-50 transition-all shadow-lg">
                    ➕ إنشاء Middleware جديد
                </a>
            </div>
        </div>
    </header>

    <!-- Quick Actions -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">🚀 إجراءات سريعة</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <a href="{{ route('middleware-generator.create') }}?type=authentication" 
                   class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 text-center hover:bg-blue-100 transition-all">
                    <div class="text-3xl mb-2">🔐</div>
                    <div class="font-bold text-blue-700">Authentication</div>
                </a>
                <a href="{{ route('middleware-generator.create') }}?type=authorization" 
                   class="bg-green-50 border-2 border-green-200 rounded-lg p-4 text-center hover:bg-green-100 transition-all">
                    <div class="text-3xl mb-2">✅</div>
                    <div class="font-bold text-green-700">Authorization</div>
                </a>
                <a href="{{ route('middleware-generator.create') }}?type=logging" 
                   class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4 text-center hover:bg-yellow-100 transition-all">
                    <div class="text-3xl mb-2">📝</div>
                    <div class="font-bold text-yellow-700">Logging</div>
                </a>
                <a href="{{ route('middleware-generator.create') }}?type=rate_limit" 
                   class="bg-red-50 border-2 border-red-200 rounded-lg p-4 text-center hover:bg-red-100 transition-all">
                    <div class="text-3xl mb-2">⏱️</div>
                    <div class="font-bold text-red-700">Rate Limit</div>
                </a>
                <a href="{{ route('middleware-generator.create') }}?type=cors" 
                   class="bg-purple-50 border-2 border-purple-200 rounded-lg p-4 text-center hover:bg-purple-100 transition-all">
                    <div class="text-3xl mb-2">🌐</div>
                    <div class="font-bold text-purple-700">CORS</div>
                </a>
                <a href="{{ route('middleware-generator.create') }}?type=custom" 
                   class="bg-gray-50 border-2 border-gray-200 rounded-lg p-4 text-center hover:bg-gray-100 transition-all">
                    <div class="text-3xl mb-2">⚙️</div>
                    <div class="font-bold text-gray-700">Custom</div>
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-indigo-500">
                <div class="text-gray-600 text-sm">إجمالي Middleware</div>
                <div class="text-3xl font-bold text-indigo-600">{{ count($middlewares ?? []) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-blue-500">
                <div class="text-gray-600 text-sm">Authentication</div>
                <div class="text-3xl font-bold text-blue-600">{{ collect($middlewares ?? [])->filter(fn($m) => str_contains(strtolower($m['name']), 'auth'))->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-green-500">
                <div class="text-gray-600 text-sm">Authorization</div>
                <div class="text-3xl font-bold text-green-600">{{ collect($middlewares ?? [])->filter(fn($m) => str_contains(strtolower($m['name']), 'permission') || str_contains(strtolower($m['name']), 'role'))->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-yellow-500">
                <div class="text-gray-600 text-sm">أخرى</div>
                <div class="text-3xl font-bold text-yellow-600">{{ collect($middlewares ?? [])->filter(fn($m) => !str_contains(strtolower($m['name']), 'auth') && !str_contains(strtolower($m['name']), 'permission') && !str_contains(strtolower($m['name']), 'role'))->count() }}</div>
            </div>
        </div>

        @if(isset($error))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
            <p class="font-bold">❌ خطأ!</p>
            <p>{{ $error }}</p>
        </div>
        @endif

        <!-- Middlewares List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-100 px-6 py-4 border-b">
                <h2 class="text-xl font-bold text-gray-800">📋 Middleware المولدة</h2>
            </div>
            
            @if(empty($middlewares))
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">🛡️</div>
                <h3 class="text-xl font-bold text-gray-600 mb-2">لا توجد Middleware مولدة بعد</h3>
                <p class="text-gray-500 mb-6">ابدأ بإنشاء أول Middleware باستخدام الذكاء الاصطناعي</p>
                <a href="{{ route('middleware-generator.create') }}" 
                   class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-indigo-700 transition-all">
                    ➕ إنشاء Middleware جديد
                </a>
            </div>
            @else
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">الاسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">الحجم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">آخر تعديل</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($middlewares as $middleware)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800">{{ $middleware['name'] }}</div>
                            <div class="text-sm text-gray-500">{{ basename($middleware['path']) }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ number_format($middleware['size'] / 1024, 2) }} KB
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $middleware['modified'] }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('middleware-generator.download') }}?name={{ $middleware['name'] }}" 
                                   class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
                                    📥 تحميل
                                </a>
                                <button onclick="deleteMiddleware('{{ $middleware['name'] }}')" 
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">
                                    🗑️ حذف
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400">
                🛡️ Middleware Generator v3.28.0 | Generated by Manus AI | SEMOP Team © 2025
            </p>
        </div>
    </footer>

    <script>
        function deleteMiddleware(name) {
            if (!confirm(`هل أنت متأكد من حذف ${name}؟`)) {
                return;
            }

            fetch(`/middleware-generator/${name}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('✅ تم حذف Middleware بنجاح');
                    location.reload();
                } else {
                    alert('❌ فشل الحذف: ' + data.message);
                }
            })
            .catch(error => {
                alert('❌ حدث خطأ: ' + error.message);
            });
        }
    </script>
</body>
</html>
