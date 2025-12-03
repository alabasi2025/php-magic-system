<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🛡️ إنشاء Middleware جديد - Middleware Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
</head>
<body class="bg-gray-50" x-data="middlewareGenerator()">
    
    <!-- Header -->
    <header class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold flex items-center gap-2">
                        🛡️ إنشاء Middleware جديد
                    </h1>
                    <p class="text-indigo-100 mt-1">مولد Middleware ذكي مدعوم بالذكاء الاصطناعي v3.28.0</p>
                </div>
                <a href="{{ route('middleware-generator.index') }}" 
                   class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-bold hover:bg-indigo-50 transition-all shadow-lg">
                    ← رجوع للقائمة
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Form Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">📝 معلومات Middleware</h2>
                
                <!-- Name -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">اسم Middleware *</label>
                    <input type="text" 
                           x-model="name"
                           placeholder="مثال: CheckUserSubscription"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-sm text-gray-500 mt-1">سيتم إضافة "Middleware" تلقائياً إذا لم يكن موجوداً</p>
                </div>

                <!-- Type -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">نوع Middleware *</label>
                    <select x-model="type" 
                            @change="updateOptions()"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($types as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Options for Authentication -->
                <div x-show="type === 'authentication'" class="mb-6 space-y-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Guard</label>
                        <select x-model="options.guard" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                            <option value="web">Web</option>
                            <option value="api">API</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Token Type</label>
                        <select x-model="options.token_type" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                            <option value="Bearer">Bearer</option>
                            <option value="API-Key">API-Key</option>
                            <option value="Custom">Custom</option>
                        </select>
                    </div>
                </div>

                <!-- Options for Authorization -->
                <div x-show="type === 'authorization'" class="mb-6 space-y-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Permission</label>
                        <input type="text" x-model="options.permission" placeholder="مثال: users.view" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Role</label>
                        <input type="text" x-model="options.role" placeholder="مثال: admin" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                </div>

                <!-- Options for Logging -->
                <div x-show="type === 'logging'" class="mb-6 space-y-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Log Channel</label>
                        <select x-model="options.log_channel" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                            <option value="daily">Daily</option>
                            <option value="single">Single</option>
                            <option value="stack">Stack</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Log Level</label>
                        <select x-model="options.log_level" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                            <option value="info">Info</option>
                            <option value="debug">Debug</option>
                            <option value="error">Error</option>
                        </select>
                    </div>
                </div>

                <!-- Options for Rate Limit -->
                <div x-show="type === 'rate_limit'" class="mb-6 space-y-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Max Attempts</label>
                        <input type="number" x-model="options.max_attempts" value="60" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Decay Minutes</label>
                        <input type="number" x-model="options.decay_minutes" value="1" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                </div>

                <!-- Options for CORS -->
                <div x-show="type === 'cors'" class="mb-6 space-y-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Allowed Origins</label>
                        <input type="text" x-model="options.allowed_origins" placeholder="*" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <p class="text-sm text-gray-500 mt-1">افصل بفاصلة للعديد من النطاقات</p>
                    </div>
                </div>

                <!-- Description for Custom -->
                <div x-show="type === 'custom'" class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">الوصف *</label>
                    <textarea x-model="description" 
                              rows="6"
                              placeholder="اكتب وصفاً تفصيلياً للوظيفة المطلوبة من Middleware..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <!-- Actions -->
                <div class="flex gap-4">
                    <button @click="preview()" 
                            :disabled="loading"
                            class="flex-1 bg-blue-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-600 transition-all disabled:opacity-50">
                        <span x-show="!loading">👁️ معاينة</span>
                        <span x-show="loading">⏳ جاري التوليد...</span>
                    </button>
                    <button @click="generate()" 
                            :disabled="loading"
                            class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-indigo-700 transition-all disabled:opacity-50">
                        <span x-show="!loading">✨ توليد وحفظ</span>
                        <span x-show="loading">⏳ جاري التوليد...</span>
                    </button>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">👁️ معاينة الكود</h2>
                
                <div x-show="!code" class="text-center py-12">
                    <div class="text-6xl mb-4">🛡️</div>
                    <p class="text-gray-500">اضغط على "معاينة" لرؤية الكود المولد</p>
                </div>

                <div x-show="code" class="space-y-4">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            <span class="font-bold" x-text="generatedName"></span>
                        </div>
                        <button @click="copyCode()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                            📋 نسخ
                        </button>
                    </div>
                    
                    <div class="bg-gray-900 rounded-lg overflow-hidden">
                        <pre class="p-4 overflow-x-auto"><code class="language-php" x-text="code"></code></pre>
                    </div>

                    <div x-show="!saved" class="flex gap-2">
                        <button @click="save()" class="flex-1 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            💾 حفظ
                        </button>
                        <button @click="download()" class="flex-1 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            📥 تحميل
                        </button>
                    </div>

                    <div x-show="saved" class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 rounded">
                        ✅ تم حفظ Middleware بنجاح!
                    </div>
                </div>

                <div x-show="error" class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 rounded mt-4">
                    <p class="font-bold">❌ خطأ!</p>
                    <p x-text="error"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function middlewareGenerator() {
            return {
                name: '',
                type: 'authentication',
                description: '',
                options: {
                    guard: 'web',
                    token_type: 'Bearer',
                    permission: '',
                    role: '',
                    log_channel: 'daily',
                    log_level: 'info',
                    max_attempts: 60,
                    decay_minutes: 1,
                    allowed_origins: '*'
                },
                code: '',
                generatedName: '',
                loading: false,
                saved: false,
                error: '',

                init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const typeParam = urlParams.get('type');
                    if (typeParam) {
                        this.type = typeParam;
                    }
                    hljs.highlightAll();
                },

                updateOptions() {
                    this.code = '';
                    this.saved = false;
                    this.error = '';
                },

                async preview() {
                    if (!this.validate()) return;

                    this.loading = true;
                    this.error = '';
                    this.saved = false;

                    try {
                        const response = await fetch('/middleware-generator/preview', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                name: this.name,
                                type: this.type,
                                description: this.description,
                                options: this.options
                            })
                        });

                        const data = await response.json();

                        if (data.status === 'success') {
                            this.code = data.code;
                            this.generatedName = data.name;
                            setTimeout(() => hljs.highlightAll(), 100);
                        } else {
                            this.error = data.message;
                        }
                    } catch (error) {
                        this.error = 'حدث خطأ في الاتصال: ' + error.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async generate() {
                    if (!this.validate()) return;

                    this.loading = true;
                    this.error = '';

                    try {
                        const response = await fetch('/middleware-generator/generate', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                name: this.name,
                                type: this.type,
                                description: this.description,
                                options: this.options
                            })
                        });

                        const data = await response.json();

                        if (data.status === 'success') {
                            this.saved = true;
                            alert('✅ تم توليد وحفظ Middleware بنجاح!');
                            setTimeout(() => {
                                window.location.href = '/middleware-generator';
                            }, 2000);
                        } else {
                            this.error = data.message;
                        }
                    } catch (error) {
                        this.error = 'حدث خطأ في الاتصال: ' + error.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async save() {
                    // Implementation for saving previewed code
                    alert('💾 سيتم حفظ الكود...');
                },

                download() {
                    const blob = new Blob([this.code], { type: 'text/plain' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = this.generatedName + '.php';
                    a.click();
                    window.URL.revokeObjectURL(url);
                },

                copyCode() {
                    navigator.clipboard.writeText(this.code);
                    alert('📋 تم نسخ الكود!');
                },

                validate() {
                    if (!this.name.trim()) {
                        this.error = 'الرجاء إدخال اسم Middleware';
                        return false;
                    }

                    if (this.type === 'custom' && !this.description.trim()) {
                        this.error = 'الرجاء إدخال وصف للـ Custom Middleware';
                        return false;
                    }

                    return true;
                }
            }
        }
    </script>
</body>
</html>
