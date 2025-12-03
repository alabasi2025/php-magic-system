<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏭 {{ $generation->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>
</head>
<body class="bg-gray-50" x-data="{ editing: false }">
    
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        🏭 {{ $generation->name }}
                    </h1>
                    <p class="text-gray-600 mt-1">تفاصيل الـ Factory</p>
                </div>
                <a href="{{ route('factory-generator.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    ← رجوع
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

        <!-- معلومات أساسية -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold">📊 المعلومات الأساسية</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الاسم</label>
                        <p class="text-lg font-semibold">{{ $generation->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                        <p class="text-lg font-mono">{{ $generation->model_name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الجدول</label>
                        <p class="text-lg font-mono">{{ $generation->table_name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">طريقة الإدخال</label>
                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-800">
                            {{ $generation->input_method }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                        @php
                            $statusColors = [
                                'draft' => 'bg-yellow-100 text-yellow-800',
                                'generated' => 'bg-blue-100 text-blue-800',
                                'saved' => 'bg-green-100 text-green-800',
                                'error' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full {{ $statusColors[$generation->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $generation->status }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الإنشاء</label>
                        <p class="text-lg">{{ $generation->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>

                @if($generation->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                    <p class="text-gray-600">{{ $generation->description }}</p>
                </div>
                @endif

                @if($generation->file_path)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">مسار الملف</label>
                    <p class="text-sm font-mono bg-gray-100 px-3 py-2 rounded">{{ $generation->file_path }}</p>
                </div>
                @endif

                @if($generation->error_message)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-red-700 mb-1">رسالة الخطأ</label>
                    <p class="text-red-600 bg-red-50 px-3 py-2 rounded">{{ $generation->error_message }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- الكود المولد -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6 border-b flex items-center justify-between">
                <h2 class="text-xl font-bold">💻 الكود المولد</h2>
                <div class="space-x-2 space-x-reverse">
                    <button @click="editing = !editing" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
                        <span x-show="!editing">✏️ تعديل</span>
                        <span x-show="editing">👁️ عرض</span>
                    </button>
                    <a href="{{ route('factory-generator.download', $generation->id) }}" 
                       class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition">
                        📥 تحميل
                    </a>
                    @if(!$generation->isSaved())
                    <form action="{{ route('factory-generator.save-file', $generation->id) }}" 
                          method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded transition">
                            💾 حفظ كملف
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="p-6">
                <!-- View Mode -->
                <div x-show="!editing">
                    <pre><code class="language-php">{{ $generation->generated_content }}</code></pre>
                </div>

                <!-- Edit Mode -->
                <div x-show="editing">
                    <form action="{{ route('factory-generator.update', $generation->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <textarea name="generated_content" rows="30"
                                  class="w-full border rounded-lg px-4 py-3 font-mono text-sm focus:ring-2 focus:ring-blue-500">{{ $generation->generated_content }}</textarea>
                        <div class="mt-4">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                                💾 حفظ التعديلات
                            </button>
                            <button type="button" @click="editing = false"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition mr-2">
                                ❌ إلغاء
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Input Data -->
        @if($generation->input_data)
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold">📋 بيانات الإدخال</h2>
            </div>
            <div class="p-6">
                <pre class="bg-gray-100 p-4 rounded-lg overflow-x-auto"><code class="language-json">{{ json_encode($generation->input_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            </div>
        </div>
        @endif

        <!-- إجراءات إضافية -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold">⚙️ إجراءات إضافية</h2>
            </div>
            <div class="p-6">
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('factory-generator.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                        ➕ إنشاء Factory جديد
                    </a>
                    
                    <form action="{{ route('factory-generator.destroy', $generation->id) }}" 
                          method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('هل أنت متأكد من حذف هذا الـ Factory؟')"
                                class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                            🗑️ حذف Factory
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
