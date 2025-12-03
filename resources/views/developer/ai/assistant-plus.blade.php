@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-lg shadow-2xl p-8 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-5xl font-bold mb-3 flex items-center">
                        <i class="fas fa-brain mr-3"></i>
                        المساعد الذكي المتقدم Plus
                    </h1>
                    <p class="text-indigo-100 text-xl mb-2">نسخة متطورة مع ميزات ذكاء اصطناعي متقدمة</p>
                    <div class="flex items-center space-x-4 space-x-reverse text-sm">
                        <span class="bg-white/20 px-3 py-1 rounded-full">v3.18.0</span>
                        <span class="bg-white/20 px-3 py-1 rounded-full">
                            <i class="fas fa-robot mr-1"></i>
                            GPT-4.1 Mini
                        </span>
                        <span class="bg-white/20 px-3 py-1 rounded-full">
                            <i class="fas fa-language mr-1"></i>
                            دعم متعدد اللغات
                        </span>
                    </div>
                </div>
                <div class="text-8xl opacity-30">
                    <i class="fas fa-brain"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Chat Area -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                    <!-- Chat Header -->
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4 text-white flex items-center justify-between">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <i class="fas fa-comments text-2xl"></i>
                            <div>
                                <h3 class="font-bold text-lg">المحادثة الذكية</h3>
                                <p class="text-xs text-indigo-100" id="conversationStatus">متصل ونشط</p>
                            </div>
                        </div>
                        <button onclick="clearChat()" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded-lg text-sm transition-colors">
                            <i class="fas fa-trash-alt mr-1"></i>
                            مسح المحادثة
                        </button>
                    </div>

                    <!-- Chat Messages -->
                    <div id="chatMessages" class="h-[500px] overflow-y-auto p-6 space-y-4 bg-gradient-to-b from-gray-50 to-white">
                        <!-- Welcome Message -->
                        <div class="flex items-start space-x-3 space-x-reverse animate-fade-in">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-lg">
                                    <i class="fas fa-brain text-xl"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="bg-white rounded-2xl shadow-md p-5 border border-indigo-100">
                                    <p class="text-gray-800 font-medium mb-2">مرحباً بك في المساعد الذكي المتقدم Plus! 🚀</p>
                                    <p class="text-gray-700 mb-3">أنا نسخة متطورة من المساعد الذكي، مزود بقدرات متقدمة في:</p>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                            تحليل الكود المتقدم
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                            توليد كود احترافي
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                            إصلاح الأخطاء الذكي
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                            فحص الأمان
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                            تحسين الأداء
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                            ترجمة الأكواد
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                            توليد الاختبارات
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                            توليد التوثيق
                                        </div>
                                    </div>
                                    <p class="text-sm text-indigo-600 mt-3 font-medium">
                                        <i class="fas fa-lightbulb mr-1"></i>
                                        استخدم الأدوات الجانبية للوصول السريع للميزات المتقدمة
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Input -->
                    <div class="border-t border-gray-200 p-4 bg-gray-50">
                        <form id="chatForm" class="space-y-3">
                            @csrf
                            <div class="flex space-x-3 space-x-reverse">
                                <textarea 
                                    id="messageInput" 
                                    name="message" 
                                    rows="2"
                                    placeholder="اكتب رسالتك أو الصق الكود هنا..." 
                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                                    required
                                ></textarea>
                                <button 
                                    type="submit" 
                                    id="sendButton"
                                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 flex items-center space-x-2 space-x-reverse shadow-lg"
                                >
                                    <i class="fas fa-paper-plane"></i>
                                    <span>إرسال</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <div>
                                    <i class="fas fa-info-circle mr-1"></i>
                                    يدعم Markdown والأكواد البرمجية
                                </div>
                                <div id="charCount">0 / 5000</div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar Tools -->
            <div class="space-y-6">
                <!-- Quick Tools -->
                <div class="bg-white rounded-lg shadow-xl p-5">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-tools text-indigo-600 mr-2"></i>
                        أدوات سريعة
                    </h3>
                    <div class="space-y-2">
                        <button onclick="openTool('analyze')" class="w-full p-3 bg-blue-50 hover:bg-blue-100 rounded-lg text-right transition-colors flex items-center">
                            <i class="fas fa-search-plus text-blue-600 ml-3"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">تحليل كود متقدم</p>
                                <p class="text-xs text-gray-500">تحليل شامل مع اقتراحات</p>
                            </div>
                        </button>
                        <button onclick="openTool('generate')" class="w-full p-3 bg-green-50 hover:bg-green-100 rounded-lg text-right transition-colors flex items-center">
                            <i class="fas fa-code text-green-600 ml-3"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">توليد كود</p>
                                <p class="text-xs text-gray-500">كود احترافي بأفضل الممارسات</p>
                            </div>
                        </button>
                        <button onclick="openTool('fix')" class="w-full p-3 bg-red-50 hover:bg-red-100 rounded-lg text-right transition-colors flex items-center">
                            <i class="fas fa-bug text-red-600 ml-3"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">إصلاح أخطاء</p>
                                <p class="text-xs text-gray-500">تشخيص وإصلاح ذكي</p>
                            </div>
                        </button>
                        <button onclick="openTool('refactor')" class="w-full p-3 bg-purple-50 hover:bg-purple-100 rounded-lg text-right transition-colors flex items-center">
                            <i class="fas fa-sync-alt text-purple-600 ml-3"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">إعادة هيكلة</p>
                                <p class="text-xs text-gray-500">تحسين بنية الكود</p>
                            </div>
                        </button>
                        <button onclick="openTool('security')" class="w-full p-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg text-right transition-colors flex items-center">
                            <i class="fas fa-shield-alt text-yellow-600 ml-3"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">فحص الأمان</p>
                                <p class="text-xs text-gray-500">كشف الثغرات الأمنية</p>
                            </div>
                        </button>
                        <button onclick="openTool('performance')" class="w-full p-3 bg-orange-50 hover:bg-orange-100 rounded-lg text-right transition-colors flex items-center">
                            <i class="fas fa-tachometer-alt text-orange-600 ml-3"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">تحسين الأداء</p>
                                <p class="text-xs text-gray-500">تحليل وتحسين السرعة</p>
                            </div>
                        </button>
                        <button onclick="openTool('translate')" class="w-full p-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg text-right transition-colors flex items-center">
                            <i class="fas fa-language text-indigo-600 ml-3"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">ترجمة الكود</p>
                                <p class="text-xs text-gray-500">بين لغات البرمجة</p>
                            </div>
                        </button>
                        <button onclick="openTool('tests')" class="w-full p-3 bg-teal-50 hover:bg-teal-100 rounded-lg text-right transition-colors flex items-center">
                            <i class="fas fa-vial text-teal-600 ml-3"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">توليد اختبارات</p>
                                <p class="text-xs text-gray-500">PHPUnit / Jest</p>
                            </div>
                        </button>
                        <button onclick="openTool('docs')" class="w-full p-3 bg-pink-50 hover:bg-pink-100 rounded-lg text-right transition-colors flex items-center">
                            <i class="fas fa-file-alt text-pink-600 ml-3"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">توليد توثيق</p>
                                <p class="text-xs text-gray-500">PHPDoc / JSDoc</p>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Language Selector -->
                <div class="bg-white rounded-lg shadow-xl p-5">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-code text-indigo-600 mr-2"></i>
                        لغة البرمجة
                    </h3>
                    <select id="languageSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="PHP">PHP</option>
                        <option value="JavaScript">JavaScript</option>
                        <option value="Python">Python</option>
                        <option value="Java">Java</option>
                        <option value="C#">C#</option>
                        <option value="Go">Go</option>
                        <option value="Ruby">Ruby</option>
                        <option value="TypeScript">TypeScript</option>
                    </select>
                </div>

                <!-- Stats -->
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg shadow-xl p-5">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
                        إحصائيات الجلسة
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">عدد الرسائل:</span>
                            <span class="font-bold text-indigo-600" id="messageCount">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">الرموز المستخدمة:</span>
                            <span class="font-bold text-purple-600" id="tokenCount">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">وقت الجلسة:</span>
                            <span class="font-bold text-pink-600" id="sessionTime">0:00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tool Modal -->
<div id="toolModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white flex justify-between items-center">
            <h3 id="toolModalTitle" class="text-2xl font-bold"></h3>
            <button onclick="closeToolModal()" class="text-white hover:text-gray-200">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div id="toolModalContent" class="p-6">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>

<script>
let conversationId = 'conv_' + Date.now();
let messageCount = 0;
let totalTokens = 0;
let sessionStartTime = Date.now();

// Update session timer
setInterval(() => {
    const elapsed = Math.floor((Date.now() - sessionStartTime) / 1000);
    const minutes = Math.floor(elapsed / 60);
    const seconds = elapsed % 60;
    document.getElementById('sessionTime').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
}, 1000);

// Character counter
document.getElementById('messageInput').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('charCount').textContent = `${count} / 5000`;
});

// Chat form submission
document.getElementById('chatForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    // Add user message to chat
    addMessage('user', message);
    messageInput.value = '';
    document.getElementById('charCount').textContent = '0 / 5000';
    
    // Show loading
    const loadingId = addMessage('ai', '<i class="fas fa-spinner fa-spin"></i> جاري التفكير...', true);
    
    try {
        const response = await fetch('{{ route("developer.ai-assistant-plus.chat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                message: message,
                conversation_id: conversationId
            })
        });
        
        const data = await response.json();
        
        // Remove loading message
        document.getElementById(loadingId).remove();
        
        if (data.success) {
            addMessage('ai', data.message);
            if (data.usage) {
                totalTokens += data.usage.total_tokens;
                document.getElementById('tokenCount').textContent = totalTokens.toLocaleString();
            }
        } else {
            addMessage('ai', '❌ ' + (data.error || 'حدث خطأ في الاتصال'));
        }
    } catch (error) {
        document.getElementById(loadingId).remove();
        addMessage('ai', '❌ حدث خطأ في الاتصال بالخادم');
    }
});

function addMessage(role, content, isLoading = false) {
    const messagesContainer = document.getElementById('chatMessages');
    const messageId = 'msg_' + Date.now() + '_' + Math.random();
    
    const isUser = role === 'user';
    const bgColor = isUser ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200';
    const alignment = isUser ? 'flex-row-reverse' : '';
    const iconBg = isUser ? 'bg-indigo-600' : 'bg-gradient-to-r from-indigo-600 to-purple-600';
    const icon = isUser ? 'fa-user' : 'fa-brain';
    
    const messageDiv = document.createElement('div');
    messageDiv.id = messageId;
    messageDiv.className = `flex items-start space-x-3 space-x-reverse animate-fade-in ${alignment}`;
    messageDiv.innerHTML = `
        <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-full ${iconBg} flex items-center justify-center text-white shadow-lg">
                <i class="fas ${icon}"></i>
            </div>
        </div>
        <div class="flex-1 max-w-[80%]">
            <div class="${bgColor} rounded-2xl shadow-md p-4">
                <div class="prose prose-sm max-w-none">${content}</div>
            </div>
        </div>
    `;
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    if (!isLoading && role === 'user') {
        messageCount++;
        document.getElementById('messageCount').textContent = messageCount;
    }
    
    return messageId;
}

function clearChat() {
    if (confirm('هل أنت متأكد من مسح المحادثة؟')) {
        const messagesContainer = document.getElementById('chatMessages');
        messagesContainer.innerHTML = '';
        conversationId = 'conv_' + Date.now();
        messageCount = 0;
        totalTokens = 0;
        document.getElementById('messageCount').textContent = '0';
        document.getElementById('tokenCount').textContent = '0';
        location.reload();
    }
}

function openTool(tool) {
    const modal = document.getElementById('toolModal');
    const title = document.getElementById('toolModalTitle');
    const content = document.getElementById('toolModalContent');
    
    const tools = {
        'analyze': {
            title: '🔍 تحليل كود متقدم',
            content: getAnalyzeToolContent()
        },
        'generate': {
            title: '💻 توليد كود احترافي',
            content: getGenerateToolContent()
        },
        'fix': {
            title: '🐛 إصلاح الأخطاء',
            content: getFixToolContent()
        },
        'refactor': {
            title: '🔄 إعادة هيكلة الكود',
            content: getRefactorToolContent()
        },
        'security': {
            title: '🛡️ فحص الأمان',
            content: getSecurityToolContent()
        },
        'performance': {
            title: '⚡ تحسين الأداء',
            content: getPerformanceToolContent()
        },
        'translate': {
            title: '🌐 ترجمة الكود',
            content: getTranslateToolContent()
        },
        'tests': {
            title: '🧪 توليد اختبارات',
            content: getTestsToolContent()
        },
        'docs': {
            title: '📄 توليد توثيق',
            content: getDocsToolContent()
        }
    };
    
    if (tools[tool]) {
        title.textContent = tools[tool].title;
        content.innerHTML = tools[tool].content;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeToolModal() {
    const modal = document.getElementById('toolModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Tool content generators
function getAnalyzeToolContent() {
    return `
        <form id="analyzeForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الكود المراد تحليله:</label>
                <textarea name="code" rows="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required></textarea>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-lg hover:from-indigo-700 hover:to-purple-700">
                <i class="fas fa-search-plus mr-2"></i>
                تحليل الآن
            </button>
        </form>
    `;
}

function getGenerateToolContent() {
    return `
        <form id="generateForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">وصف المهمة:</label>
                <textarea name="description" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="مثال: دالة لحساب مجموع عناصر مصفوفة" required></textarea>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-3 rounded-lg hover:from-green-700 hover:to-teal-700">
                <i class="fas fa-code mr-2"></i>
                توليد الكود
            </button>
        </form>
    `;
}

function getFixToolContent() {
    return `
        <form id="fixForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الكود الذي به خطأ:</label>
                <textarea name="code" rows="8" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">رسالة الخطأ:</label>
                <textarea name="error" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required></textarea>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-pink-600 text-white px-6 py-3 rounded-lg hover:from-red-700 hover:to-pink-700">
                <i class="fas fa-bug mr-2"></i>
                إصلاح الخطأ
            </button>
        </form>
    `;
}

function getRefactorToolContent() {
    return `
        <form id="refactorForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الكود المراد تحسينه:</label>
                <textarea name="code" rows="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required></textarea>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-lg hover:from-purple-700 hover:to-indigo-700">
                <i class="fas fa-sync-alt mr-2"></i>
                إعادة الهيكلة
            </button>
        </form>
    `;
}

function getSecurityToolContent() {
    return `
        <form id="securityForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الكود المراد فحصه:</label>
                <textarea name="code" rows="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required></textarea>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-yellow-600 to-orange-600 text-white px-6 py-3 rounded-lg hover:from-yellow-700 hover:to-orange-700">
                <i class="fas fa-shield-alt mr-2"></i>
                فحص الأمان
            </button>
        </form>
    `;
}

function getPerformanceToolContent() {
    return `
        <form id="performanceForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الكود المراد تحسينه:</label>
                <textarea name="code" rows="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required></textarea>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-orange-600 to-red-600 text-white px-6 py-3 rounded-lg hover:from-orange-700 hover:to-red-700">
                <i class="fas fa-tachometer-alt mr-2"></i>
                تحسين الأداء
            </button>
        </form>
    `;
}

function getTranslateToolContent() {
    return `
        <form id="translateForm" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">من:</label>
                    <select name="from_language" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="PHP">PHP</option>
                        <option value="JavaScript">JavaScript</option>
                        <option value="Python">Python</option>
                        <option value="Java">Java</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">إلى:</label>
                    <select name="to_language" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="JavaScript">JavaScript</option>
                        <option value="PHP">PHP</option>
                        <option value="Python">Python</option>
                        <option value="Java">Java</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الكود:</label>
                <textarea name="code" rows="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required></textarea>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 text-white px-6 py-3 rounded-lg hover:from-indigo-700 hover:to-blue-700">
                <i class="fas fa-language mr-2"></i>
                ترجمة الكود
            </button>
        </form>
    `;
}

function getTestsToolContent() {
    return `
        <form id="testsForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الكود المراد اختباره:</label>
                <textarea name="code" rows="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required></textarea>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-teal-600 to-green-600 text-white px-6 py-3 rounded-lg hover:from-teal-700 hover:to-green-700">
                <i class="fas fa-vial mr-2"></i>
                توليد الاختبارات
            </button>
        </form>
    `;
}

function getDocsToolContent() {
    return `
        <form id="docsForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الكود المراد توثيقه:</label>
                <textarea name="code" rows="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required></textarea>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-pink-600 to-purple-600 text-white px-6 py-3 rounded-lg hover:from-pink-700 hover:to-purple-700">
                <i class="fas fa-file-alt mr-2"></i>
                توليد التوثيق
            </button>
        </form>
    `;
}

// Handle tool form submissions
document.addEventListener('submit', async function(e) {
    const formId = e.target.id;
    if (!formId || !formId.endsWith('Form')) return;
    
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    data.language = document.getElementById('languageSelect').value;
    
    const endpoints = {
        'analyzeForm': '{{ route("developer.ai-assistant-plus.analyze-code") }}',
        'generateForm': '{{ route("developer.ai-assistant-plus.generate-code") }}',
        'fixForm': '{{ route("developer.ai-assistant-plus.fix-bug") }}',
        'refactorForm': '{{ route("developer.ai-assistant-plus.refactor-code") }}',
        'securityForm': '{{ route("developer.ai-assistant-plus.security-scan") }}',
        'performanceForm': '{{ route("developer.ai-assistant-plus.optimize-performance") }}',
        'translateForm': '{{ route("developer.ai-assistant-plus.translate-code") }}',
        'testsForm': '{{ route("developer.ai-assistant-plus.generate-tests") }}',
        'docsForm': '{{ route("developer.ai-assistant-plus.generate-documentation") }}'
    };
    
    const endpoint = endpoints[formId];
    if (!endpoint) return;
    
    closeToolModal();
    const loadingId = addMessage('ai', '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...', true);
    
    try {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        document.getElementById(loadingId).remove();
        
        if (result.success) {
            addMessage('ai', result.message);
            if (result.usage) {
                totalTokens += result.usage.total_tokens;
                document.getElementById('tokenCount').textContent = totalTokens.toLocaleString();
            }
        } else {
            addMessage('ai', '❌ ' + (result.error || 'حدث خطأ'));
        }
    } catch (error) {
        document.getElementById(loadingId).remove();
        addMessage('ai', '❌ حدث خطأ في الاتصال');
    }
});
</script>
@endsection
