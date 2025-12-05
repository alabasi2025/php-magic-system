@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-lg p-8 mb-8 text-white">
            <h1 class="text-4xl font-bold mb-2">مراجعة الأكواد بـ AI</h1>
            <p class="text-purple-100 text-lg">تحسين جودة الكود واكتشاف الأخطاء الأمنية والبرمجية باستخدام الذكاء الاصطناعي</p>
        </div>

        <div class="bg-white rounded-lg shadow-xl p-8">
            <form id="codeReviewForm" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-3">
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">الكود المراد مراجعته</label>
                        <textarea id="code" name="code" rows="15" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono" placeholder="أدخل الكود هنا..."></textarea>
                    </div>
                    <div class="md:col-span-1 space-y-6">
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700 mb-2">لغة البرمجة</label>
                            <select id="language" name="language" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="php">PHP</option>
                                <option value="javascript">JavaScript</option>
                                <option value="python">Python</option>
                                <option value="java">Java</option>
                                <option value="csharp">C#</option>
                                <option value="typescript">TypeScript</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>
                        <button type="submit" id="reviewButton" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center">
                            <i class="fas fa-magic ml-2"></i>
                            <span>مراجعة الكود</span>
                        </button>
                        <div id="loadingIndicator" class="hidden text-center text-indigo-600 font-medium">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            <span>جاري المراجعة...</span>
                        </div>
                    </div>
                </div>
            </form>

            <div class="mt-10 pt-6 border-t border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">نتائج المراجعة</h2>
                <div id="reviewResults" class="bg-gray-50 p-4 rounded-lg border border-gray-200 min-h-[200px] whitespace-pre-wrap font-mono text-sm text-gray-800">
                    النتائج ستظهر هنا بعد المراجعة.
                </div>
                <div id="errorMessage" class="hidden mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">خطأ!</strong>
                    <span class="block sm:inline" id="errorText"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('codeReviewForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = e.target;
        const code = document.getElementById('code').value;
        const language = document.getElementById('language').value;
        const reviewButton = document.getElementById('reviewButton');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const reviewResults = document.getElementById('reviewResults');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');

        // Reset state
        reviewResults.innerHTML = 'جاري المراجعة...';
        errorMessage.classList.add('hidden');
        reviewButton.disabled = true;
        loadingIndicator.classList.remove('hidden');

        try {
            const response = await fetch('{{ route('developer.ai.code-review.post') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ code, language })
            });

            const data = await response.json();

            if (response.ok) {
                reviewResults.innerHTML = data.review || 'تمت المراجعة بنجاح، ولكن لا توجد نتائج محددة.';
            } else {
                let message = data.message || 'حدث خطأ غير معروف.';
                if (data.errors) {
                    message += '<ul>' + Object.values(data.errors).map(err => `<li>${err[0]}</li>`).join('') + '</ul>';
                }
                errorText.innerHTML = message;
                errorMessage.classList.remove('hidden');
                reviewResults.innerHTML = 'فشلت المراجعة. انظر رسالة الخطأ أعلاه.';
            }
        } catch (error) {
            errorText.innerHTML = 'فشل الاتصال بالخادم: ' + error.message;
            errorMessage.classList.remove('hidden');
            reviewResults.innerHTML = 'فشلت المراجعة بسبب خطأ في الاتصال.';
        } finally {
            reviewButton.disabled = false;
            loadingIndicator.classList.add('hidden');
        }
    });
</script>
@endsection
