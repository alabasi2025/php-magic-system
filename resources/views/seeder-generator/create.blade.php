<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء Seeder جديد - Seeder Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
</head>
<body class="bg-gray-50">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('seeder-generator.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← العودة للقائمة
            </a>
            <h1 class="text-3xl font-bold text-gray-900">إنشاء Seeder جديد</h1>
            <p class="text-gray-600 mt-2">اختر طريقة التوليد المناسبة لك</p>
        </div>

        <!-- رسائل الخطأ -->
        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            ❌ {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- التبويبات -->
        <div x-data="{ activeTab: 'text' }" class="bg-white rounded-lg shadow">
            
            <!-- رؤوس التبويبات -->
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button @click="activeTab = 'text'" 
                            :class="activeTab === 'text' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="px-6 py-4 border-b-2 font-medium text-sm">
                        📝 من وصف نصي
                    </button>
                    <button @click="activeTab = 'json'" 
                            :class="activeTab === 'json' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="px-6 py-4 border-b-2 font-medium text-sm">
                        🔧 من JSON Schema
                    </button>
                    <button @click="activeTab = 'template'" 
                            :class="activeTab === 'template' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="px-6 py-4 border-b-2 font-medium text-sm">
                        📦 من قالب جاهز
                    </button>
                    <button @click="activeTab = 'reverse'" 
                            :class="activeTab === 'reverse' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="px-6 py-4 border-b-2 font-medium text-sm">
                        🔄 من جدول موجود
                    </button>
                </nav>
            </div>

            <!-- محتوى التبويبات -->
            <div class="p-6">
                
                <!-- تبويب: من وصف نصي -->
                <div x-show="activeTab === 'text'">
                    <form method="POST" action="{{ route('seeder-generator.generate.text') }}">
                        @csrf
                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">
                                الوصف النصي
                            </label>
                            <textarea name="description" rows="6" 
                                      class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                                      placeholder="مثال: أنشئ seeder لجدول المنتجات مع 100 منتج، كل منتج له اسم بالعربية، سعر بين 100 و 5000، وصف، وصورة"
                                      required>{{ old('description') }}</textarea>
                            <p class="text-gray-500 text-sm mt-2">
                                اكتب وصفاً واضحاً بالعربية أو الإنجليزية لما تريد توليده
                            </p>
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="use_ai" value="1" class="rounded">
                                <span class="mr-2">استخدام الذكاء الاصطناعي لتوليد بيانات واقعية</span>
                            </label>
                        </div>

                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
                            🚀 توليد Seeder
                        </button>
                    </form>
                </div>

                <!-- تبويب: من JSON Schema -->
                <div x-show="activeTab === 'json'">
                    <form method="POST" action="{{ route('seeder-generator.generate.json') }}">
                        @csrf
                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">
                                JSON Schema
                            </label>
                            <textarea id="json-editor" name="schema" rows="15" 
                                      class="w-full border rounded-lg px-4 py-2 font-mono text-sm"
                                      required>{
  "table_name": "products",
  "model_name": "Product",
  "count": 50,
  "locale": "ar_SA",
  "columns": {
    "name": {
      "type": "name"
    },
    "price": {
      "type": "price",
      "min": 100,
      "max": 5000
    },
    "description": {
      "type": "text",
      "length": 200
    },
    "image": {
      "type": "imageUrl"
    }
  }
}</textarea>
                            <p class="text-gray-500 text-sm mt-2">
                                أدخل JSON Schema كامل لتوليد Seeder دقيق
                            </p>
                        </div>

                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
                            🚀 توليد Seeder
                        </button>
                    </form>
                </div>

                <!-- تبويب: من قالب جاهز -->
                <div x-show="activeTab === 'template'">
                    <form method="POST" action="{{ route('seeder-generator.generate.template') }}">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">
                                اختر قالباً جاهزاً
                            </label>
                            <select name="template_id" class="w-full border rounded-lg px-4 py-2" required>
                                <option value="">-- اختر قالباً --</option>
                                @foreach($templates->groupBy('category') as $category => $categoryTemplates)
                                <optgroup label="{{ $categories[$category] ?? $category }}">
                                    @foreach($categoryTemplates as $template)
                                    <option value="{{ $template->id }}">
                                        {{ $template->name }} ({{ $template->default_count }} سجل)
                                    </option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">
                                عدد السجلات (اختياري)
                            </label>
                            <input type="number" name="count" min="1" max="10000" 
                                   class="w-full border rounded-lg px-4 py-2"
                                   placeholder="اترك فارغاً لاستخدام العدد الافتراضي">
                        </div>

                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
                            🚀 توليد Seeder
                        </button>
                    </form>
                </div>

                <!-- تبويب: من جدول موجود -->
                <div x-show="activeTab === 'reverse'">
                    <form method="POST" action="{{ route('seeder-generator.generate.reverse') }}">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">
                                اسم الجدول
                            </label>
                            <input type="text" name="table_name" 
                                   class="w-full border rounded-lg px-4 py-2"
                                   placeholder="مثال: users"
                                   required>
                            <p class="text-gray-500 text-sm mt-2">
                                أدخل اسم جدول موجود في قاعدة البيانات
                            </p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">
                                عدد السجلات
                            </label>
                            <input type="number" name="count" min="1" max="10000" value="10"
                                   class="w-full border rounded-lg px-4 py-2"
                                   required>
                        </div>

                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
                            🚀 توليد Seeder
                        </button>
                    </form>
                </div>

            </div>
        </div>

        <!-- معلومات مساعدة -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="font-semibold text-blue-900 mb-3">💡 نصائح سريعة</h3>
            <ul class="space-y-2 text-blue-800 text-sm">
                <li>• <strong>من وصف نصي:</strong> الطريقة الأسهل - اكتب ما تريد بالعربية</li>
                <li>• <strong>من JSON:</strong> للتحكم الكامل في كل التفاصيل</li>
                <li>• <strong>من قالب:</strong> الأسرع - استخدم قوالب جاهزة</li>
                <li>• <strong>من جدول:</strong> لتوليد بيانات لجداول موجودة</li>
            </ul>
        </div>

    </div>

    <script>
        // تفعيل CodeMirror للـ JSON
        if (document.getElementById('json-editor')) {
            CodeMirror.fromTextArea(document.getElementById('json-editor'), {
                mode: 'application/json',
                lineNumbers: true,
                theme: 'default',
                indentUnit: 2,
                tabSize: 2,
            });
        }
    </script>

</body>
</html>
