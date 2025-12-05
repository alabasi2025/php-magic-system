@extends('layouts.app')

@section('title', 'سجل التغييرات - نظام المطور')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-history text-blue-600"></i>
            سجل التغييرات (Git History)
        </h1>
        <p class="text-gray-600">عرض جميع الـ Commits على فرع developer</p>
    </div>

    <!-- Commits List -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div id="commitsList">
            <p class="text-gray-500 text-center py-8">جاري التحميل...</p>
        </div>
    </div>
</div>

<script>
// Load Git History
async function loadGitHistory() {
    try {
        const response = await fetch('{{ route('developer.git.history') }}');
        const data = await response.json();
        
        const commitsList = document.getElementById('commitsList');
        
        if (data.commits.length === 0) {
            commitsList.innerHTML = '<p class="text-gray-500 text-center py-8">لا توجد Commits</p>';
            return;
        }
        
        commitsList.innerHTML = data.commits.map((commit, index) => `
            <div class="border-b border-gray-200 last:border-0 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 space-x-reverse mb-2">
                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">#${index + 1}</span>
                            <h3 class="text-lg font-bold text-gray-800">${commit.message}</h3>
                        </div>
                        <div class="flex items-center space-x-4 space-x-reverse text-sm text-gray-600">
                            <span>
                                <i class="fas fa-code-branch text-green-600"></i>
                                ${commit.hash.substring(0, 7)}
                            </span>
                            <span>
                                <i class="fas fa-user text-purple-600"></i>
                                ${commit.author}
                            </span>
                            <span>
                                <i class="fas fa-clock text-orange-600"></i>
                                ${commit.date}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
        
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('commitsList').innerHTML = 
            '<p class="text-red-500 text-center py-8">فشل تحميل السجل</p>';
    }
}

// Load on page load
document.addEventListener('DOMContentLoaded', loadGitHistory);
</script>
@endsection
