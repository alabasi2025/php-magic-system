@extends('layouts.app')

@section('title', 'إضافة سند صرف جديد')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white shadow-xl rounded-lg mt-10">
    <div class="flex items-center justify-between border-b pb-4 mb-6 border-red-200">
        <h1 class="text-3xl font-bold text-red-700 flex items-center">
            <i class="fas fa-file-invoice-dollar mr-3 text-red-500"></i>
            إضافة سند صرف جديد
        </h1>
        <a href="{{ route('cash-payments.index') }}" class="text-red-600 hover:text-red-800 transition duration-150 ease-in-out flex items-center">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة إلى سندات الصرف
        </a>
    </div>

    <form action="{{ route('cash-payments.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- حقل رقم السند (للعرض فقط أو ترقيم تلقائي) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="voucher_number" class="block text-sm font-medium text-gray-700">رقم السند</label>
                <input type="text" id="voucher_number" name="voucher_number" value="{{ $nextVoucherNumber ?? 'تلقائي' }}" disabled class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm bg-gray-100 cursor-not-allowed">
            </div>

            {{-- حقل التاريخ --}}
            <div>
                <label for="voucher_date" class="block text-sm font-medium text-gray-700">تاريخ السند <span class="text-red-500">*</span></label>
                <input type="date" id="voucher_date" name="voucher_date" value="{{ old('voucher_date', date('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm @error('voucher_date') border-red-500 @enderror">
                @error('voucher_date')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- حقول الحسابات والمبلغ --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- الحساب المدفوع منه (الصندوق/البنك) --}}
            <div>
                <label for="paid_from_account_id" class="block text-sm font-medium text-gray-700">الحساب المدفوع منه <span class="text-red-500">*</span></label>
                <select id="paid_from_account_id" name="paid_from_account_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm @error('paid_from_account_id') border-red-500 @enderror">
                    <option value="">-- اختر حساب الصرف --</option>
                    {{-- @foreach ($cashBankAccounts as $account)
                        <option value="{{ $account->id }}" {{ old('paid_from_account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                    @endforeach --}}
                </select>
                @error('paid_from_account_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- الحساب المدفوع له (المستفيد) --}}
            <div>
                <label for="paid_to_account_id" class="block text-sm font-medium text-gray-700">الحساب المدفوع له <span class="text-red-500">*</span></label>
                <select id="paid_to_account_id" name="paid_to_account_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm @error('paid_to_account_id') border-red-500 @enderror">
                    <option value="">-- اختر حساب المستفيد --</option>
                    {{-- @foreach ($beneficiaryAccounts as $account)
                        <option value="{{ $account->id }}" {{ old('paid_to_account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                    @endforeach --}}
                </select>
                @error('paid_to_account_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- حقل المبلغ --}}
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700">المبلغ <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0.01" id="amount" name="amount" value="{{ old('amount') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm @error('amount') border-red-500 @enderror">
                @error('amount')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- حقل اسم المستفيد والعملة --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- حقل اسم المستفيد --}}
            <div>
                <label for="beneficiary_name" class="block text-sm font-medium text-gray-700">اسم المستفيد <span class="text-red-500">*</span></label>
                <input type="text" id="beneficiary_name" name="beneficiary_name" value="{{ old('beneficiary_name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm @error('beneficiary_name') border-red-500 @enderror">
                @error('beneficiary_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- حقل العملة --}}
            <div>
                <label for="currency_id" class="block text-sm font-medium text-gray-700">العملة <span class="text-red-500">*</span></label>
                <select id="currency_id" name="currency_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm @error('currency_id') border-red-500 @enderror">
                    <option value="">-- اختر العملة --</option>
                    {{-- @foreach ($currencies as $currency)
                        <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>{{ $currency->name }}</option>
                    @endforeach --}}
                </select>
                @error('currency_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- حقل الوصف/البيان --}}
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">البيان/الوصف <span class="text-red-500">*</span></label>
            <textarea id="description" name="description" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- أزرار الإجراءات --}}
        <div class="flex justify-end space-x-4 space-x-reverse pt-4 border-t border-gray-200">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                <i class="fas fa-save ml-2"></i>
                حفظ سند الصرف
            </button>
            <a href="{{ route('cash-payments.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
