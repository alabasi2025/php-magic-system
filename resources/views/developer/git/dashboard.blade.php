@extends('layouts.app')

@section('title', 'لوحة Git - نظام المطور')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fab fa-git-alt text-green-600"></i>
            لوحة Git
        </h1>
        <p class="text-gray-600">عرض حالة المستودع والتغييرات الحالية</p>
    </div>

    <!-- Git Status Card -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-info-circle text-blue-600"></i>
            حالة المستودع
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">الفرع الحالي</p>
                        <p class="text-2xl font-bold text-green-600" id="currentBranch">...</p>
                    </div>
                    <i class="fab fa-git-alt text-4xl text-green-600 opacity-20"></i>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">ملفات معدّلة</p>
                        <p class="text-2xl font-bold text-blue-600" id="modifiedCount">0</p>
                    </div>
                    <i class="fas fa-file-code text-4xl text-blue-600 opacity-20"></i>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">آخر Commit</p>
                        <p class="text-sm font-bold text-purple-600" id="lastCommit">...</p>
                    </div>
                    <i class="fas fa-code-branch text-4xl text-purple-600 opacity-20"></i>
                </div>
            </div>
        </div>

        <!-- Modified Files List -->
        <div id="modifiedFiles" class="mt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">الملفات المعدّلة</h3>
            <div id="filesList" class="space-y-2">
                <p class="text-gray-500 text-center py-4">جاري التحميل...</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('developer.git.commit') }}" class="bg-gradient-to-r from-green-500 to-emerald-600 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <i class="fas fa-check-circle text-3xl mb-3"></i>
            <h3 class="text-xl font-bold mb-2">Commit & Push</h3>
            <p class="text-sm opacity-90">حفظ التغييرات ونشرها</p>
        </a>
        
        <a href="{{ route('developer.git.log') }}" class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <i class="fas fa-history text-3xl mb-3"></i>
            <h3 class="text-xl font-bold mb-2">سجل التغييرات</h3>
            <p class="text-sm opacity-90">عرض جميع الـ Commits</p>
        </a>
        
        <button onclick="refreshStatus()" class="bg-gradient-to-r from-purple-500 to-pink-600 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <i class="fas fa-sync-alt text-3xl mb-3"></i>
            <h3 class="text-xl font-bold mb-2">تحديث الحالة</h3>
            <p class="text-sm opacity-90">إعادة تحميل البيانات</p>
        </button>
    </div>
</div>

<script>
// Load Git Status
async function loadGitStatus() {
    try {
        const response = await fetch('{{ route('developer.git.status') }}');
        const data = await response.json();
        
        // Update UI
        document.getElementById('currentBranch').textContent = data.branch;
        document.getElementById('modifiedCount').textContent = data.modified_files.length;
        document.getElementById('lastCommit').textContent = data.last_commit.substring(0, 50) + '...';
        
        // Display modified files
        const filesList = document.getElementById('filesList');
        if (data.modified_files.length === 0) {
            filesList.innerHTML = '<p class="text-gray-500 text-center py-4">لا توجد تغييرات</p>';
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
        console.error('Error loading git status:', error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'فشل تحميل حالة Git'
        });
    }
}

function refreshStatus() {
    Swal.fire({
        title: 'جاري التحديث...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    loadGitStatus().then(() => {
        Swal.close();
        Swal.fire({
            icon: 'success',
            title: 'تم التحديث',
            timer: 1500,
            showConfirmButton: false
        });
    });
}

// Load on page load
document.addEventListener('DOMContentLoaded', loadGitStatus);
</script>
@endsection
