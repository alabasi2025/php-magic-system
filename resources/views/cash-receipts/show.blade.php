@extends('layouts.app')

@section('title', 'عرض سند القبض')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl shadow-xl p-8 mb-8 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold mb-2 flex items-center">
                    <i class="fas fa-file-invoice-dollar mr-4"></i>
                    سند قبض رقم: {{ $cashReceipt->receipt_number }}
                </h1>
                <p class="text-green-100 text-lg">تفاصيل سند القبض</p>
            </div>
            <div class="flex gap-3">
                @if($cashReceipt->status == 'draft')
                <a href="{{ route('cash-receipts.edit', $cashReceipt) }}" class="bg-white text-green-600 px-6 py-3 rounded-xl font-bold hover:bg-green-50 transition-all duration-300 shadow-lg flex items-center">
                    <i class="fas fa-edit mr-2"></i>
                    تعديل
                </a>
                @endif
                <a href="{{ route('cash-receipts.index') }}" class="bg-white/20 text-white px-6 py-3 rounded-xl font-bold hover:bg-white/30 transition-all duration-300 flex items-center">
                    <i class="fas fa-arrow-right mr-2"></i>
                    رجوع
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Receipt Details -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        معلومات السند
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">رقم السند</label>
                            <p class="text-lg font-bold text-gray-900">{{ $cashReceipt->receipt_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">تاريخ السند</label>
                            <p class="text-lg text-gray-900 flex items-center">
                                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                {{ $cashReceipt->receipt_date->format('Y-m-d') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">المستلم منه</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $cashReceipt->received_from }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">المبلغ</label>
                            <p class="text-2xl font-bold text-green-600">{{ number_format($cashReceipt->amount, 2) }} ريال</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">الحساب</label>
                            <p class="text-lg text-gray-900">
                                <i class="fas {{ $cashReceipt->account_type == 'App\\Models\\CashBox' ? 'fa-cash-register' : 'fa-university' }} text-gray-400 mr-2"></i>
                                {{ $cashReceipt->account->name }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">طريقة الدفع</label>
                            <p class="text-lg text-gray-900">
                                @if($cashReceipt->payment_method == 'cash')
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-money-bill-wave mr-1"></i> نقدي
                                    </span>
                                @elseif($cashReceipt->payment_method == 'check')
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                        <i class="fas fa-money-check mr-1"></i> شيك
                                    </span>
                                @elseif($cashReceipt->payment_method == 'transfer')
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
                                        <i class="fas fa-exchange-alt mr-1"></i> تحويل
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">
                                        <i class="fas fa-credit-card mr-1"></i> بطاقة
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($cashReceipt->payment_method == 'check')
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-money-check text-blue-600 mr-2"></i>
                            معلومات الشيك
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">رقم الشيك</label>
                                <p class="text-lg text-gray-900">{{ $cashReceipt->check_number }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">تاريخ الشيك</label>
                                <p class="text-lg text-gray-900">{{ $cashReceipt->check_date ? $cashReceipt->check_date->format('Y-m-d') : '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">البنك</label>
                                <p class="text-lg text-gray-900">{{ $cashReceipt->check_bank }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($cashReceipt->reference_number)
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-600 mb-1">رقم المرجع</label>
                        <p class="text-lg text-gray-900">{{ $cashReceipt->reference_number }}</p>
                    </div>
                    @endif

                    @if($cashReceipt->description)
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-600 mb-1">الوصف</label>
                        <p class="text-gray-900 leading-relaxed">{{ $cashReceipt->description }}</p>
                    </div>
                    @endif

                    @if($cashReceipt->notes)
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-600 mb-1">ملاحظات</label>
                        <p class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-lg">{{ $cashReceipt->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Journal Entry -->
            @if($cashReceipt->journal_entry_id)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-book text-purple-600 mr-2"></i>
                        القيد المحاسبي
                    </h2>
                </div>
                <div class="p-6">
                    <a href="{{ route('journal-entries.show', $cashReceipt->journal_entry_id) }}" class="flex items-center justify-between p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <div>
                            <p class="text-sm text-gray-600">رقم القيد</p>
                            <p class="text-lg font-bold text-purple-600">{{ $cashReceipt->journalEntry->entry_number ?? 'N/A' }}</p>
                        </div>
                        <i class="fas fa-arrow-left text-purple-600"></i>
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-tasks text-blue-600 mr-2"></i>
                        الحالة
                    </h2>
                </div>
                <div class="p-6">
                    <div class="text-center mb-4">
                        @if($cashReceipt->status == 'draft')
                            <div class="inline-flex items-center px-6 py-3 rounded-full bg-gray-100 text-gray-800 font-bold text-lg">
                                <i class="fas fa-file-alt mr-2"></i> مسودة
                            </div>
                        @elseif($cashReceipt->status == 'pending')
                            <div class="inline-flex items-center px-6 py-3 rounded-full bg-purple-100 text-purple-800 font-bold text-lg">
                                <i class="fas fa-clock mr-2"></i> قيد المراجعة
                            </div>
                        @elseif($cashReceipt->status == 'approved')
                            <div class="inline-flex items-center px-6 py-3 rounded-full bg-green-100 text-green-800 font-bold text-lg">
                                <i class="fas fa-check-circle mr-2"></i> معتمد
                            </div>
                        @elseif($cashReceipt->status == 'posted')
                            <div class="inline-flex items-center px-6 py-3 rounded-full bg-yellow-100 text-yellow-800 font-bold text-lg">
                                <i class="fas fa-paper-plane mr-2"></i> مرحّل
                            </div>
                        @else
                            <div class="inline-flex items-center px-6 py-3 rounded-full bg-red-100 text-red-800 font-bold text-lg">
                                <i class="fas fa-times-circle mr-2"></i> ملغى
                            </div>
                        @endif
                    </div>

                    @if(in_array($cashReceipt->status, ['draft', 'pending']))
                    <form action="{{ route('cash-receipts.approve', $cashReceipt) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-green-700 transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            اعتماد السند
                        </button>
                    </form>
                    @endif

                    @if($cashReceipt->approved_by)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600 mb-1">تم الاعتماد بواسطة</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $cashReceipt->approver->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ $cashReceipt->approved_at ? $cashReceipt->approved_at->format('Y-m-d H:i') : '-' }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Creator Info -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-user text-blue-600 mr-2"></i>
                        معلومات الإنشاء
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">أنشئ بواسطة</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $cashReceipt->creator->name ?? 'النظام' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">تاريخ الإنشاء</p>
                        <p class="text-gray-900">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ $cashReceipt->created_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                    @if($cashReceipt->updated_at != $cashReceipt->created_at)
                    <div>
                        <p class="text-sm text-gray-600 mb-1">آخر تحديث</p>
                        <p class="text-gray-900">
                            <i class="fas fa-clock mr-1"></i>
                            {{ $cashReceipt->updated_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-cog text-blue-600 mr-2"></i>
                        إجراءات
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <button onclick="window.print()" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-print mr-2"></i>
                        طباعة
                    </button>
                    @if($cashReceipt->status == 'draft')
                    <form action="{{ route('cash-receipts.destroy', $cashReceipt) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا السند؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-red-700 transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-trash mr-2"></i>
                            حذف السند
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
