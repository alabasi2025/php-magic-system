@extends('layouts.app') {{-- افتراض وجود ملف تخطيط رئيسي --}}

@section('content')
<div class="container mx-auto p-4 sm:p-6 lg:p-8" dir="rtl">
    <h1 class="text-4xl font-extrabold text-gray-900 mb-2 text-right">مولد التوثيق الذكي</h1>
    <p class="text-xl text-gray-600 mb-8 text-right">استخدم قوة الذكاء الاصطناعي لتوليد توثيق شامل ومحترف لكود Laravel الخاص بك.</p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- عمود الإدخال والخيارات --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-xl shadow-2xl border border-gray-100">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-2">الكود المصدر</h2>
                <textarea id="sourceCode" rows="15" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono" placeholder="الصق الكود هنا (PHP, JS, إلخ...) أو ارفع ملفاً..."></textarea>
                <p class="text-sm text-gray-500 mt-2">يمكنك لصق الكود مباشرة أو افتراضياً، سيتم توفير آلية لرفع الملفات في المستقبل.</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-2xl border border-gray-100">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-2">خيارات التوليد</h2>

                {{-- نوع التوثيق المطلوب --}}
                <div class="mb-4">
                    <label for="docType" class="block text-sm font-medium text-gray-700 mb-2">نوع التوثيق</label>
                    <select id="docType" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="code">توثيق شامل للكود (Classes, Methods, Functions)</option>
                        <option value="readme">توليد ملف README.md تلقائياً</option>
                        <option value="api">توليد توثيق API</option>
                        <option value="user_guide">توليد دليل المستخدم (User Guide)</option>
                    </select>
                </div>

                {{-- تنسيق الإخراج --}}
                <div class="mb-6">
                    <label for="outputFormat" class="block text-sm font-medium text-gray-700 mb-2">تنسيق الإخراج</label>
                    <select id="outputFormat" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="markdown">Markdown (.md)</option>
                        <option value="html">HTML</option>
                        <option value="pdf">PDF (يتطلب معالجة من جهة الخادم)</option>
                    </select>
                </div>

                {{-- زر التوليد --}}
                <button onclick="generateDocumentation()" class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    توليد التوثيق الآن
                </button>
            </div>
        </div>

        {{-- عمود النتائج --}}
        <div class="lg:col-span-2">
            <div class="bg-white p-6 rounded-xl shadow-2xl border border-gray-100 min-h-[600px]">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-2">النتائج</h2>
                <div id="documentationOutput" class="prose max-w-none text-gray-700 text-right">
                    <p class="text-gray-500">سيظهر التوثيق الذي تم توليده هنا. يرجى إدخال الكود واختيار الخيارات والضغط على "توليد التوثيق الآن".</p>
                </div>

                {{-- زر التحميل (يظهر بعد التوليد) --}}
                <div id="downloadArea" class="mt-6 hidden">
                    <button onclick="downloadDocumentation()" class="px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        تحميل الملف
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- سكربت JavaScript للتكامل مع Manus AI API --}}
<script>
    // **ملاحظة:** في بيئة الإنتاج، يجب أن يتم تمرير مفتاح API عبر متغير بيئة أو معالج خلفي (Backend Handler)
    // لتجنب كشفه في الواجهة الأمامية. هذا الكود هو للمحاكاة وإظهار منطق التكامل.
    const MANUS_API_KEY = 'sk-4-tSe7JkjRuRPoZ70EWgVWA_Kr9v2ldVSfo8z5VsVJGhbNjAodRsNM618fEaYGGWvvKHofv-HSTwglnGZcizlVrTDJQt';
    const MANUS_API_URL = 'https://api.manus.ai/v1/chat/completions'; // افتراض نقطة نهاية متوافقة مع OpenAI
    const MODEL_NAME = 'gpt-4.1-mini';

    async function generateDocumentation() {
        const code = document.getElementById('sourceCode').value;
        const docType = document.getElementById('docType').value;
        const outputFormat = document.getElementById('outputFormat').value;
        const outputDiv = document.getElementById('documentationOutput');
        const downloadArea = document.getElementById('downloadArea');

        if (!code.trim()) {
            outputDiv.innerHTML = '<p class="text-red-500">الرجاء إدخال الكود المصدر أولاً.</p>';
            downloadArea.classList.add('hidden');
            return;
        }

        outputDiv.innerHTML = '<div class="flex items-center justify-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span class="text-indigo-600">جاري توليد التوثيق... قد يستغرق الأمر بضع ثوانٍ.</span></div>';
        downloadArea.classList.add('hidden');

        let prompt = `أنت خبير في توثيق الكود. قم بتوليد ${getDocTypeDescription(docType)} للكود التالي. يجب أن يكون الإخراج بتنسيق ${outputFormat} وجميع النصوص باللغة العربية الفصحى. الكود المصدر: \n\n\`\`\`\n${code}\n\`\`\``;

        try {
            const response = await fetch(MANUS_API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${MANUS_API_KEY}`
                },
                body: JSON.stringify({
                    model: MODEL_NAME,
                    messages: [{ role: "user", content: prompt }],
                    temperature: 0.7,
                    max_tokens: 4096
                })
            });

            if (!response.ok) {
                throw new Error(`خطأ في استدعاء API: ${response.statusText}`);
            }

            const data = await response.json();
            const documentation = data.choices[0].message.content;

            // عرض النتيجة
            outputDiv.innerHTML = formatOutput(documentation, outputFormat);
            downloadArea.classList.remove('hidden');

        } catch (error) {
            console.error('خطأ في التوليد:', error);
            outputDiv.innerHTML = `<p class="text-red-600 font-semibold">حدث خطأ أثناء توليد التوثيق: ${error.message}. يرجى التحقق من مفتاح API أو الاتصال بالخادم.</p>`;
            downloadArea.classList.add('hidden');
        }
    }

    function getDocTypeDescription(type) {
        switch (type) {
            case 'code': return 'توثيق شامل ومفصل لجميع الفئات والدوال والأساليب (Classes, Methods, Functions)';
            case 'readme': return 'ملف README.md احترافي وشامل';
            case 'api': return 'توثيق API مفصل يشمل نقاط النهاية، المعلمات، والاستجابات';
            case 'user_guide': return 'دليل مستخدم شامل يشرح كيفية استخدام الكود أو الميزة';
            default: return 'توثيق';
        }
    }

    function formatOutput(content, format) {
        // في بيئة الإنتاج، سيتم استخدام مكتبة لتحويل Markdown إلى HTML
        // هنا، سنقوم بمحاكاة عرض Markdown البسيط أو HTML
        if (format === 'markdown') {
            // محاكاة عرض Markdown (يمكن استخدام مكتبة مثل marked.js)
            return `<pre class="whitespace-pre-wrap p-4 bg-gray-50 border border-gray-200 rounded-lg text-sm overflow-auto" dir="ltr">${content.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</pre>`;
        } else if (format === 'html') {
            // عرض HTML مباشرة (يجب أن يكون الكود المولد HTML نظيفاً)
            return `<div class="p-4 border border-gray-200 rounded-lg">${content}</div>`;
        } else if (format === 'pdf') {
            // PDF يتطلب معالجة من جهة الخادم، لذا نعرض رسالة
            return `<p class="text-blue-600 font-semibold">تم توليد المحتوى بنجاح. سيتم تحويله إلى PDF من جهة الخادم عند الضغط على زر التحميل.</p><div class="p-4 border border-gray-200 rounded-lg">${content}</div>`;
        }
        return content;
    }

    function downloadDocumentation() {
        const outputDiv = document.getElementById('documentationOutput');
        const outputFormat = document.getElementById('outputFormat').value;
        const docType = document.getElementById('docType').value;
        const content = outputDiv.innerText; // استخدام innerText للحصول على النص الخام

        // إنشاء كائن Blob وتحميله
        const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
        const filename = `${docType}_documentation.${outputFormat === 'markdown' ? 'md' : outputFormat === 'html' ? 'html' : 'txt'}`;

        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        alert(`تم بدء تحميل ملف التوثيق بصيغة ${outputFormat}.`);
    }
</script>
@endsection
