@extends('layouts.app')

@section('title', 'سندات الصرف')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl shadow-xl p-8 mb-8 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold mb-2 flex items-center">
                    <i class="fas fa-hand-holding-usd mr-4"></i>
                    سندات الصرف
                </h1>
                <p class="text-red-100 text-lg">إدارة وتتبع جميع سندات الصرف النقدية والبنكية</p>
            </div>
            <a href="{{ route('cash-payments.create') }}" class="bg-white text-red-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-red-50 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center">
                <i class="fas fa-plus-circle mr-2"></i>
                سند صرف جديد
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <!-- Total Payments -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">إجمالي السندات</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $payments->total() }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-file-invoice text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">قيد المراجعة</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $payments->where('status', 'pending')->count() }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <i class="fas fa-clock text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Approved -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">معتمد</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $payments->where('status', 'approved')->count() }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Posted -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">مرحّل</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $payments->where('status', 'posted')->count() }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-4">
                    <i class="fas fa-paper-plane text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Amount -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold mb-1">إجمالي المبلغ</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($payments->sum('amount'), 2) }}</p>
                    <p class="text-xs text-gray-500">ريال</p>
                </div>
                <div class="bg-red-100 rounded-full p-4">
                    <i class="fas fa-money-bill-wave text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-filter text-blue-600 mr-2"></i>
            البحث والتصفية
        </h3>
        <form method="GET" action="{{ route('cash-payments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="searchInput" class="block text-sm font-semibold text-gray-700 mb-2">البحث</label>
                <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="ابحث برقم السند أو المدفوع له..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
            </div>
            <div>
                <label for="statusFilter" class="block text-sm font-semibold text-gray-700 mb-2">الحالة</label>
                <select id="statusFilter" name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                    <option value="">جميع الحالات</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                    <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>مرحّل</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغى</option>
                </select>
            </div>
            <div>
                <label for="dateFrom" class="block text-sm font-semibold text-gray-700 mb-2">من تاريخ</label>
                <input type="date" id="dateFrom" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
            </div>
            <div>
                <label for="dateTo" class="block text-sm font-semibold text-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" id="dateTo" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    @if($payments->count() > 0)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">#</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">رقم السند</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">المدفوع له</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الحساب</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">المبلغ</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">طريقة الدفع</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($payments as $index => $payment)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payments->firstItem() + $index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-blue-600">{{ $payment->payment_number }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>
                            {{ $payment->payment_date->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->paid_to }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->account->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-red-600">{{ number_format($payment->amount, 2) }} ريال</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($payment->payment_method == 'cash')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">نقدي</span>
                            @elseif($payment->payment_method == 'check')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">شيك</span>
                            @elseif($payment->payment_method == 'transfer')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">تحويل</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">بطاقة</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payment->status == 'draft')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">مسودة</span>
                            @elseif($payment->status == 'pending')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">قيد المراجعة</span>
                            @elseif($payment->status == 'approved')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">معتمد</span>
                            @elseif($payment->status == 'posted')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">مرحّل</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">ملغى</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <a href="{{ route('cash-payments.show', $payment) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($payment->status == 'draft')
                                <a href="{{ route('cash-payments.edit', $payment) }}" class="text-yellow-600 hover:text-yellow-800 transition-colors" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                @if(in_array($payment->status, ['draft', 'pending']))
                                <form action="{{ route('cash-payments.approve', $payment) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 transition-colors" title="اعتماد">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                                @endif
                                @if($payment->status == 'draft')
                                <form action="{{ route('cash-payments.destroy', $payment) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا السند؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition-colors" title="حذف">
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
        
        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50">
            {{ $payments->links() }}
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-lg p-12 text-center">
        <div class="text-gray-400 mb-4">
            <i class="fas fa-hand-holding-usd text-6xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-700 mb-2">لا توجد سندات صرف</h3>
        <p class="text-gray-500 mb-6">ابدأ بإنشاء سند صرف جديد</p>
        <a href="{{ route('cash-payments.create') }}" class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
            <i class="fas fa-plus-circle mr-2"></i>
            إنشاء سند صرف
        </a>
    </div>
    @endif
</div>

<script>
// Auto-submit filters
document.getElementById('statusFilter').addEventListener('change', function() {
    this.form.submit();
});

document.getElementById('dateFrom').addEventListener('change', function() {
    this.form.submit();
});

document.getElementById('dateTo').addEventListener('change', function() {
    this.form.submit();
});

// Search with delay
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        this.form.submit();
    }, 500);
});
</script>
@endsection
