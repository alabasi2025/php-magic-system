<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🛡️ إنشاء Policy جديد - Policy Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
</head>
<body class="bg-gray-50" x-data="policyGenerator()">
    
    <!-- Header -->
    <header class="bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold flex items-center gap-2">
                        🛡️ إنشاء Policy جديد
                    </h1>
                    <p class="text-purple-100 mt-1">مولد Policies ذكي مدعوم بالذكاء الاصطناعي v3.31.0</p>
                </div>
                <a href="{{ route('policy-generator.index') }}" 
                   class="bg-white text-purple-600 px-6 py-3 rounded-lg font-bold hover:bg-purple-50 transition-all shadow-lg">
                    ← رجوع للقائمة
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Form Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">📝 معلومات Policy</h2>
                
                <!-- Name -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">اسم Policy *</label>
                    <input type="text" 
                           x-model="name"
                           placeholder="مثال: PostPolicy"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <p class="text-sm text-gray-500 mt-1">سيتم إضافة "Policy" تلقائياً إذا لم يكن موجوداً</p>
                </div>

                <!-- Model -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">النموذج (Model) *</label>
                    <input type="text" 
                           x-model="model"
                           placeholder="مثال: Post"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <p class="text-sm text-gray-500 mt-1">اسم النموذج المرتبط بهذا Policy</p>
                </div>

                <!-- Type -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">نوع Policy *</label>
                    <select x-model="type" 
                            @change="updateOptions()"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        @foreach($types as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Options for Resource -->
                <div x-show="type === 'resource'" class="mb-6 space-y-4 border-t pt-4">
                    <h3 class="font-bold text-gray-700">خيارات Resource Policy</h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" x-model="options.use_responses" class="rounded">
                            <span class="text-sm">استخدام Response objects (بدلاً من boolean)</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" x-model="options.include_filters" class="rounded">
                            <span class="text-sm">تضمين before() filter للمسؤولين</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" x-model="options.guest_support" class="rounded">
                            <span class="text-sm">دعم المستخدمين الضيوف (Guest Users)</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" x-model="options.soft_deletes" class="rounded">
                            <span class="text-sm">تضمين restore و forceDelete</span>
                        </label>
                    </div>
                </div>

                <!-- Options for Custom -->
                <div x-show="type === 'custom'" class="mb-6 space-y-4 border-t pt-4">
                    <h3 class="font-bold text-gray-700">خيارات Custom Policy</h3>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">الأساليب المطلوبة</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($standard_methods as $method)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="{{ $method }}" x-model="selectedMethods" class="rounded">
                                <span class="text-sm">{{ $method }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">وصف إضافي (اختياري)</label>
                        <textarea x-model="options.ai_description" 
                                  rows="4"
                                  placeholder="اكتب وصفاً تفصيلياً للوظيفة المطلوبة..."
                                  class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" x-model="options.use_responses" class="rounded">
                        <span class="text-sm">استخدام Response objects</span>
                    </label>
                </div>

                <!-- Options for Role-Based -->
                <div x-show="type === 'role_based'" class="mb-6 space-y-4 border-t pt-4">
                    <h3 class="font-bold text-gray-700">خيارات Role-Based Policy</h3>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">الأدوار (Roles)</label>
                        <input type="text" 
                               x-model="rolesInput" 
                               placeholder="مثال: admin,editor,viewer"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <p class="text-sm text-gray-500 mt-1">افصل بفاصلة للعديد من الأدوار</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">الصلاحيات (Permissions) - اختياري</label>
                        <input type="text" 
                               x-model="permissionsInput" 
                               placeholder="مثال: posts.create,posts.edit"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <p class="text-sm text-gray-500 mt-1">افصل بفاصلة للعديد من الصلاحيات</p>
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" x-model="options.use_responses" class="rounded">
                        <span class="text-sm">استخدام Response objects</span>
                    </label>
                </div>

                <!-- Options for Ownership -->
                <div x-show="type === 'ownership'" class="mb-6 space-y-4 border-t pt-4">
                    <h3 class="font-bold text-gray-700">خيارات Ownership Policy</h3>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">حقل الملكية</label>
                        <input type="text" 
                               x-model="options.ownership_field" 
                               placeholder="مثال: user_id"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <p class="text-sm text-gray-500 mt-1">الحقل الذي يحدد مالك المورد</p>
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" x-model="options.use_responses" class="rounded">
                            <span class="text-sm">استخدام Response objects</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" x-model="options.soft_deletes" class="rounded">
                            <span class="text-sm">تضمين restore و forceDelete</span>
                        </label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-4">
                    <button @click="preview()" 
                            :disabled="loading || !isValid()"
                            class="flex-1 bg-blue-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!loading">👁️ معاينة</span>
                        <span x-show="loading">⏳ جاري التوليد...</span>
                    </button>
                    <button @click="generate()" 
                            :disabled="loading || !isValid()"
                            class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-purple-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
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
                        ✅ تم حفظ Policy بنجاح!
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
        function policyGenerator() {
            return {
                name: '',
                model: '',
                type: 'resource',
                selectedMethods: ['view', 'update', 'delete'],
                rolesInput: 'admin,editor,viewer',
                permissionsInput: '',
                options: {
                    use_responses: true,
                    include_filters: false,
                    guest_support: false,
                    soft_deletes: false,
                    ownership_field: 'user_id',
                    ai_description: ''
                },
                code: '',
                generatedName: '',
                loading: false,
                saved: false,
                error: '',

                init() {
                    // تحميل النوع من URL إذا كان موجوداً
                    const urlParams = new URLSearchParams(window.location.search);
                    const typeParam = urlParams.get('type');
                    if (typeParam) {
                        this.type = typeParam;
                    }
                },

                isValid() {
                    return this.name.trim() !== '' && this.model.trim() !== '';
                },

                updateOptions() {
                    // إعادة تعيين الخيارات عند تغيير النوع
                    this.code = '';
                    this.error = '';
                    this.saved = false;
                },

                async preview() {
                    if (!this.isValid()) {
                        this.error = 'الرجاء ملء جميع الحقول المطلوبة';
                        return;
                    }

                    this.loading = true;
                    this.error = '';
                    this.saved = false;

                    try {
                        const response = await fetch('{{ route("policy-generator.preview") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.getPayload())
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.code = data.content;
                            this.generatedName = this.name + (this.name.endsWith('Policy') ? '' : 'Policy');
                            
                            // تطبيق syntax highlighting
                            this.$nextTick(() => {
                                document.querySelectorAll('pre code').forEach((block) => {
                                    hljs.highlightElement(block);
                                });
                            });
                        } else {
                            this.error = data.message || 'فشل توليد المعاينة';
                        }
                    } catch (err) {
                        this.error = 'خطأ في الاتصال: ' + err.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async generate() {
                    if (!this.isValid()) {
                        this.error = 'الرجاء ملء جميع الحقول المطلوبة';
                        return;
                    }

                    this.loading = true;
                    this.error = '';

                    try {
                        const response = await fetch('{{ route("policy-generator.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.getPayload())
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.code = ''; // سيتم جلبه من المعاينة
                            this.saved = true;
                            
                            // إعادة التوجيه بعد ثانيتين
                            setTimeout(() => {
                                window.location.href = '{{ route("policy-generator.index") }}';
                            }, 2000);
                        } else {
                            this.error = data.message || 'فشل توليد Policy';
                        }
                    } catch (err) {
                        this.error = 'خطأ في الاتصال: ' + err.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async save() {
                    await this.generate();
                },

                download() {
                    if (!this.code) return;

                    const blob = new Blob([this.code], { type: 'text/plain' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = this.generatedName + '.php';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                },

                copyCode() {
                    if (!this.code) return;

                    navigator.clipboard.writeText(this.code).then(() => {
                        alert('✅ تم نسخ الكود بنجاح!');
                    }).catch(err => {
                        alert('❌ فشل النسخ: ' + err.message);
                    });
                },

                getPayload() {
                    const payload = {
                        name: this.name,
                        model: this.model,
                        type: this.type,
                        ...this.options
                    };

                    // إضافة الأساليب للـ Custom
                    if (this.type === 'custom') {
                        payload.methods = this.selectedMethods;
                    }

                    // إضافة الأدوار والصلاحيات للـ Role-Based
                    if (this.type === 'role_based') {
                        payload.roles = this.rolesInput.split(',').map(r => r.trim()).filter(r => r);
                        payload.permissions = this.permissionsInput.split(',').map(p => p.trim()).filter(p => p);
                    }

                    return payload;
                }
            }
        }

        // تهيئة highlight.js
        hljs.highlightAll();
    </script>

</body>
</html>
