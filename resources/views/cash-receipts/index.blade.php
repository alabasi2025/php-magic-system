@extends('layouts.app')

@section('title', 'سندات القبض')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-white text-xl"></i>
                        </div>
                        سندات القبض
                    </h1>
                    <p class="text-green-100 mt-2">إدارة وتتبع جميع سندات القبض النقدية والبنكية</p>
                </div>
                <a href="{{ route('cash-receipts.create') }}" 
                   class="bg-white text-green-600 hover:bg-green-50 px-6 py-3 rounded-xl font-semibold shadow-lg transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>سند قبض جديد</span>
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border-r-4 border-green-500 p-4 mb-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 text-2xl ml-3"></i>
                <p class="text-green-700 font-semibold">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border-r-4 border-red-500 p-4 mb-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 text-2xl ml-3"></i>
                <p class="text-red-700 font-semibold">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-lg p-6 border-r-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold mb-1">إجمالي السندات</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-invoice text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-r-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold mb-1">قيد المراجعة</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-r-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold mb-1">معتمد</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['approved'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-r-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold mb-1">مرحّل</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['posted'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-double text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-r-4 border-emerald-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold mb-1">إجمالي المبلغ</p>
                        <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['total_amount'], 2) }}</p>
                        <p class="text-xs text-gray-500">ريال</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-coins text-emerald-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-filter text-blue-500"></i>
                البحث والتصفية
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">البحث</label>
                    <input type="text" 
                           id="searchInput"
                           placeholder="ابحث برقم السند أو المستلم منه..."
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
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
                        <option value="cancelled">ملغى</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">من تاريخ</label>
                    <input type="date" 
                           id="dateFrom"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">إلى تاريخ</label>
                    <input type="date" 
                           id="dateTo"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                </div>
            </div>
        </div>

        <!-- Receipts Table -->
        @if($receipts->isEmpty())
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-dashed border-green-300 rounded-2xl p-12 text-center">
            <i class="fas fa-inbox text-green-400 text-7xl mb-6"></i>
            <h3 class="text-2xl font-bold text-green-800 mb-3">لا توجد سندات قبض</h3>
            <p class="text-green-600 mb-6">ابدأ بإنشاء سند قبض جديد</p>
            <a href="{{ route('cash-receipts.create') }}" 
               class="inline-block bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-xl font-bold shadow-lg transition-all duration-300">
                <i class="fas fa-plus-circle ml-2"></i>
                إنشاء سند قبض
            </a>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-green-500 to-emerald-600">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">#</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">رقم السند</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">التاريخ</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">المستلم منه</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">الحساب</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">المبلغ</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">الحالة</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($receipts as $index => $receipt)
                        <tr class="hover:bg-green-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $receipts->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-blue-600 font-bold">{{ $receipt->receipt_number }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <i class="fas fa-calendar ml-2 text-gray-400"></i>
                                {{ $receipt->receipt_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $receipt->received_from }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                @if($receipt->account_type === 'cash_box')
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                        <i class="fas fa-cash-register ml-1"></i>
                                        صندوق
                                    </span>
                                @else
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                                        <i class="fas fa-university ml-1"></i>
                                        بنك
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-lg font-bold text-green-600">
                                    {{ number_format($receipt->amount, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($receipt->status === 'draft')
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-bold">
                                        <i class="fas fa-file ml-1"></i>مسودة
                                    </span>
                                @elseif($receipt->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold">
                                        <i class="fas fa-clock ml-1"></i>معلق
                                    </span>
                                @elseif($receipt->status === 'approved')
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">
                                        <i class="fas fa-check-circle ml-1"></i>معتمد
                                    </span>
                                @elseif($receipt->status === 'posted')
                                    <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-bold">
                                        <i class="fas fa-check-double ml-1"></i>مرحّل
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-bold">
                                        <i class="fas fa-times-circle ml-1"></i>ملغى
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex gap-2">
                                    <a href="{{ route('cash-receipts.show', $receipt) }}" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg transition-all">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(in_array($receipt->status, ['draft', 'pending']))
                                    <a href="{{ route('cash-receipts.edit', $receipt) }}" 
                                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg transition-all">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    @if(in_array($receipt->status, ['draft', 'cancelled']))
                                    <form action="{{ route('cash-receipts.destroy', $receipt) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('هل أنت متأكد من حذف هذا السند؟')"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition-all">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($receipts->hasPages())
        <div class="mt-6">
            {{ $receipts->links() }}
        </div>
        @endif
        @endif
    </div>
</div>

@push('scripts')
<script>
// Simple client-side filtering
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const status = statusFilter.value.toLowerCase();
        const from = dateFrom.value;
        const to = dateTo.value;
        
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const matchesSearch = text.includes(searchTerm);
            const matchesStatus = !status || text.includes(status);
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    searchInput?.addEventListener('input', filterTable);
    statusFilter?.addEventListener('change', filterTable);
    dateFrom?.addEventListener('change', filterTable);
    dateTo?.addEventListener('change', filterTable);
});
</script>
@endpush
@endsection
