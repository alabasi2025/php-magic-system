@extends('layouts.app')

@section('title', 'أدوات الذكاء الاصطناعي المساعدة - SEMOP')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 pt-20 pb-10">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1 h-10 bg-gradient-to-b from-pink-400 to-pink-600 rounded"></div>
            <div>
                <h1 class="text-4xl font-bold text-white">
                    <i class="fas fa-sparkles text-pink-400 mr-3"></i>أدوات الذكاء الاصطناعي المساعدة
                </h1>
                <p class="text-gray-400 mt-2">مراجعة الأكواد، إصلاح الأخطاء، توليد الاختبارات، والتوثيق</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Tool Selector -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <button onclick="selectTool('review')" class="tool-btn active px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition border-2 border-blue-500" data-tool="review">
                <i class="fas fa-check-double text-xl mr-2"></i>مراجعة الأكواد
            </button>
            <button onclick="selectTool('bugfix')" class="tool-btn px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-lg transition border-2 border-white/20" data-tool="bugfix">
                <i class="fas fa-bug text-xl mr-2"></i>إصلاح الأخطاء
            </button>
            <button onclick="selectTool('tests')" class="tool-btn px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-lg transition border-2 border-white/20" data-tool="tests">
                <i class="fas fa-flask text-xl mr-2"></i>توليد الاختبارات
            </button>
            <button onclick="selectTool('docs')" class="tool-btn px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-lg transition border-2 border-white/20" data-tool="docs">
                <i class="fas fa-book text-xl mr-2"></i>التوثيق
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Input Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-6">
                    <h2 class="text-xl font-bold text-white mb-6">
                        <i class="fas fa-pen text-blue-400 mr-2"></i>إدخال البيانات
                    </h2>

                    <form id="toolForm" class="space-y-4">
                        @csrf

                        <!-- Code Input -->
                        <div>
                            <label class="block text-white font-semibold mb-2">
                                <i class="fas fa-code mr-2 text-green-400"></i>الكود
                            </label>
                            <textarea 
                                id="codeInput" 
                                name="code"
                                rows="8"
                                placeholder="الصق الكود هنا..."
                                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:border-green-500 focus:outline-none transition resize-none font-mono text-sm"
                                required
                            ></textarea>
                        </div>

                        <!-- Error Message (for bugfix) -->
                        <div id="errorDiv" class="hidden">
                            <label class="block text-white font-semibold mb-2">
                                <i class="fas fa-exclamation-triangle mr-2 text-red-400"></i>رسالة الخطأ
                            </label>
                            <textarea 
                                id="errorInput" 
                                name="error_message"
                                rows="4"
                                placeholder="الصق رسالة الخطأ هنا..."
                                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:border-red-500 focus:outline-none transition resize-none font-mono text-sm"
                            ></textarea>
                        </div>

                        <!-- Description (for tests) -->
                        <div id="descDiv" class="hidden">
                            <label class="block text-white font-semibold mb-2">
                                <i class="fas fa-align-left mr-2 text-purple-400"></i>الوصف (اختياري)
                            </label>
                            <textarea 
                                id="descInput" 
                                name="description"
                                rows="4"
                                placeholder="صف ما تريد اختباره..."
                                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:border-purple-500 focus:outline-none transition resize-none"
                            ></textarea>
                        </div>

                        <!-- Process Button -->
                        <button 
                            type="submit"
                            id="processBtn"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-bold py-3 rounded-lg transition transform hover:scale-105 flex items-center justify-center gap-2 mt-6"
                        >
                            <i class="fas fa-magic"></i>
                            <span id="processText">معالجة</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Output Panel -->
            <div class="lg:col-span-2">
                <!-- Tabs -->
                <div class="flex gap-2 mb-4 overflow-x-auto">
                    <button class="tab-btn active px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition" data-tab="result">
                        <i class="fas fa-eye mr-2"></i>النتيجة
                    </button>
                    <button class="tab-btn px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition border border-white/20" data-tab="raw">
                        <i class="fas fa-code mr-2"></i>الكود الخام
                    </button>
                    <button class="tab-btn px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition border border-white/20" data-tab="logs">
                        <i class="fas fa-list mr-2"></i>السجلات
                    </button>
                </div>

                <!-- Output Content -->
                <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-6 min-h-96">
                    <!-- Result Tab -->
                    <div id="result-tab" class="tab-content">
                        <div id="resultContent" class="text-gray-400 text-center py-20">
                            <i class="fas fa-hourglass-start text-4xl text-gray-500 mb-4"></i>
                            <p>في انتظار إدخال البيانات والضغط على "معالجة"</p>
                        </div>
                    </div>

                    <!-- Raw Tab -->
                    <div id="raw-tab" class="tab-content hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-white font-semibold">الكود الخام</h3>
                            <button onclick="copyRaw()" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition">
                                <i class="fas fa-copy mr-1"></i>نسخ
                            </button>
                        </div>
                        <pre id="rawContent" class="bg-black/50 rounded p-4 text-green-400 text-sm overflow-x-auto max-h-96">
// سيتم عرض النتيجة هنا
                        </pre>
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
                    <button onclick="downloadResult()" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-download"></i>تحميل
                    </button>
                    <button onclick="copyResult()" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-copy"></i>نسخ
                    </button>
                    <button onclick="clearForm()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-trash"></i>مسح
                    </button>
                </div>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
            <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-blue-400 text-2xl"></i>
                    <div>
                        <p class="text-gray-400 text-sm">مراجعة الأكواد</p>
                        <p class="text-white font-bold text-sm">تقييم شامل</p>
                    </div>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-bug text-red-400 text-2xl"></i>
                    <div>
                        <p class="text-gray-400 text-sm">إصلاح الأخطاء</p>
                        <p class="text-white font-bold text-sm">حلول فعالة</p>
                    </div>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-flask text-green-400 text-2xl"></i>
                    <div>
                        <p class="text-gray-400 text-sm">توليد الاختبارات</p>
                        <p class="text-white font-bold text-sm">تغطية شاملة</p>
                    </div>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 p-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-book text-yellow-400 text-2xl"></i>
                    <div>
                        <p class="text-gray-400 text-sm">التوثيق</p>
                        <p class="text-white font-bold text-sm">توثيق احترافي</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentTool = 'review';
let lastResult = null;

// Tool selection
function selectTool(tool) {
    currentTool = tool;
    
    // Update buttons
    document.querySelectorAll('.tool-btn').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'border-blue-500');
        btn.classList.add('bg-white/10', 'hover:bg-white/20', 'border-white/20');
    });
    document.querySelector(`[data-tool="${tool}"]`).classList.add('bg-blue-600', 'hover:bg-blue-700', 'border-blue-500');
    
    // Show/hide fields
    document.getElementById('errorDiv').classList.toggle('hidden', tool !== 'bugfix');
    document.getElementById('descDiv').classList.toggle('hidden', tool !== 'tests');
    
    // Update button text
    const texts = {
        'review': 'مراجعة',
        'bugfix': 'إصلاح',
        'tests': 'توليد الاختبارات',
        'docs': 'توليد التوثيق'
    };
    document.getElementById('processText').textContent = texts[tool];
}

// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabName = this.dataset.tab;
        
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active', 'bg-blue-600', 'hover:bg-blue-700'));
        this.classList.add('active', 'bg-blue-600', 'hover:bg-blue-700');
        
        document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
        document.getElementById(tabName + '-tab').classList.remove('hidden');
    });
});

// Form submission
document.getElementById('toolForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const code = document.getElementById('codeInput').value;
    if (!code) {
        showNotification('الرجاء إدخال الكود', 'error');
        return;
    }
    
    const btn = document.getElementById('processBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner animate-spin"></i><span>جاري المعالجة...</span>';
    
    addLog('جاري معالجة الكود...');
    
    try {
        let response;
        
        if (currentTool === 'review') {
            response = await fetch('{{ route("developer.ai.code-review") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ code })
            });
        } else if (currentTool === 'bugfix') {
            const error = document.getElementById('errorInput').value;
            response = await fetch('{{ route("developer.ai.bug-fixer") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ code, error_message: error })
            });
        } else if (currentTool === 'tests') {
            const desc = document.getElementById('descInput').value;
            response = await fetch('{{ route("developer.ai.test-generator") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ code, description: desc })
            });
        } else if (currentTool === 'docs') {
            response = await fetch('{{ route("developer.ai.documentation") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ code })
            });
        }
        
        const data = await response.json();
        
        if (data.success) {
            lastResult = data;
            displayResult(data);
            addLog('✅ تمت المعالجة بنجاح!');
            showNotification('تمت المعالجة بنجاح!', 'success');
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
        btn.innerHTML = '<i class="fas fa-magic"></i><span id="processText">معالجة</span>';
    }
});

function displayResult(data) {
    const result = document.getElementById('resultContent');
    
    if (currentTool === 'review') {
        const review = data.review || {};
        result.innerHTML = `
            <div class="text-right space-y-4">
                <h3 class="text-white font-bold text-lg">✅ تقرير المراجعة</h3>
                ${review.summary ? `<div class="bg-white/5 rounded p-3"><p class="text-gray-400 text-sm">الملخص</p><p class="text-white text-sm">${review.summary}</p></div>` : ''}
                ${review.quality_score ? `<div class="bg-white/5 rounded p-3"><p class="text-gray-400 text-sm">مستوى الجودة</p><p class="text-green-400 font-bold">${review.quality_score}/10</p></div>` : ''}
                ${review.strengths ? `<div class="bg-white/5 rounded p-3"><p class="text-gray-400 text-sm">نقاط القوة</p><p class="text-white text-sm">${review.strengths}</p></div>` : ''}
                ${review.weaknesses ? `<div class="bg-white/5 rounded p-3"><p class="text-gray-400 text-sm">نقاط الضعف</p><p class="text-white text-sm">${review.weaknesses}</p></div>` : ''}
            </div>
        `;
    } else if (currentTool === 'bugfix') {
        result.innerHTML = `
            <div class="text-right space-y-4">
                <h3 class="text-white font-bold text-lg">✅ تم إصلاح الخطأ</h3>
                ${data.explanation ? `<div class="bg-white/5 rounded p-3"><p class="text-gray-400 text-sm">التفسير</p><p class="text-white text-sm">${data.explanation}</p></div>` : ''}
                <div class="bg-green-500/20 border border-green-500/50 rounded p-3">
                    <p class="text-green-400 font-semibold">الكود المصحح جاهز في تبويب "الكود الخام"</p>
                </div>
            </div>
        `;
        document.getElementById('rawContent').textContent = data.fixed_code || '';
    } else if (currentTool === 'tests') {
        result.innerHTML = `
            <div class="text-right space-y-4">
                <h3 class="text-white font-bold text-lg">✅ تم توليد الاختبارات</h3>
                <div class="bg-white/5 rounded p-3">
                    <p class="text-gray-400 text-sm">عدد الاختبارات</p>
                    <p class="text-blue-400 font-bold">${data.test_count || 0} اختبار</p>
                </div>
                <div class="bg-blue-500/20 border border-blue-500/50 rounded p-3">
                    <p class="text-blue-400 font-semibold">الاختبارات جاهزة في تبويب "الكود الخام"</p>
                </div>
            </div>
        `;
        document.getElementById('rawContent').textContent = data.tests || '';
    } else if (currentTool === 'docs') {
        result.innerHTML = `
            <div class="text-right space-y-4">
                <h3 class="text-white font-bold text-lg">✅ تم توليد التوثيق</h3>
                <div class="bg-yellow-500/20 border border-yellow-500/50 rounded p-3">
                    <p class="text-yellow-400 font-semibold">التوثيق جاهز في تبويب "الكود الخام"</p>
                </div>
            </div>
        `;
        document.getElementById('rawContent').textContent = data.documentation || '';
    }
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

function copyRaw() {
    const text = document.getElementById('rawContent').textContent;
    navigator.clipboard.writeText(text);
    showNotification('تم النسخ', 'success');
}

function copyResult() {
    copyRaw();
}

function downloadResult() {
    const text = document.getElementById('rawContent').textContent;
    const element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('download', `${currentTool}-result.txt`);
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
    showNotification('تم التحميل', 'success');
}

function clearForm() {
    document.getElementById('toolForm').reset();
    document.getElementById('resultContent').innerHTML = `
        <i class="fas fa-hourglass-start text-4xl text-gray-500 mb-4"></i>
        <p>في انتظار إدخال البيانات والضغط على "معالجة"</p>
    `;
    lastResult = null;
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
