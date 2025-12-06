@extends('layouts.app')

@section('title', 'القيود المحاسبية')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-file-invoice text-white text-xl"></i>
                        </div>
                        القيود المحاسبية
                    </h1>
                    <p class="text-gray-600 mt-2 mr-16">إدارة وتتبع جميع القيود اليومية والمحاسبية</p>
                </div>
                <a href="{{ route('journal-entries.create') }}" 
                   class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>إضافة قيد جديد</span>
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
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">البحث</label>
                    <div class="relative">
                        <input type="text" id="searchInput" 
                               placeholder="ابحث برقم القيد أو المرجع..." 
                               class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الحالة</label>
                    <select id="statusFilter" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                        <option value="">جميع الحالات</option>
                        <option value="draft">مسودة</option>
                        <option value="pending">قيد المراجعة</option>
                        <option value="approved">معتمد</option>
                        <option value="posted">مرحّل</option>
                        <option value="rejected">مرفوض</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">من تاريخ</label>
                    <input type="date" id="dateFrom" 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">إلى تاريخ</label>
                    <input type="date" id="dateTo" 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-semibold">إجمالي القيود</p>
                        <p class="text-3xl font-bold mt-2">{{ $entries->total() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-invoice text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-semibold">قيد المراجعة</p>
                        <p class="text-3xl font-bold mt-2">{{ $entries->where('status', 'pending')->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-semibold">معتمد</p>
                        <p class="text-3xl font-bold mt-2">{{ $entries->where('status', 'approved')->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-semibold">مرحّل</p>
                        <p class="text-3xl font-bold mt-2">{{ $entries->where('status', 'posted')->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-share text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-semibold">مرفوض</p>
                        <p class="text-3xl font-bold mt-2">{{ $entries->where('status', 'rejected')->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Entries Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">#</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">رقم القيد</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">التاريخ</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">الوصف</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">المرجع</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">المدين</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">الدائن</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">الحالة</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($entries as $entry)
                        <tr class="entry-row hover:bg-gray-50 transition-colors duration-200"
                            data-number="{{ strtolower($entry->entry_number) }}"
                            data-reference="{{ strtolower($entry->reference ?? '') }}"
                            data-status="{{ $entry->status }}"
                            data-date="{{ $entry->entry_date->format('Y-m-d') }}">
                            <td class="px-6 py-4 text-gray-600">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-blue-600">{{ $entry->entry_number }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                <i class="fas fa-calendar text-gray-400 ml-2"></i>
                                {{ $entry->entry_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 text-gray-700 max-w-xs truncate">
                                {{ $entry->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $entry->reference ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-green-600 font-bold">{{ number_format($entry->total_debit, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-red-600 font-bold">{{ number_format($entry->total_credit, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($entry->status == 'draft')
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">مسودة</span>
                                @elseif($entry->status == 'pending')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">قيد المراجعة</span>
                                @elseif($entry->status == 'approved')
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">معتمد</span>
                                @elseif($entry->status == 'posted')
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">مرحّل</span>
                                @elseif($entry->status == 'rejected')
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">مرفوض</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('journal-entries.show', $entry) }}" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg transition-all duration-300 flex items-center justify-center"
                                       title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(!in_array($entry->status, ['posted', 'approved']))
                                    <a href="{{ route('journal-entries.edit', $entry) }}" 
                                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg transition-all duration-300 flex items-center justify-center"
                                       title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteEntry({{ $entry->id }})" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition-all duration-300 flex items-center justify-center"
                                            title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-inbox text-gray-400 text-4xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">لا توجد قيود محاسبية</h3>
                                    <p class="text-gray-600 mb-4">ابدأ بإنشاء قيدك الأول</p>
                                    <a href="{{ route('journal-entries.create') }}" 
                                       class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                                        <i class="fas fa-plus ml-2"></i>
                                        إنشاء قيد جديد
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($entries->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $entries->links() }}
            </div>
            @endif
        </div>
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
            <p class="text-gray-600 mb-6">هل أنت متأكد من حذف هذا القيد؟ لا يمكن التراجع عن هذا الإجراء.</p>
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

@endsection

@push('scripts')
<script>
// Search and Filter
document.getElementById('searchInput').addEventListener('input', filterEntries);
document.getElementById('statusFilter').addEventListener('change', filterEntries);
document.getElementById('dateFrom').addEventListener('change', filterEntries);
document.getElementById('dateTo').addEventListener('change', filterEntries);

function filterEntries() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    
    document.querySelectorAll('.entry-row').forEach(row => {
        const number = row.dataset.number;
        const reference = row.dataset.reference;
        const rowStatus = row.dataset.status;
        const rowDate = row.dataset.date;
        
        const matchSearch = !search || number.includes(search) || reference.includes(search);
        const matchStatus = !status || rowStatus === status;
        const matchDateFrom = !dateFrom || rowDate >= dateFrom;
        const matchDateTo = !dateTo || rowDate <= dateTo;
        
        if (matchSearch && matchStatus && matchDateFrom && matchDateTo) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Delete Entry
let deleteEntryId = null;

function deleteEntry(id) {
    deleteEntryId = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    deleteEntryId = null;
}

function confirmDelete() {
    if (deleteEntryId) {
        const form = document.getElementById('deleteForm');
        form.action = `/journal-entries/${deleteEntryId}`;
        form.submit();
    }
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
</style>
@endpush
