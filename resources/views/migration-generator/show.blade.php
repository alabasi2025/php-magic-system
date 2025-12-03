<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $generation->name }} - Migration Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/php/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/clike/clike.min.js"></script>
</head>
<body class="bg-gray-50">
    
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">{{ $generation->name }}</h1>
                    <p class="text-blue-100 mt-1">{{ $generation->table_name }}</p>
                </div>
                <a href="{{ route('migration-generator.index') }}" 
                   class="bg-white text-blue-600 px-6 py-3 rounded-lg font-bold hover:bg-blue-50 transition-all">
                    ← العودة
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        
        <!-- Success Message -->
        @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
            <p class="font-bold">✅ نجح!</p>
            <p>{{ session('success') }}</p>
        </div>
        @endif

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="text-gray-600 text-sm mb-1">النوع</div>
                <div class="text-lg font-bold text-blue-600">{{ ucfirst($generation->migration_type) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="text-gray-600 text-sm mb-1">الحالة</div>
                <div class="text-lg font-bold text-green-600">{{ ucfirst($generation->status) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="text-gray-600 text-sm mb-1">طريقة الإدخال</div>
                <div class="text-lg font-bold text-purple-600">{{ $generation->input_method }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="text-gray-600 text-sm mb-1">تاريخ الإنشاء</div>
                <div class="text-lg font-bold text-gray-700">{{ $generation->created_at->format('Y-m-d') }}</div>
            </div>
        </div>

        <!-- Description -->
        @if($generation->description)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">📝 الوصف</h3>
            <p class="text-gray-700 whitespace-pre-line">{{ $generation->description }}</p>
        </div>
        @endif

        <!-- Generated Code -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">💻 الكود المولد</h3>
                <div class="flex gap-2">
                    <button onclick="copyCode()" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all text-sm">
                        📋 نسخ
                    </button>
                    <a href="{{ route('migration-generator.download', $generation->id) }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-all text-sm">
                        ⬇️ تحميل
                    </a>
                    <form action="{{ route('migration-generator.save-file', $generation->id) }}" 
                          method="POST" 
                          class="inline">
                        @csrf
                        <button type="submit" 
                                class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-all text-sm">
                            💾 حفظ كملف
                        </button>
                    </form>
                </div>
            </div>
            
            <form action="{{ route('migration-generator.update', $generation->id) }}" method="POST">
                @csrf
                @method('PUT')
                <textarea id="codeEditor" 
                          name="generated_content" 
                          class="w-full font-mono text-sm border border-gray-300 rounded-lg p-4"
                          rows="30">{{ $generation->generated_content }}</textarea>
                
                <div class="mt-4 flex justify-end">
                    <button type="submit" 
                            class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-all">
                        💾 حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>

        <!-- Input Data -->
        @if($generation->input_data)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">📊 البيانات المدخلة</h3>
            <pre class="bg-gray-100 rounded-lg p-4 overflow-x-auto text-sm">{{ json_encode($generation->input_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        @endif

        <!-- AI Suggestions -->
        @if($generation->ai_suggestions)
        <div class="bg-blue-50 border-r-4 border-blue-500 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-bold text-blue-800 mb-4">🤖 اقتراحات الذكاء الاصطناعي</h3>
            <pre class="text-sm text-blue-900">{{ json_encode($generation->ai_suggestions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        @endif

        <!-- Validation Results -->
        @if($generation->validation_results)
        <div class="bg-green-50 border-r-4 border-green-500 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-bold text-green-800 mb-4">✅ نتائج التحقق</h3>
            <pre class="text-sm text-green-900">{{ json_encode($generation->validation_results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        @endif

        <!-- Creator Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">👤 معلومات المنشئ</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-sm text-gray-600">أنشأ بواسطة</div>
                    <div class="font-medium">{{ $generation->creator->name ?? 'غير معروف' }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">آخر تحديث</div>
                    <div class="font-medium">{{ $generation->updated_at->format('Y-m-d H:i') }}</div>
                </div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm">🧬 Migration Generator v3.23.0</p>
        </div>
    </footer>

    <script>
        // Initialize CodeMirror
        const editor = CodeMirror.fromTextArea(document.getElementById('codeEditor'), {
            mode: 'application/x-httpd-php',
            theme: 'monokai',
            lineNumbers: true,
            indentUnit: 4,
            indentWithTabs: false,
            lineWrapping: true,
        });

        // Copy code function
        function copyCode() {
            const code = editor.getValue();
            navigator.clipboard.writeText(code).then(() => {
                alert('✅ تم نسخ الكود بنجاح!');
            });
        }
    </script>

</body>
</html>
