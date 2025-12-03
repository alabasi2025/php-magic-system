@extends('layouts.app')

@section('title', 'مساعد الدردشة الذكي للمشروع')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- زر العودة -->
    <div class="mb-6 text-right">
        <a href="{{ route('ai-tools.dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800 transition duration-150 ease-in-out">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة إلى لوحة أدوات الذكاء الاصطناعي
        </a>
    </div>

    <!-- العنوان والوصف -->
    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-3">
            <i class="fas fa-robot text-indigo-600 mr-2"></i>
            مساعد الدردشة الذكي للمشروع
        </h1>
        <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            روبوت دردشة متقدم يفهم بنية مشروعك البرمجي، ويجيب على أسئلة المطورين حول الكود، الوثائق، والتبعيات. اجعل استكشاف المشروع أسرع وأكثر كفاءة.
        </p>
    </div>

    <!-- بطاقات الإحصائيات (Stats Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- بطاقة 1: الملفات المفهرسة -->
        <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-indigo-500">
            <div class="flex items-center justify-end">
                <i class="fas fa-file-code text-3xl text-indigo-500 mr-4"></i>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-500">الملفات المفهرسة</p>
                    <p class="text-2xl font-bold text-gray-900">1,452</p>
                </div>
            </div>
        </div>
        <!-- بطاقة 2: حجم قاعدة المعرفة -->
        <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-green-500">
            <div class="flex items-center justify-end">
                <i class="fas fa-database text-3xl text-green-500 mr-4"></i>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-500">حجم قاعدة المعرفة</p>
                    <p class="text-2xl font-bold text-gray-900">12.5 ميجابايت</p>
                </div>
            </div>
        </div>
        <!-- بطاقة 3: إجمالي الاستفسارات -->
        <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-purple-500">
            <div class="flex items-center justify-end">
                <i class="fas fa-comments text-3xl text-purple-500 mr-4"></i>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-500">إجمالي الاستفسارات</p>
                    <p class="text-2xl font-bold text-gray-900">4,870</p>
                </div>
            </div>
        </div>
    </div>

    <!-- واجهة الدردشة الرئيسية -->
    <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
        <!-- رأس الواجهة مع التدرج اللوني -->
        <div class="p-5 text-white text-center" style="background-image: linear-gradient(to right, #4f46e5, #8b5cf6);">
            <h2 class="text-2xl font-semibold flex items-center justify-center">
                <i class="fas fa-comment-dots mr-2"></i>
                ابدأ محادثتك مع المساعد
            </h2>
            <p class="text-sm opacity-90 mt-1">اسأل عن أي جزء في مشروعك البرمجي</p>
        </div>

        <!-- منطقة الدردشة (يجب أن يتم ملؤها بـ JS لاحقاً) -->
        <div id="chat-window" class="p-6 h-96 overflow-y-auto flex flex-col-reverse space-y-4 space-y-reverse">
            <!-- رسالة ترحيب (مثال) -->
            <div class="flex justify-start">
                <div class="bg-gray-100 p-3 rounded-lg max-w-xs lg:max-w-md shadow-md">
                    <p class="text-sm text-gray-800">
                        مرحباً! أنا مساعد الدردشة الذكي للمشروع. كيف يمكنني مساعدتك في فهم أو استكشاف الكود الخاص بك اليوم؟
                    </p>
                </div>
            </div>
        </div>

        <!-- نموذج الإدخال التفاعلي -->
        <div class="p-4 border-t border-gray-200">
            <form id="chat-form" class="flex items-center">
                <input type="text" id="chat-input" placeholder="اكتب سؤالك هنا... (مثال: ما هي وظيفة الدالة 'handlePayment'؟)"
                       class="flex-grow p-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 text-right"
                       dir="rtl">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white p-3 rounded-full mr-3 transition duration-150 ease-in-out shadow-lg transform hover:scale-105">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- ملاحظة إضافية -->
    <div class="mt-8 p-4 bg-indigo-50 border-r-4 border-indigo-500 text-indigo-700 rounded-lg shadow-md text-right">
        <p class="font-semibold">
            <i class="fas fa-info-circle ml-2"></i>
            ملاحظة:
        </p>
        <p class="text-sm mt-1">
            يعتمد هذا المساعد على فهرسة ملفات مشروعك الحالية. تأكد من تحديث الفهرس للحصول على أدق الإجابات.
        </p>
    </div>
</div>

<!-- تضمين Font Awesome (افتراضاً أنه متاح في layouts.app، ولكن يضاف هنا للتأكيد) -->
@push('scripts')
<script>
    // كود JavaScript بسيط لجعل النموذج تفاعلياً (للتوضيح فقط)
    document.getElementById('chat-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('chat-input');
        const message = input.value.trim();

        if (message) {
            // إضافة رسالة المستخدم
            const chatWindow = document.getElementById('chat-window');
            const userMessage = document.createElement('div');
            userMessage.className = 'flex justify-end';
            userMessage.innerHTML = `
                <div class="bg-indigo-500 text-white p-3 rounded-lg max-w-xs lg:max-w-md shadow-md">
                    <p class="text-sm">${message}</p>
                </div>
            `;
            chatWindow.prepend(userMessage); // إضافة الرسالة في الأعلى (لأننا نستخدم flex-col-reverse)

            // محاكاة رد المساعد
            setTimeout(() => {
                const assistantMessage = document.createElement('div');
                assistantMessage.className = 'flex justify-start';
                assistantMessage.innerHTML = `
                    <div class="bg-gray-100 p-3 rounded-lg max-w-xs lg:max-w-md shadow-md">
                        <p class="text-sm text-gray-800">
                            جاري البحث في ملفات المشروع عن: <strong>${message}</strong>...
                        </p>
                    </div>
                `;
                chatWindow.prepend(assistantMessage);
                chatWindow.scrollTop = chatWindow.scrollHeight;
            }, 1000);

            input.value = '';
        }
    });
</script>
@endpush
@endsection