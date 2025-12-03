<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء Migration جديد - Migration Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
</head>
<body class="bg-gray-50">
    
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">➕ إنشاء Migration جديد</h1>
                    <p class="text-blue-100 mt-1">اختر الطريقة المناسبة لتوليد الـ Migration</p>
                </div>
                <a href="{{ route('migration-generator.index') }}" 
                   class="bg-white text-blue-600 px-6 py-3 rounded-lg font-bold hover:bg-blue-50 transition-all">
                    ← العودة
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8" x-data="{ activeTab: 'text' }">
        
        <!-- Errors -->
        @if($errors->any())
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
            <p class="font-bold">❌ خطأ!</p>
            <ul class="list-disc list-inside mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button @click="activeTab = 'text'" 
                            :class="activeTab === 'text' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-6 border-b-2 font-medium text-sm transition-all">
                        📝 وصف نصي
                    </button>
                    <button @click="activeTab = 'json'" 
                            :class="activeTab === 'json' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-6 border-b-2 font-medium text-sm transition-all">
                        📋 JSON Schema
                    </button>
                    <button @click="activeTab = 'template'" 
                            :class="activeTab === 'template' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-6 border-b-2 font-medium text-sm transition-all">
                        🎨 من قالب
                    </button>
                </nav>
            </div>

            <!-- Tab Content: Text Description -->
            <div x-show="activeTab === 'text'" class="p-6">
                <form action="{{ route('migration-generator.generate-text') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">
                            📝 صف الجدول الذي تريد إنشاءه
                        </label>
                        <p class="text-sm text-gray-600 mb-4">
                            اكتب وصفاً بالعربية أو الإنجليزية، وسيقوم الذكاء الاصطناعي بتحليله وتوليد الـ Migration
                        </p>
                        <textarea name="description" 
                                  rows="12" 
                                  class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="مثال:

أريد إنشاء جدول للمنتجات يحتوي على:
- اسم المنتج
- السعر
- الكمية المتوفرة
- الوصف
- صورة المنتج
- علاقة مع جدول الفئات

يجب أن يكون السعر decimal
والكمية integer
والوصف نص طويل">{{ old('description') }}</textarea>
                    </div>

                    <div class="bg-blue-50 border-r-4 border-blue-500 p-4 rounded-lg mb-6">
                        <p class="text-sm text-blue-800">
                            💡 <strong>نصيحة:</strong> كلما كان الوصف أكثر تفصيلاً، كان الـ Migration أفضل وأدق!
                        </p>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 rounded-lg font-bold hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg">
                        🚀 توليد Migration
                    </button>
                </form>
            </div>

            <!-- Tab Content: JSON Schema -->
            <div x-show="activeTab === 'json'" class="p-6">
                <form action="{{ route('migration-generator.generate-json') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">
                            📋 JSON Schema
                        </label>
                        <p class="text-sm text-gray-600 mb-4">
                            أدخل JSON Schema كامل للجدول
                        </p>
                        <textarea name="json_schema" 
                                  id="jsonEditor"
                                  rows="20" 
                                  class="w-full border border-gray-300 rounded-lg px-4 py-3 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('json_schema', '{
  "table_name": "products",
  "description": "جدول المنتجات",
  "type": "create",
  "columns": [
    {
      "name": "name",
      "type": "string",
      "length": 255,
      "nullable": false,
      "comment": "اسم المنتج"
    },
    {
      "name": "price",
      "type": "decimal",
      "precision": 10,
      "scale": 2,
      "comment": "السعر"
    },
    {
      "name": "category_id",
      "type": "foreignId",
      "references": "categories",
      "onDelete": "cascade",
      "comment": "الفئة"
    }
  ],
  "indexes": [
    {
      "columns": ["name"],
      "unique": false
    }
  ]
}') }}</textarea>
                    </div>

                    <div class="bg-yellow-50 border-r-4 border-yellow-500 p-4 rounded-lg mb-6">
                        <p class="text-sm text-yellow-800">
                            ⚠️ <strong>تنبيه:</strong> تأكد من صحة JSON قبل الإرسال!
                        </p>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-green-600 to-teal-600 text-white py-4 rounded-lg font-bold hover:from-green-700 hover:to-teal-700 transition-all shadow-lg">
                        🚀 توليد من JSON
                    </button>
                </form>
            </div>

            <!-- Tab Content: Template -->
            <div x-show="activeTab === 'template'" class="p-6">
                <form action="{{ route('migration-generator.generate-template') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">
                            🎨 اختر قالباً جاهزاً
                        </label>
                        <select name="template_id" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- اختر قالباً --</option>
                            @foreach($templates as $template)
                            <option value="{{ $template->id }}">
                                {{ $template->name }} ({{ $template->category }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">
                            🔧 المتغيرات
                        </label>
                        <div class="space-y-3">
                            <input type="text" 
                                   name="variables[table_name]" 
                                   placeholder="اسم الجدول"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="text" 
                                   name="variables[name]" 
                                   placeholder="اسم الـ Migration"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="bg-purple-50 border-r-4 border-purple-500 p-4 rounded-lg mb-6">
                        <p class="text-sm text-purple-800">
                            🎨 <strong>القوالب:</strong> استخدم القوالب الجاهزة لتسريع التطوير!
                        </p>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-4 rounded-lg font-bold hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg">
                        🚀 توليد من القالب
                    </button>
                </form>
            </div>
        </div>

        <!-- Examples Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">📚 أمثلة ونماذج</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition-all cursor-pointer">
                    <h4 class="font-bold text-gray-800 mb-2">🛒 جدول منتجات</h4>
                    <p class="text-sm text-gray-600">جدول بسيط للمنتجات مع علاقات</p>
                </div>
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition-all cursor-pointer">
                    <h4 class="font-bold text-gray-800 mb-2">👥 جدول مستخدمين</h4>
                    <p class="text-sm text-gray-600">جدول متقدم مع مصادقة</p>
                </div>
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition-all cursor-pointer">
                    <h4 class="font-bold text-gray-800 mb-2">💰 جدول محاسبي</h4>
                    <p class="text-sm text-gray-600">جدول معقد للمحاسبة</p>
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

</body>
</html>
