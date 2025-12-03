<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏭 إنشاء Factory جديد</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50" x-data="{ activeTab: 'text' }">
    
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        🏭 إنشاء Factory جديد
                    </h1>
                    <p class="text-gray-600 mt-1">اختر طريقة التوليد المناسبة</p>
                </div>
                <a href="{{ route('factory-generator.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    ← رجوع
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- رسائل الخطأ -->
        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            ❌ {{ session('error') }}
        </div>
        @endif

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="border-b">
                <nav class="flex -mb-px">
                    <button @click="activeTab = 'text'" 
                            :class="activeTab === 'text' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-6 border-b-2 font-medium text-sm">
                        📝 من وصف نصي
                    </button>
                    <button @click="activeTab = 'json'" 
                            :class="activeTab === 'json' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-6 border-b-2 font-medium text-sm">
                        📋 من JSON Schema
                    </button>
                    <button @click="activeTab = 'template'" 
                            :class="activeTab === 'template' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-6 border-b-2 font-medium text-sm">
                        📦 من قالب جاهز
                    </button>
                    <button @click="activeTab = 'model'" 
                            :class="activeTab === 'model' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-6 border-b-2 font-medium text-sm">
                        🔄 من Model موجود
                    </button>
                </nav>
            </div>

            <!-- Tab Content: Text -->
            <div x-show="activeTab === 'text'" class="p-6">
                <h2 class="text-xl font-bold mb-4">📝 توليد Factory من وصف نصي</h2>
                <p class="text-gray-600 mb-6">اكتب وصفاً للـ Factory الذي تريد إنشاءه بلغة طبيعية</p>
                
                <form action="{{ route('factory-generator.generate.text') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">الوصف النصي</label>
                        <textarea name="description" rows="8" required
                                  class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"
                                  placeholder="مثال:&#10;&#10;أريد factory لموديل Product&#10;يحتوي على:&#10;- اسم المنتج&#10;- السعر&#10;- الوصف&#10;- الصورة&#10;- الكمية المتوفرة&#10;- SKU"></textarea>
                    </div>
                    
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                        🚀 توليد Factory
                    </button>
                </form>
            </div>

            <!-- Tab Content: JSON -->
            <div x-show="activeTab === 'json'" class="p-6">
                <h2 class="text-xl font-bold mb-4">📋 توليد Factory من JSON Schema</h2>
                <p class="text-gray-600 mb-6">أدخل JSON Schema محدد للحقول والأنواع</p>
                
                <form action="{{ route('factory-generator.generate.json') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">JSON Schema</label>
                        <textarea name="json_schema" rows="12" required
                                  class="w-full border rounded-lg px-4 py-3 font-mono text-sm focus:ring-2 focus:ring-blue-500"
                                  placeholder='{
  "model_name": "Product",
  "table_name": "products",
  "description": "مصنع المنتجات",
  "fields": {
    "name": {
      "faker": "words(3, true)"
    },
    "price": {
      "faker": "randomFloat(2, 10, 1000)"
    },
    "description": {
      "faker": "text(200)"
    },
    "image": {
      "faker": "imageUrl(640, 480)"
    },
    "quantity": {
      "faker": "numberBetween(1, 100)"
    },
    "sku": {
      "faker": "unique()->bothify(\"???-####\")"
    }
  }
}'></textarea>
                    </div>
                    
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                        🚀 توليد Factory
                    </button>
                </form>
            </div>

            <!-- Tab Content: Template -->
            <div x-show="activeTab === 'template'" class="p-6">
                <h2 class="text-xl font-bold mb-4">📦 توليد Factory من قالب جاهز</h2>
                <p class="text-gray-600 mb-6">اختر قالباً جاهزاً وخصصه حسب احتياجك</p>
                
                <form action="{{ route('factory-generator.generate.template') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">اختر القالب</label>
                        <select name="template_id" required
                                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- اختر قالباً --</option>
                            @foreach($templates as $template)
                            <option value="{{ $template->id }}">
                                {{ $template->name }} ({{ $template->category }}) - استخدم {{ $template->usage_count }} مرة
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">اسم الـ Model</label>
                        <input type="text" name="model_name" required
                               class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"
                               placeholder="مثال: Product">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">اسم الجدول (اختياري)</label>
                        <input type="text" name="table_name"
                               class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"
                               placeholder="مثال: products">
                    </div>
                    
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                        🚀 توليد Factory
                    </button>
                </form>
            </div>

            <!-- Tab Content: Model -->
            <div x-show="activeTab === 'model'" class="p-6">
                <h2 class="text-xl font-bold mb-4">🔄 توليد Factory من Model موجود</h2>
                <p class="text-gray-600 mb-6">أدخل اسم Model موجود لتوليد Factory تلقائياً من بنية الجدول</p>
                
                <form action="{{ route('factory-generator.generate.model') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">اسم الـ Model</label>
                        <input type="text" name="model_name" required
                               class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"
                               placeholder="مثال: Product">
                        <p class="text-sm text-gray-500 mt-2">
                            💡 سيتم قراءة بنية الجدول تلقائياً من قاعدة البيانات
                        </p>
                    </div>
                    
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                        🚀 توليد Factory
                    </button>
                </form>
            </div>
        </div>

        <!-- نصائح وإرشادات -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-3">💡 نصائح للاستخدام الأمثل</h3>
            <ul class="space-y-2 text-blue-800">
                <li>✅ استخدم الوصف النصي للتوليد السريع والبسيط</li>
                <li>✅ استخدم JSON Schema للتحكم الدقيق في أنواع البيانات</li>
                <li>✅ استخدم القوالب الجاهزة لتوفير الوقت</li>
                <li>✅ استخدم Reverse Engineering لتوليد Factory من جدول موجود</li>
                <li>✅ يمكنك تعديل الكود المولد قبل حفظه</li>
            </ul>
        </div>
    </div>

</body>
</html>
