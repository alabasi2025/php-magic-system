<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $seeder->name }} - Seeder Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/php/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/clike/clike.min.js"></script>
</head>
<body class="bg-gray-50">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('seeder-generator.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← العودة للقائمة
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $seeder->name }}</h1>
                    <p class="text-gray-600 mt-2">{{ $seeder->description }}</p>
                </div>
                <div class="flex space-x-3 space-x-reverse">
                    <a href="{{ route('seeder-generator.download', $seeder->id) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        📥 تحميل
                    </a>
                    <form action="{{ route('seeder-generator.save-file', $seeder->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            💾 حفظ كملف
                        </button>
                    </form>
                    @if($seeder->status !== 'executed')
                    <form action="{{ route('seeder-generator.execute', $seeder->id) }}" method="POST" class="inline"
                          onsubmit="return confirm('هل أنت متأكد من تنفيذ الـ Seeder؟')">
                        @csrf
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                            🚀 تنفيذ
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- العمود الأيمن: المعلومات -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- معلومات أساسية -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">📊 معلومات أساسية</h2>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600 text-sm">اسم الجدول:</span>
                            <code class="block bg-gray-100 px-2 py-1 rounded mt-1">{{ $seeder->table_name }}</code>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">اسم الـ Model:</span>
                            <code class="block bg-gray-100 px-2 py-1 rounded mt-1">{{ $seeder->model_name }}</code>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">عدد السجلات:</span>
                            <div class="font-bold text-lg mt-1">{{ number_format($seeder->count) }}</div>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">طريقة الإدخال:</span>
                            <span class="block px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 mt-1 inline-block">
                                {{ $seeder->input_method }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">الحالة:</span>
                            <div class="mt-1">
                                @if($seeder->status == 'draft')
                                    <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">مسودة</span>
                                @elseif($seeder->status == 'generated')
                                    <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">تم التوليد</span>
                                @elseif($seeder->status == 'executed')
                                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">تم التنفيذ</span>
                                @elseif($seeder->status == 'failed')
                                    <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">فشل</span>
                                @endif
                            </div>
                        </div>
                        @if($seeder->use_ai)
                        <div>
                            <span class="text-gray-600 text-sm">الذكاء الاصطناعي:</span>
                            <div class="mt-1">
                                <span class="px-2 py-1 text-xs rounded bg-purple-100 text-purple-800">
                                    🤖 {{ $seeder->ai_provider ?? 'OpenAI' }}
                                </span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- إحصائيات التنفيذ -->
                @if($seeder->isExecuted())
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">📈 إحصائيات التنفيذ</h2>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600 text-sm">السجلات المنشأة:</span>
                            <div class="font-bold text-lg text-green-600 mt-1">
                                {{ number_format($seeder->records_created) }}
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">وقت التنفيذ:</span>
                            <div class="font-bold text-lg mt-1">
                                {{ number_format($seeder->execution_time, 2) }}s
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- رسالة الخطأ -->
                @if($seeder->isFailed() && $seeder->error_message)
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-red-900 mb-4">❌ رسالة الخطأ</h2>
                    <pre class="text-sm text-red-700 whitespace-pre-wrap">{{ $seeder->error_message }}</pre>
                </div>
                @endif

                <!-- معلومات إضافية -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">ℹ️ معلومات إضافية</h2>
                    <div class="space-y-2 text-sm">
                        <div>
                            <span class="text-gray-600">تاريخ الإنشاء:</span>
                            <div class="mt-1">{{ $seeder->created_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                        <div>
                            <span class="text-gray-600">آخر تحديث:</span>
                            <div class="mt-1">{{ $seeder->updated_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                        @if($seeder->creator)
                        <div>
                            <span class="text-gray-600">المنشئ:</span>
                            <div class="mt-1">{{ $seeder->creator->name }}</div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- العمود الأيسر: الكود -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold">💻 كود الـ Seeder</h2>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('seeder-generator.update', $seeder->id) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">الاسم</label>
                                <input type="text" name="name" value="{{ $seeder->name }}" 
                                       class="w-full border rounded px-4 py-2">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">الوصف</label>
                                <textarea name="description" rows="2" 
                                          class="w-full border rounded px-4 py-2">{{ $seeder->description }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">الكود</label>
                                <textarea id="code-editor" name="generated_content" rows="20" 
                                          class="w-full border rounded px-4 py-2 font-mono text-sm">{{ $seeder->generated_content }}</textarea>
                            </div>

                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
                                💾 حفظ التعديلات
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script>
        // تفعيل CodeMirror
        if (document.getElementById('code-editor')) {
            CodeMirror.fromTextArea(document.getElementById('code-editor'), {
                mode: 'application/x-httpd-php',
                lineNumbers: true,
                theme: 'monokai',
                indentUnit: 4,
                tabSize: 4,
                lineWrapping: true,
            });
        }
    </script>

</body>
</html>
