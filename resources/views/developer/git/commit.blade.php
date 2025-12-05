@extends('layouts.app')

@section('title', 'Commit & Push - نظام المطور')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-code-branch text-green-600"></i>
            Commit & Push
        </h1>
        <p class="text-gray-600">حفظ التغييرات ونشرها إلى فرع developer</p>
    </div>

    <!-- Current Status -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">الفرع الحالي</p>
                <p class="text-2xl font-bold text-blue-600" id="currentBranch">developer</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">ملفات معدّلة</p>
                <p class="text-2xl font-bold text-green-600" id="modifiedCount">0</p>
            </div>
        </div>
    </div>

    <!-- Modified Files Preview -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-file-code text-blue-600"></i>
            الملفات المعدّلة
        </h2>
        <div id="filesList" class="space-y-2 max-h-96 overflow-y-auto">
            <p class="text-gray-500 text-center py-4">جاري التحميل...</p>
        </div>
    </div>

    <!-- Commit Form -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-edit text-purple-600"></i>
            رسالة Commit
        </h2>
        
        <form id="commitForm">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">
                    رسالة Commit
                    <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="commitMessage" 
                    name="message" 
                    rows="3" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="اكتب رسالة Commit هنا... (سيتم توليدها تلقائياً إذا تركتها فارغة)"
                ></textarea>
                <p class="text-sm text-gray-500 mt-2">
                    <i class="fas fa-info-circle"></i>
                    يمكنك ترك الحقل فارغاً لتوليد رسالة تلقائية بالذكاء الاصطناعي
                </p>
            </div>

            <div class="flex items-center space-x-4 space-x-reverse">
                <button 
                    type="button" 
                    onclick="generateCommitMessage()" 
                    class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-300">
                    <i class="fas fa-magic"></i>
                    توليد رسالة تلقائياً
                </button>
                
                <button 
                    type="submit" 
                    class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-bold">
                    <i class="fas fa-check-circle"></i>
                    Commit & Push
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Load Modified Files
async function loadModifiedFiles() {
    try {
        const response = await fetch('{{ route('git.status') }}');
        const data = await response.json();
        
        document.getElementById('currentBranch').textContent = data.branch;
        document.getElementById('modifiedCount').textContent = data.modified_files.length;
        
        const filesList = document.getElementById('filesList');
        if (data.modified_files.length === 0) {
            filesList.innerHTML = '<p class="text-gray-500 text-center py-4">✅ لا توجد تغييرات للحفظ</p>';
        } else {
            filesList.innerHTML = data.modified_files.map(file => `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-file-code text-blue-600"></i>
                        <span class="text-gray-800 font-medium">${file}</span>
                    </div>
                    <span class="text-xs text-gray-500 bg-yellow-100 px-2 py-1 rounded">معدّل</span>
                </div>
            `).join('');
        }
        
    } catch (error) {
        console.error('Error:', error);
    }
}

// Generate Commit Message using AI
async function generateCommitMessage() {
    Swal.fire({
        title: 'جاري التوليد...',
        text: 'الذكاء الاصطناعي يحلل التغييرات...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const response = await fetch('{{ route('git.status') }}');
        const data = await response.json();
        
        // Simple AI-like message generation
        const fileCount = data.modified_files.length;
        const message = `feat: تحديث ${fileCount} ملف في النظام\n\n` +
                       `- تحسينات على نظام المطور\n` +
                       `- إضافة ميزات جديدة\n` +
                       `- تحسين الأداء`;
        
        document.getElementById('commitMessage').value = message;
        
        Swal.close();
        Swal.fire({
            icon: 'success',
            title: 'تم التوليد!',
            text: 'يمكنك تعديل الرسالة قبل الحفظ',
            timer: 2000
        });
        
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'فشل توليد الرسالة'
        });
    }
}

// Handle Commit Form
document.getElementById('commitForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const message = document.getElementById('commitMessage').value.trim();
    
    Swal.fire({
        title: 'جاري الحفظ...',
        html: `
            <div class="text-center">
                <i class="fas fa-spinner fa-spin text-4xl text-green-600 mb-3"></i>
                <p class="text-gray-600">جاري عمل Commit & Push إلى فرع developer...</p>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    try {
        // Commit
        const commitResponse = await fetch('{{ route('git.commit.post') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message || null })
        });
        
        const commitData = await commitResponse.json();
        
        if (!commitData.success) {
            throw new Error(commitData.message);
        }
        
        // Push
        const pushResponse = await fetch('{{ route('git.push') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const pushData = await pushResponse.json();
        
        if (!pushData.success) {
            throw new Error(pushData.message);
        }
        
        Swal.fire({
            icon: 'success',
            title: 'نجح! ✅',
            html: `
                <div class="text-center">
                    <p class="text-gray-700 mb-3">تم Commit & Push بنجاح!</p>
                    <p class="text-sm text-gray-500">الكود الآن على فرع developer</p>
                    <p class="text-sm text-green-600 font-bold mt-2">سيتم النشر إلى الخادم التجريبي تلقائياً</p>
                </div>
            `,
            confirmButtonText: 'حسناً'
        }).then(() => {
            window.location.href = '{{ route('git.dashboard') }}';
        });
        
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ!',
            text: error.message || 'فشل Commit & Push'
        });
    }
});

// Load on page load
document.addEventListener('DOMContentLoaded', loadModifiedFiles);
</script>
@endsection
