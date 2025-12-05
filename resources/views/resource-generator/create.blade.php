<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إنشاء Resource جديد - Resource Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">📦 إنشاء Resource جديد</h1>
                    <p class="text-blue-100 mt-1">مولد API Resources ذكي v3.30.0</p>
                </div>
                <a href="{{ route('resource-generator.index') }}" 
                   class="bg-white text-blue-600 px-6 py-3 rounded-lg font-bold hover:bg-blue-50 transition-all">
                    ← العودة للقائمة
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8" x-data="resourceGenerator()">
        
        @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
            <p class="font-bold">❌ خطأ!</p>
            <p>{{ session('error') }}</p>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
            <p class="font-bold">❌ أخطاء في النموذج:</p>
            <ul class="list-disc list-inside mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('resource-generator.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">📋 معلومات أساسية</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Resource Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            اسم الـ Resource <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               x-model="formData.name"
                               placeholder="UserResource"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        <p class="text-xs text-gray-500 mt-1">مثال: UserResource, ProductResource</p>
                    </div>

                    <!-- Resource Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            نوع الـ Resource <span class="text-red-500">*</span>
                        </label>
                        <select name="type" 
                                x-model="formData.type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                            <option value="single">📄 Single Resource - تنسيق عنصر واحد</option>
                            <option value="collection">📚 Collection Resource - تنسيق مجموعة</option>
                            <option value="nested">🔗 Nested Resource - مع العلاقات</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Model Selection -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">🎯 اختيار Model</h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Model (اختياري)
                    </label>
                    <select name="model" 
                            x-model="formData.model"
                            @change="loadModelAttributes()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- اختر Model --</option>
                        @foreach($models as $model)
                        <option value="{{ $model }}">{{ $model }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">سيتم تحليل الخصائص تلقائياً من Model</p>
                </div>
            </div>

            <!-- Attributes -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">📝 الخصائص (Attributes)</h2>
                
                <div x-show="modelAttributes.length > 0" class="mb-4">
                    <p class="text-sm text-gray-600 mb-2">خصائص Model المكتشفة:</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="attr in modelAttributes" :key="attr">
                            <label class="inline-flex items-center bg-blue-50 px-3 py-1 rounded-full cursor-pointer hover:bg-blue-100">
                                <input type="checkbox" 
                                       name="attributes[]" 
                                       :value="attr"
                                       class="ml-2">
                                <span class="text-sm" x-text="attr"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        أو أدخل الخصائص يدوياً (مفصولة بفاصلة)
                    </label>
                    <input type="text" 
                           x-model="manualAttributes"
                           @input="updateAttributes()"
                           placeholder="id, name, email, created_at"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <!-- Relations -->
            <div class="bg-white rounded-lg shadow-md p-6" x-show="formData.type === 'nested' || formData.type === 'single'">
                <h2 class="text-xl font-bold text-gray-800 mb-4">🔗 العلاقات (Relations)</h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        العلاقات (مفصولة بفاصلة)
                    </label>
                    <input type="text" 
                           x-model="manualRelations"
                           @input="updateRelations()"
                           placeholder="posts, comments, profile"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">مثال: posts, comments (للمجموعات) أو profile (للعنصر الواحد)</p>
                </div>
            </div>

            <!-- AI Options -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">🤖 خيارات الذكاء الاصطناعي</h2>
                
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" 
                           name="use_ai" 
                           value="1"
                           x-model="formData.use_ai"
                           class="ml-2 w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                    <span class="font-medium">استخدام الذكاء الاصطناعي للتوليد</span>
                </label>
                <p class="text-sm text-gray-600 mt-2">سيقوم الذكاء الاصطناعي بتوليد Resource محسّن مع أفضل الممارسات</p>
            </div>

            <!-- Actions -->
            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition-all shadow-lg">
                    ✨ توليد Resource
                </button>
                <a href="{{ route('resource-generator.index') }}" 
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300 transition-all">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <script>
        function resourceGenerator() {
            return {
                formData: {
                    name: '{{ old("name", request("name", "")) }}',
                    type: '{{ old("type", request("type", "single")) }}',
                    model: '{{ old("model", "") }}',
                    use_ai: {{ old('use_ai', 0) ? 'true' : 'false' }}
                },
                modelAttributes: [],
                manualAttributes: '',
                manualRelations: '',

                async loadModelAttributes() {
                    if (!this.formData.model) {
                        this.modelAttributes = [];
                        return;
                    }

                    try {
                        const response = await fetch('/resource-generator/model-attributes?model=' + this.formData.model, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.modelAttributes = data.attributes;
                        }
                    } catch (error) {
                        console.error('Error loading model attributes:', error);
                    }
                },

                updateAttributes() {
                    // This is handled by manual input
                },

                updateRelations() {
                    // This is handled by manual input
                }
            }
        }
    </script>

</body>
</html>
