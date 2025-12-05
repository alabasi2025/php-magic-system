@extends('layouts.app')

@section('title', 'مولد الأكواد بالذكاء الاصطناعي - SEMOP')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 pt-20 pb-10">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1 h-10 bg-gradient-to-b from-blue-400 to-blue-600 rounded"></div>
            <div>
                <h1 class="text-4xl font-bold text-white">
                    <i class="fas fa-robot text-blue-400 mr-3"></i>مولد الأكواد بالذكاء الاصطناعي
                </h1>
                <p class="text-gray-400 mt-2">توليد CRUD كامل في ثوان - من الوصف إلى الكود الجاهز</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Input Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-6">
                    <h2 class="text-xl font-bold text-white mb-6">
                        <i class="fas fa-pen text-blue-400 mr-2"></i>إدخال البيانات
                    </h2>

                    <form id="generatorForm" class="space-y-4">
                        @csrf

                        <!-- Generator Type -->
                        <div>
                            <label class="block text-white font-semibold mb-2">
                                <i class="fas fa-cube mr-2 text-blue-400"></i>نوع الإنشاء
                            </label>
                            <select id="generatorType" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:outline-none transition">
                                <option value="crud" selected>CRUD كامل</option>
                                <option value="migration">Migration فقط</option>
                                <option value="api-resource">API Resource</option>
                                <option value="tests">Unit Tests</option>
                            </select>
                        </div>

                        <!-- Model/Table Name -->
                        <div>
                            <label class="block text-white font-semibold mb-2">
                                <i class="fas fa-tag mr-2 text-green-400"></i>اسم النموذج
                            </label>
                            <input 
                                type="text" 
                                id="modelName" 
                                name="model_name"
                                placeholder="مثال: Product, Category, Order"
                                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:border-green-500 focus:outline-none transition"
                                required
                            >
                            <p class="text-xs text-gray-400 mt-1">يجب أن يبدأ بحرف كبير (PascalCase)</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-white font-semibold mb-2">
                                <i class="fas fa-align-left mr-2 text-purple-400"></i>وصف الميزة
                            </label>
                            <textarea 
                                id="description" 
                                name="description"
                                rows="6"
                                placeholder="صف الميزة بالتفصيل. مثال: نموذج المنتجات يحتوي على اسم، وصف، سعر، فئة، صور، وحالة النشر"
                                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:border-purple-500 focus:outline-none transition resize-none"
                                required
                            ></textarea>
                            <p class="text-xs text-gray-400 mt-1">كلما كان الوصف أكثر تفصيلاً، كانت النتيجة أفضل</p>
                        </div>

                        <!-- Fields (Optional) -->
                        <div>
                            <label class="block text-white font-semibold mb-2">
                                <i class="fas fa-list mr-2 text-yellow-400"></i>الحقول (اختياري)
                            </label>
                            <input 
                                type="text" 
                                id="fields" 
                                name="fields"
                                placeholder="name, email, phone (مفصولة بفواصل)"
                                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:border-yellow-500 focus:outline-none transition"
                            >
                            <p class="text-xs text-gray-400 mt-1">اتركها فارغة للكشف التلقائي من الوصف</p>
                        </div>

                        <!-- Auto Save -->
                        <div class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                id="autoSave" 
                                name="auto_save"
                                class="w-4 h-4 rounded border-white/20 bg-white/10 text-blue-500 focus:ring-2 focus:ring-blue-500"
                            >
                            <label for="autoSave" class="text-white text-sm">
                                حفظ الملفات تلقائياً بعد الإنشاء
                            </label>
                        </div>

                        <!-- Generate Button -->
                        <button 
                            type="submit"
                            id="generateBtn"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-bold py-3 rounded-lg transition transform hover:scale-105 flex items-center justify-center gap-2 mt-6"
                        >
                            <i class="fas fa-magic"></i>
                            <span>توليد الأكواد</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Output Panel -->
            <div class="lg:col-span-2">
                <!-- Tabs -->
                <div class="flex gap-2 mb-4 overflow-x-auto">
                    <button class="tab-btn active px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition" data-tab="preview">
                        <i class="fas fa-eye mr-2"></i>معاينة
                    </button>
                    <button class="tab-btn px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition border border-white/20" data-tab="code">
                        <i class="fas fa-code mr-2"></i>الكود
                    </button>
                    <button class="tab-btn px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition border border-white/20" data-tab="components">
                        <i class="fas fa-cube mr-2"></i>المكونات
                    </button>
                    <button class="tab-btn px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition border border-white/20" data-tab="logs">
                        <i class="fas fa-list mr-2"></i>السجلات
                    </button>
                </div>

                <!-- Output Content -->
                <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-6 min-h-96">
                    <!-- Preview Tab -->
                    <div id="preview-tab" class="tab-content">
                        <div id="previewContent" class="text-gray-400 text-center py-20">
                            <i class="fas fa-hourglass-start text-4xl text-gray-500 mb-4"></i>
                            <p>في انتظار إدخال البيانات والضغط على "توليد الأكواد"</p>
                        </div>
                    </div>

                    <!-- Code Tab -->
                    <div id="code-tab" class="tab-content hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-white font-semibold">الكود المولد</h3>
                            <button onclick="copyCode()" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition">
                                <i class="fas fa-copy mr-1"></i>نسخ
                            </button>
                        </div>
                        <pre id="codeContent" class="bg-black/50 rounded p-4 text-green-400 text-sm overflow-x-auto max-h-96">
// سيتم عرض الكود هنا
                        </pre>
                    </div>

                    <!-- Components Tab -->
                    <div id="components-tab" class="tab-content hidden">
                        <div id="componentsContent" class="space-y-3">
                            <div class="text-gray-400 text-center py-10">
                                <p>لم يتم إنشاء أي مكونات حتى الآن</p>
                            </div>
                        </div>
                    </div>

                    <!-- Logs Tab -->
                    <div id="logs-tab" class="tab-content hidden">
                        <div id="logsContent" class="space-y-2 max-h-96 overflow-y-auto">
                            <div class="text-gray-400 text-sm">جاهز للعمل...</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 mt-4">
                    <button onclick="downloadCode()" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-download"></i>تحميل
                    </button>
                    <button onclick="copyAllCode()" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-copy"></i>نسخ الكل
                    </button>
                    <button onclick="clearForm()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-trash"></i>مسح
                    </button>
                </div>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
            <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-bolt text-yellow-400 text-2xl"></i>
                    <div>
                        <p class="text-gray-400 text-sm">سرعة التطوير</p>
                        <p class="text-white font-bold">من ساعتين إلى 5 دقائق</p>
                    </div>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-400 text-2xl"></i>
                    <div>
                        <p class="text-gray-400 text-sm">جودة الكود</p>
                        <p class="text-white font-bold">متوافق مع أفضل الممارسات</p>
                    </div>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-robot text-blue-400 text-2xl"></i>
                    <div>
                        <p class="text-gray-400 text-sm">التكنولوجيا</p>
                        <p class="text-white font-bold">OpenAI GPT-4</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// State
let generatedCode = null;
let generatedComponents = null;

// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabName = this.dataset.tab;
        
        // Update active button
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active', 'bg-blue-600', 'hover:bg-blue-700'));
        this.classList.add('active', 'bg-blue-600', 'hover:bg-blue-700');
        
        // Update active content
        document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
        document.getElementById(tabName + '-tab').classList.remove('hidden');
    });
});

// Form submission
document.getElementById('generatorForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const type = document.getElementById('generatorType').value;
    const modelName = document.getElementById('modelName').value;
    const description = document.getElementById('description').value;
    const fields = document.getElementById('fields').value.split(',').filter(f => f.trim());
    const autoSave = document.getElementById('autoSave').checked;
    
    // Validation
    if (!modelName || !description) {
        showNotification('الرجاء ملء جميع الحقول المطلوبة', 'error');
        return;
    }
    
    // Disable button
    const btn = document.getElementById('generateBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner animate-spin"></i><span>جاري التوليد...</span>';
    
    addLog('جاري توليد الأكواد...');
    
    try {
        let response;
        
        if (type === 'crud') {
            response = await fetch('{{ route("developer.ai.code-generator.post") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    description,
                    model_name: modelName,
                    fields,
                    auto_save: autoSave
                })
            });
        } else if (type === 'migration') {
            response = await fetch('{{ route("developer.ai.database-designer.post") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    table_name: modelName.toLowerCase(),
                    description,
                    fields
                })
            });
        } else if (type === 'api-resource') {
            response = await fetch('{{ route("developer.ai.code-generator.post") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    resource_name: modelName + 'Resource',
                    fields
                })
            });
        } else if (type === 'tests') {
            response = await fetch('{{ route("developer.ai.test-generator.post") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    model_name: modelName,
                    description
                })
            });
        }
        
        const data = await response.json();
        
        if (data.success) {
            generatedCode = data.code;
            generatedComponents = data.components || {};
            
            displayPreview(data);
            displayCode(data.code);
            displayComponents(data.components || {});
            
            addLog('✅ تم توليد الأكواد بنجاح!');
            showNotification('تم توليد الأكواد بنجاح!', 'success');
            
            if (data.save_result) {
                addLog('✅ تم حفظ الملفات بنجاح!');
            }
        } else {
            addLog('❌ خطأ: ' + data.message);
            showNotification(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        addLog('❌ خطأ في الاتصال: ' + error.message);
        showNotification('حدث خطأ في الاتصال', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-magic"></i><span>توليد الأكواد</span>';
    }
});

function displayPreview(data) {
    const preview = document.getElementById('previewContent');
    preview.innerHTML = `
        <div class="text-right">
            <h3 class="text-white font-bold text-lg mb-4">✅ تم توليد الأكواد بنجاح</h3>
            <div class="grid grid-cols-2 gap-4 text-left">
                <div class="bg-white/5 rounded p-3">
                    <p class="text-gray-400 text-sm">النوع</p>
                    <p class="text-white font-semibold">${data.model_name || data.table_name || 'N/A'}</p>
                </div>
                <div class="bg-white/5 rounded p-3">
                    <p class="text-gray-400 text-sm">الحالة</p>
                    <p class="text-green-400 font-semibold">جاهز للاستخدام</p>
                </div>
            </div>
            <p class="text-gray-400 text-sm mt-4">${data.message}</p>
        </div>
    `;
}

function displayCode(code) {
    document.getElementById('codeContent').textContent = code;
}

function displayComponents(components) {
    const content = document.getElementById('componentsContent');
    if (Object.keys(components).length === 0) {
        content.innerHTML = '<div class="text-gray-400 text-center py-10"><p>لم يتم العثور على مكونات</p></div>';
        return;
    }
    
    content.innerHTML = Object.entries(components).map(([key, value]) => `
        <div class="bg-white/5 rounded p-3">
            <p class="text-blue-400 font-semibold text-sm mb-2">${key}</p>
            <p class="text-gray-400 text-xs line-clamp-3">${value ? value.substring(0, 100) + '...' : 'N/A'}</p>
        </div>
    `).join('');
}

function addLog(message) {
    const logs = document.getElementById('logsContent');
    const timestamp = new Date().toLocaleTimeString('ar-SA');
    const logEntry = document.createElement('div');
    logEntry.className = 'text-gray-400 text-sm border-l-2 border-blue-500 pl-3 py-1';
    logEntry.textContent = `[${timestamp}] ${message}`;
    logs.appendChild(logEntry);
    logs.scrollTop = logs.scrollHeight;
}

function copyCode() {
    if (!generatedCode) {
        showNotification('لا يوجد كود للنسخ', 'error');
        return;
    }
    navigator.clipboard.writeText(generatedCode);
    showNotification('تم نسخ الكود', 'success');
}

function copyAllCode() {
    copyCode();
}

function downloadCode() {
    if (!generatedCode) {
        showNotification('لا يوجد كود للتحميل', 'error');
        return;
    }
    
    const element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(generatedCode));
    element.setAttribute('download', 'generated-code.php');
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
    showNotification('تم تحميل الكود', 'success');
}

function clearForm() {
    document.getElementById('generatorForm').reset();
    document.getElementById('previewContent').innerHTML = `
        <i class="fas fa-hourglass-start text-4xl text-gray-500 mb-4"></i>
        <p>في انتظار إدخال البيانات والضغط على "توليد الأكواد"</p>
    `;
    generatedCode = null;
    generatedComponents = null;
    showNotification('تم مسح النموذج', 'success');
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-20 right-4 px-6 py-4 rounded-lg text-white z-50 animate-slide-in-left ${
        type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

@endsection
