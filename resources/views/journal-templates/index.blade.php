@extends('layouts.app')

@section('title', 'قوالب القيود الذكية')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-layer-group text-white text-xl"></i>
                        </div>
                        قوالب القيود الذكية
                    </h1>
                    <p class="text-gray-600 mt-2 mr-16">إدارة وإنشاء قوالب جاهزة للقيود المحاسبية المتكررة</p>
                </div>
                <a href="{{ route('journal-templates.create') }}" 
                   class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>إضافة قالب جديد</span>
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="bg-green-50 border-r-4 border-green-500 rounded-xl p-4 mb-6 shadow-md animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-white"></i>
                </div>
                <div>
                    <p class="text-green-800 font-semibold">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Filter & Search Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">البحث</label>
                    <div class="relative">
                        <input type="text" id="searchInput" 
                               placeholder="ابحث عن قالب..." 
                               class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الفئة</label>
                    <select id="categoryFilter" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                        <option value="">جميع الفئات</option>
                        <option value="مبيعات">مبيعات</option>
                        <option value="مشتريات">مشتريات</option>
                        <option value="رواتب">رواتب</option>
                        <option value="مصروفات">مصروفات</option>
                        <option value="عام">عام</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الحالة</label>
                    <select id="statusFilter" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                        <option value="">الكل</option>
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-semibold">إجمالي القوالب</p>
                        <p class="text-3xl font-bold mt-2">{{ $templates->total() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-layer-group text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-semibold">القوالب النشطة</p>
                        <p class="text-3xl font-bold mt-2">{{ $templates->where('is_active', true)->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-semibold">الفئات</p>
                        <p class="text-3xl font-bold mt-2">{{ $templates->unique('category')->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tags text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-semibold">تم الاستخدام</p>
                        <p class="text-3xl font-bold mt-2">0</p>
                    </div>
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-magic text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Templates Grid -->
        <div id="templatesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($templates as $template)
            <div class="template-card bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden"
                 data-name="{{ strtolower($template->name) }}"
                 data-category="{{ strtolower($template->category ?? 'عام') }}"
                 data-status="{{ $template->is_active ? 'active' : 'inactive' }}">
                
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 border-b-2 border-gray-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $template->name }}</h3>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                    {{ $template->category ?? 'عام' }}
                                </span>
                                @if($template->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full flex items-center gap-1">
                                    <i class="fas fa-check text-xs"></i> نشط
                                </span>
                                @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full flex items-center gap-1">
                                    <i class="fas fa-times text-xs"></i> غير نشط
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-file-invoice text-white"></i>
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6">
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        {{ $template->description ?? 'لا يوجد وصف' }}
                    </p>

                    <!-- Template Info -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-user text-blue-500 w-5"></i>
                            <span>{{ $template->creator->name ?? 'غير معروف' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-calendar text-green-500 w-5"></i>
                            <span>{{ $template->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-list text-purple-500 w-5"></i>
                            <span>{{ is_array($template->template_data) ? count($template->template_data['entries'] ?? []) : 0 }} سطر</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 pt-4 border-t border-gray-200">
                        <a href="{{ route('journal-templates.use', $template) }}" 
                           class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-xl font-semibold text-center transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fas fa-magic"></i>
                            <span>استخدام</span>
                        </a>
                        <a href="{{ route('journal-templates.edit', $template) }}" 
                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-xl transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="toggleTemplate({{ $template->id }}, {{ $template->is_active ? 'false' : 'true' }})"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-power-off"></i>
                        </button>
                        <button onclick="deleteTemplate({{ $template->id }})" 
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-inbox text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">لا توجد قوالب حالياً</h3>
                    <p class="text-gray-600 mb-6">ابدأ بإنشاء قالبك الأول للقيود المتكررة</p>
                    <a href="{{ route('journal-templates.create') }}" 
                       class="inline-block bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                        <i class="fas fa-plus ml-2"></i>
                        إنشاء قالب جديد
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($templates->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $templates->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-scale-in">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">تأكيد الحذف</h3>
            <p class="text-gray-600 mb-6">هل أنت متأكد من حذف هذا القالب؟ لا يمكن التراجع عن هذا الإجراء.</p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" 
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-xl font-semibold transition-all duration-300">
                    إلغاء
                </button>
                <button onclick="confirmDelete()" 
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300">
                    حذف
                </button>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="toggleForm" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="is_active" id="toggleActive">
</form>

@endsection

@push('scripts')
<script>
// Search and Filter
document.getElementById('searchInput').addEventListener('input', filterTemplates);
document.getElementById('categoryFilter').addEventListener('change', filterTemplates);
document.getElementById('statusFilter').addEventListener('change', filterTemplates);

function filterTemplates() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    
    document.querySelectorAll('.template-card').forEach(card => {
        const name = card.dataset.name;
        const cardCategory = card.dataset.category;
        const cardStatus = card.dataset.status;
        
        const matchSearch = !search || name.includes(search);
        const matchCategory = !category || cardCategory === category;
        const matchStatus = !status || cardStatus === status;
        
        if (matchSearch && matchCategory && matchStatus) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Delete Template
let deleteTemplateId = null;

function deleteTemplate(id) {
    deleteTemplateId = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    deleteTemplateId = null;
}

function confirmDelete() {
    if (deleteTemplateId) {
        const form = document.getElementById('deleteForm');
        form.action = `/journal-templates/${deleteTemplateId}`;
        form.submit();
    }
}

// Toggle Template Status
function toggleTemplate(id, newStatus) {
    const form = document.getElementById('toggleForm');
    form.action = `/journal-templates/${id}/toggle`;
    document.getElementById('toggleActive').value = newStatus;
    form.submit();
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes scale-in {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.animate-scale-in {
    animation: scale-in 0.3s ease-out;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
