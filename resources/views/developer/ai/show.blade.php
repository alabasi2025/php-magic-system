<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $generation->name }} - Resource Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/php.min.js"></script>
</head>
<body class="bg-gray-50">
    
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">📦 {{ $generation->name }}</h1>
                    <p class="text-blue-100 mt-1">تفاصيل Resource المولد</p>
                </div>
                <a href="{{ route('resource-generator.index') }}" 
                   class="bg-white text-blue-600 px-6 py-3 rounded-lg font-bold hover:bg-blue-50 transition-all">
                    ← العودة للقائمة
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        
        @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
            <p class="font-bold">✅ نجح!</p>
            <p>{{ session('success') }}</p>
        </div>
        @endif

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-gray-600 text-sm mb-1">النوع</div>
                @if($generation->type === 'single')
                <div class="text-2xl font-bold text-blue-600">📄 Single</div>
                @elseif($generation->type === 'collection')
                <div class="text-2xl font-bold text-green-600">📚 Collection</div>
                @else
                <div class="text-2xl font-bold text-purple-600">🔗 Nested</div>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-gray-600 text-sm mb-1">الحالة</div>
                @if($generation->status === 'success')
                <div class="text-2xl font-bold text-green-600">✅ ناجح</div>
                @elseif($generation->status === 'failed')
                <div class="text-2xl font-bold text-red-600">❌ فاشل</div>
                @else
                <div class="text-2xl font-bold text-yellow-600">⏳ معلق</div>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-gray-600 text-sm mb-1">Model</div>
                <div class="text-2xl font-bold text-gray-800">{{ $generation->model ?? '-' }}</div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-gray-600 text-sm mb-1">مولد بالـ AI</div>
                <div class="text-2xl font-bold {{ $generation->ai_generated ? 'text-purple-600' : 'text-gray-400' }}">
                    {{ $generation->ai_generated ? '🤖 نعم' : '❌ لا' }}
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">📋 التفاصيل</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="text-sm text-gray-600 mb-1">اسم الـ Resource</div>
                    <div class="font-medium text-gray-900">{{ $generation->name }}</div>
                </div>

                <div>
                    <div class="text-sm text-gray-600 mb-1">مسار الملف</div>
                    <div class="font-mono text-sm text-gray-900 bg-gray-100 px-2 py-1 rounded">
                        {{ $generation->file_path }}
                    </div>
                </div>

                @if($generation->attributes)
                <div>
                    <div class="text-sm text-gray-600 mb-1">الخصائص</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($generation->attributes as $attr)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $attr }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($generation->relations)
                <div>
                    <div class="text-sm text-gray-600 mb-1">العلاقات</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($generation->relations as $rel)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                            🔗 {{ $rel }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <div>
                    <div class="text-sm text-gray-600 mb-1">تاريخ الإنشاء</div>
                    <div class="font-medium text-gray-900">
                        {{ $generation->created_at->format('Y-m-d H:i:s') }}
                        <span class="text-gray-500 text-sm">({{ $generation->created_at->diffForHumans() }})</span>
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-600 mb-1">آخر تحديث</div>
                    <div class="font-medium text-gray-900">
                        {{ $generation->updated_at->format('Y-m-d H:i:s') }}
                        <span class="text-gray-500 text-sm">({{ $generation->updated_at->diffForHumans() }})</span>
                    </div>
                </div>
            </div>
        </div>

        @if($generation->error_message)
        <div class="bg-red-50 border-r-4 border-red-500 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-bold text-red-800 mb-2">❌ رسالة الخطأ</h3>
            <pre class="text-sm text-red-700 whitespace-pre-wrap">{{ $generation->error_message }}</pre>
        </div>
        @endif

        <!-- Code Preview -->
        @if($generation->content)
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gray-800 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-white">💻 الكود المولد</h2>
                <button onclick="copyCode()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-all text-sm">
                    📋 نسخ الكود
                </button>
            </div>
            <div class="relative">
                <pre><code class="language-php" id="code-content">{{ $generation->content }}</code></pre>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="flex gap-4">
            <a href="{{ route('resource-generator.create') }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition-all">
                ➕ إنشاء Resource جديد
            </a>
            
            <form action="{{ route('resource-generator.destroy', $generation->id) }}" 
                  method="POST" 
                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الـ Resource؟')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-red-700 transition-all">
                    🗑️ حذف
                </button>
            </form>
        </div>
    </div>

    <script>
        // Syntax highlighting
        hljs.highlightAll();

        // Copy code function
        function copyCode() {
            const code = document.getElementById('code-content').textContent;
            navigator.clipboard.writeText(code).then(() => {
                alert('✅ تم نسخ الكود بنجاح!');
            }).catch(err => {
                console.error('Failed to copy:', err);
            });
        }
    </script>

</body>
</html>
