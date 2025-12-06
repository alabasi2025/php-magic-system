@extends('layouts.app')

@section('title', 'تفاصيل سند الصرف')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header and Back Button --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">تفاصيل سند الصرف رقم: <span class="text-red-600">{{ $cashPayment->voucher_number }}</span></h1>
        <a href="{{ route('cash-payments.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg inline-flex items-center transition duration-150 ease-in-out">
            <i class="fas fa-arrow-right mr-2"></i>
            العودة إلى القائمة
        </a>
    </div>

    {{-- Success/Error Messages (Tailwind style) --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">نجاح!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Action Buttons --}}
    <div class="flex space-x-4 space-x-reverse mb-6">
        <a href="{{ route('cash-payments.edit', $cashPayment->id) }}" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center transition duration-150 ease-in-out shadow-md">
            <i class="fas fa-edit ml-2"></i>
            تعديل
        </a>
        <button type="button" onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center transition duration-150 ease-in-out shadow-md">
            <i class="fas fa-print ml-2"></i>
            طباعة
        </button>
        {{-- Delete Button (with confirmation) --}}
        <form action="{{ route('cash-payments.destroy', $cashPayment->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف سند الصرف هذا؟')" class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center transition duration-150 ease-in-out shadow-md">
                <i class="fas fa-trash-alt ml-2"></i>
                حذف
            </button>
        </form>
    </div>

    {{-- Main Details Card --}}
    <div class="bg-white shadow-xl rounded-lg overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-200 bg-red-500 text-white">
            <h2 class="text-xl font-semibold flex items-center">
                <i class="fas fa-money-bill-wave ml-2"></i>
                بيانات السند الأساسية
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Date --}}
            <div class="detail-item">
                <p class="text-sm font-medium text-gray-500">تاريخ السند</p>
                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $cashPayment->date }}</p>
            </div>
            {{-- Amount --}}
            <div class="detail-item">
                <p class="text-sm font-medium text-gray-500">المبلغ</p>
                <p class="mt-1 text-lg font-semibold text-red-600">{{ number_format($cashPayment->amount, 2) }} {{ $cashPayment->currency }}</p>
            </div>
            {{-- Payee/Beneficiary --}}
            <div class="detail-item">
                <p class="text-sm font-medium text-gray-500">المستفيد</p>
                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $cashPayment->payee_name }}</p>
            </div>
            {{-- Payment Method --}}
            <div class="detail-item">
                <p class="text-sm font-medium text-gray-500">طريقة الدفع</p>
                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $cashPayment->payment_method }}</p>
            </div>
            {{-- Account Paid From --}}
            <div class="detail-item">
                <p class="text-sm font-medium text-gray-500">الحساب المصروف منه</p>
                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $cashPayment->account->name ?? 'N/A' }}</p>
            </div>
            {{-- Status --}}
            <div class="detail-item">
                <p class="text-sm font-medium text-gray-500">الحالة</p>
                <p class="mt-1 text-lg font-semibold text-gray-900">
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $cashPayment->status == 'posted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $cashPayment->status == 'posted' ? 'مرحل' : 'مسودة' }}
                    </span>
                </p>
            </div>
        </div>
        <div class="p-6 border-t border-gray-200">
            <p class="text-sm font-medium text-gray-500">الوصف/البيان</p>
            <p class="mt-1 text-lg text-gray-900 whitespace-pre-wrap">{{ $cashPayment->description }}</p>
        </div>
    </div>

    {{-- Related Journal Entry Card (if applicable) --}}
    @if(isset($cashPayment->journalEntry))
    <div class="bg-white shadow-xl rounded-lg overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-200 bg-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-book ml-2"></i>
                القيد المحاسبي المرتبط
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div class="detail-item">
                    <p class="text-sm font-medium text-gray-500">رقم القيد</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $cashPayment->journalEntry->entry_number }}</p>
                </div>
                <div class="detail-item">
                    <p class="text-sm font-medium text-gray-500">تاريخ القيد</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $cashPayment->journalEntry->date }}</p>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">تفاصيل الأرصدة</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحساب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مدين</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">دائن</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cashPayment->journalEntry->lines as $line)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $line->account->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Audit Information --}}
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-info-circle ml-2"></i>
                معلومات التدقيق
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="detail-item">
                <p class="text-sm font-medium text-gray-500">تم الإنشاء بواسطة</p>
                <p class="mt-1 text-lg text-gray-900">{{ $cashPayment->user->name ?? 'N/A' }}</p>
            </div>
            <div class="detail-item">
                <p class="text-sm font-medium text-gray-500">تاريخ الإنشاء</p>
                <p class="mt-1 text-lg text-gray-900">{{ $cashPayment->created_at }}</p>
            </div>
        </div>
    </div>

</div>
@endsection

<style>
    /* Custom style for better detail presentation */
    .detail-item {
        padding: 0.5rem 0;
    }
</style>
