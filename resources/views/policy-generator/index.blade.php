<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🛡️ Policy Generator - مولد Policies ذكي</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    
    <!-- Header -->
    <header class="bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold flex items-center gap-2">
                        🛡️ Policy Generator
                    </h1>
                    <p class="text-purple-100 mt-1">مولد Policies ذكي مدعوم بالذكاء الاصطناعي v3.31.0</p>
                </div>
                <a href="{{ route('policy-generator.create') }}" 
                   class="bg-white text-purple-600 px-6 py-3 rounded-lg font-bold hover:bg-purple-50 transition-all shadow-lg">
                    ➕ إنشاء Policy جديد
                </a>
            </div>
        </div>
    </header>

    <!-- Quick Actions -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">🚀 إجراءات سريعة</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('policy-generator.create') }}?type=resource" 
                   class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 text-center hover:bg-blue-100 transition-all">
                    <div class="text-3xl mb-2">📦</div>
                    <div class="font-bold text-blue-700">Resource Policy</div>
                    <div class="text-xs text-blue-600 mt-1">شامل مع جميع الأساليب</div>
                </a>
                <a href="{{ route('policy-generator.create') }}?type=custom" 
                   class="bg-green-50 border-2 border-green-200 rounded-lg p-4 text-center hover:bg-green-100 transition-all">
                    <div class="text-3xl mb-2">⚙️</div>
                    <div class="font-bold text-green-700">Custom Policy</div>
                    <div class="text-xs text-green-600 mt-1">مخصص حسب الحاجة</div>
                </a>
                <a href="{{ route('policy-generator.create') }}?type=role_based" 
                   class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4 text-center hover:bg-yellow-100 transition-all">
                    <div class="text-3xl mb-2">👥</div>
                    <div class="font-bold text-yellow-700">Role-Based</div>
                    <div class="text-xs text-yellow-600 mt-1">قائم على الأدوار</div>
                </a>
                <a href="{{ route('policy-generator.create') }}?type=ownership" 
                   class="bg-purple-50 border-2 border-purple-200 rounded-lg p-4 text-center hover:bg-purple-100 transition-all">
                    <div class="text-3xl mb-2">🔑</div>
                    <div class="font-bold text-purple-700">Ownership</div>
                    <div class="text-xs text-purple-600 mt-1">قائم على الملكية</div>
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-purple-500">
                <div class="text-gray-600 text-sm">إجمالي Policies</div>
                <div class="text-3xl font-bold text-purple-600">{{ $total ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-blue-500">
                <div class="text-gray-600 text-sm">Resource Policies</div>
                <div class="text-3xl font-bold text-blue-600">{{ collect($policies ?? [])->filter(fn($p) => str_contains(strtolower($p['name']), 'resource'))->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-green-500">
                <div class="text-gray-600 text-sm">Custom Policies</div>
                <div class="text-3xl font-bold text-green-600">{{ collect($policies ?? [])->filter(fn($p) => str_contains(strtolower($p['name']), 'custom'))->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-yellow-500">
                <div class="text-gray-600 text-sm">أخرى</div>
                <div class="text-3xl font-bold text-yellow-600">{{ collect($policies ?? [])->filter(fn($p) => !str_contains(strtolower($p['name']), 'resource') && !str_contains(strtolower($p['name']), 'custom'))->count() }}</div>
            </div>
        </div>

        @if(isset($error))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
            <p class="font-bold">❌ خطأ!</p>
            <p>{{ $error }}</p>
        </div>
        @endif

        <!-- Policies List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-100 px-6 py-4 border-b">
                <h2 class="text-xl font-bold text-gray-800">📋 Policies المولدة</h2>
            </div>
            
            @if(empty($policies) || count($policies) === 0)
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🛡️</div>
                <p class="text-gray-500 text-lg mb-4">لا توجد Policies مولدة بعد</p>
                <a href="{{ route('policy-generator.create') }}" 
                   class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-purple-700 transition-all">
                    ➕ إنشاء أول Policy
                </a>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase">الاسم</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase">الحجم</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase">آخر تعديل</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($policies as $policy)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">🛡️</span>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $policy['name'] }}</div>
                                        <div class="text-xs text-gray-500">{{ basename($policy['path']) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ number_format($policy['size'] / 1024, 2) }} KB
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ date('Y-m-d H:i', $policy['modified']) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('policy-generator.download', $policy['name']) }}" 
                                       class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                        📥 تحميل
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Info Section -->
        <div class="mt-8 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-6 border border-purple-200">
            <h3 class="text-lg font-bold text-purple-800 mb-3">💡 ما هي Policies؟</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                <div>
                    <p class="mb-2"><strong>📦 Resource Policy:</strong> Policy شامل مع جميع الأساليب القياسية (viewAny, view, create, update, delete, restore, forceDelete)</p>
                    <p class="mb-2"><strong>⚙️ Custom Policy:</strong> Policy مخصص بأساليب محددة حسب احتياجاتك</p>
                </div>
                <div>
                    <p class="mb-2"><strong>👥 Role-Based Policy:</strong> Policy يعتمد على الأدوار والصلاحيات</p>
                    <p class="mb-2"><strong>🔑 Ownership Policy:</strong> Policy يتحقق من ملكية المستخدم للمورد</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
