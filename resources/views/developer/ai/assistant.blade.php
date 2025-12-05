
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg shadow-lg p-8 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2">🤖 المساعد الذكي</h1>
                    <p class="text-purple-100 text-lg">مساعد ذكي مدعوم بـ AI لمساعدتك في البرمجة وحل المشاكل</p>
                </div>
                <div class="text-6xl opacity-50">
                    <i class="fas fa-robot"></i>
                </div>
            </div>
        </div>

        <!-- Chat Container -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Chat Messages -->
            <div id="chatMessages" class="h-96 overflow-y-auto p-6 space-y-4 bg-gray-50">
                <!-- Welcome Message -->
                <div class="flex items-start space-x-3 space-x-reverse">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-600 to-pink-600 flex items-center justify-center text-white">
                            <i class="fas fa-robot"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="bg-white rounded-lg shadow p-4">
                            <p class="text-gray-800">مرحباً! 👋 أنا المساعد الذكي لنظام SEMOP. كيف يمكنني مساعدتك اليوم؟</p>
                            <p class="text-sm text-gray-500 mt-2">يمكنني مساعدتك في:</p>
                            <ul class="text-sm text-gray-600 mt-1 mr-4 list-disc">
                                <li>تحليل وشرح الكود البرمجي</li>
                                <li>إصلاح الأخطاء البرمجية</li>
                                <li>توليد كود جديد</li>
                                <li>الإجابة على أسئلتك التقنية</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Input -->
            <div class="border-t border-gray-200 p-4 bg-white">
                <form id="chatForm" class="flex space-x-3 space-x-reverse">
                    @csrf
                    <input 
                        type="text" 
                        id="messageInput" 
                        name="message" 
                        placeholder="اكتب رسالتك هنا..." 
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        required
                    >
                    <button 
                        type="submit" 
                        id="sendButton"
                        class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-200 flex items-center space-x-2 space-x-reverse"
                    >
                        <i class="fas fa-paper-plane"></i>
                        <span>إرسال</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
            <button onclick="quickAction('تحليل الكود')" class="p-4 bg-blue-50 hover:bg-blue-100 rounded-lg text-center transition-colors">
                <i class="fas fa-search text-2xl text-blue-600 mb-2"></i>
                <p class="text-sm font-medium text-gray-700">تحليل الكود</p>
            </button>
            <button onclick="quickAction('إصلاح خطأ')" class="p-4 bg-red-50 hover:bg-red-100 rounded-lg text-center transition-colors">
                <i class="fas fa-bug text-2xl text-red-600 mb-2"></i>
                <p class="text-sm font-medium text-gray-700">إصلاح خطأ</p>
            </button>
            <button onclick="quickAction('توليد كود')" class="p-4 bg-green-50 hover:bg-green-100 rounded-lg text-center transition-colors">
                <i class="fas fa-code text-2xl text-green-600 mb-2"></i>