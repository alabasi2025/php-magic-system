@extends('layouts.app')

@section('title', 'تعديل بيانات البنك')

@section('content')
    <div class="container mx-auto p-4 md:p-8" dir="rtl">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-university text-blue-600 ml-3"></i>
                تعديل بيانات البنك
            </h1>
            <a href="{{ route('bank-accounts.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-arrow-right mr-2"></i>
                العودة إلى قائمة البنوك
            </a>
        </div>

        <!-- Card Container -->
        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
            <div class="p-6 border-b border-gray-200" style="background-color: #0052CC;">
                <h2 class="text-xl font-semibold text-white">نموذج تعديل بيانات البنك: {{ $bankAccount->bank_name ?? 'اسم البنك' }}</h2>
            </div>

            <form action="{{ route('bank-accounts.update', $bankAccount->id ?? 1) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <!-- General Information Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Bank Name -->
                    <div>
                        <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-building text-blue-500 ml-1"></i> اسم البنك
                        </label>
                        <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $bankAccount->bank_name ?? 'البنك الأهلي') }}" required
                               class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                               placeholder="أدخل اسم البنك">
                        @error('bank_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Account Number -->
                    <div>
                        <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-hashtag text-blue-500 ml-1"></i> رقم الحساب
                        </label>
                        <input type="text" name="account_number" id="account_number" value="{{ old('account_number', $bankAccount->account_number ?? '1234567890') }}" required
                               class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                               placeholder="أدخل رقم الحساب">
                        @error('account_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- IBAN -->
                    <div>
                        <label for="iban" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-credit-card text-blue-500 ml-1"></i> IBAN
                        </label>
                        <input type="text" name="iban" id="iban" value="{{ old('iban', $bankAccount->iban ?? 'SA0012345678901234567890') }}"
                               class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                               placeholder="أدخل رقم IBAN">
                        @error('iban')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Account Type -->
                    <div>
                        <label for="account_type" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-wallet text-blue-500 ml-1"></i> نوع الحساب
                        </label>
                        <select name="account_type" id="account_type" required
                                class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                            @php $currentType = old('account_type', $bankAccount->account_type ?? 'جاري'); @endphp
                            <option value="جاري" {{ $currentType == 'جاري' ? 'selected' : '' }}>جاري</option>
                            <option value="توفير" {{ $currentType == 'توفير' ? 'selected' : '' }}>توفير</option>
                            <option value="استثماري" {{ $currentType == 'استثماري' ? 'selected' : '' }}>استثماري</option>
                        </select>
                        @error('account_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-money-bill-wave text-blue-500 ml-1"></i> العملة
                        </label>
                        <select name="currency" id="currency" required
                                class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                            @php $currentCurrency = old('currency', $bankAccount->currency ?? 'SAR'); @endphp
                            <option value="SAR" {{ $currentCurrency == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                            <option value="USD" {{ $currentCurrency == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                            <option value="EUR" {{ $currentCurrency == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                        </select>
                        @error('currency')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Linked Accounting Account -->
                    <div>
                        <label for="accounting_account" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-link text-blue-500 ml-1"></i> الحساب المحاسبي المرتبط
                        </label>
                        <select name="accounting_account" id="accounting_account"
                                class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                            @php $currentAccount = old('accounting_account', $bankAccount->accounting_account ?? '111001'); @endphp
                            <option value="111001" {{ $currentAccount == '111001' ? 'selected' : '' }}>111001 - الصندوق الرئيسي</option>
                            <option value="111002" {{ $currentAccount == '111002' ? 'selected' : '' }}>111002 - بنك الرياض</option>
                            <option value="111003" {{ $currentAccount == '111003' ? 'selected' : '' }}>111003 - بنك الأهلي</option>
                        </select>
                        @error('accounting_account')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-toggle-on text-blue-500 ml-1"></i> الحالة
                        </label>
                        <select name="status" id="status" required
                                class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                            @php $currentStatus = old('status', $bankAccount->status ?? 'نشط'); @endphp
                            <option value="نشط" {{ $currentStatus == 'نشط' ? 'selected' : '' }}>نشط</option>
                            <option value="معطل" {{ $currentStatus == 'معطل' ? 'selected' : '' }}>معطل</option>
                        </select>
                        @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Opening Balance (Read-only for reference) -->
                    <div>
                        <label for="opening_balance" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-balance-scale text-blue-500 ml-1"></i> الرصيد الافتتاحي
                        </label>
                        <input type="text" id="opening_balance" value="{{ number_format($bankAccount->opening_balance ?? 50000, 2) }}" disabled
                               class="w-full border border-gray-300 bg-gray-100 rounded-lg shadow-sm p-3 transition duration-150 cursor-not-allowed"
                               placeholder="الرصيد الافتتاحي">
                        <p class="text-xs text-gray-500 mt-1">لا يمكن تعديل الرصيد الافتتاحي بعد الإنشاء.</p>
                    </div>

                    <!-- Current Balance (Read-only for reference) -->
                    <div>
                        <label for="current_balance" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-chart-line text-blue-500 ml-1"></i> الرصيد الحالي
                        </label>
                        <input type="text" id="current_balance" value="{{ number_format($bankAccount->current_balance ?? 75000, 2) }}" disabled
                               class="w-full border border-gray-300 bg-gray-100 rounded-lg shadow-sm p-3 transition duration-150 cursor-not-allowed"
                               placeholder="الرصيد الحالي">
                        <p class="text-xs text-gray-500 mt-1">يتم احتساب الرصيد الحالي تلقائياً.</p>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="mb-8">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-pencil-alt text-blue-500 ml-1"></i> الملاحظات
                    </label>
                    <textarea name="notes" id="notes" rows="4"
                              class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                              placeholder="أضف أي ملاحظات إضافية حول الحساب">{{ old('notes', $bankAccount->notes ?? 'هذا الحساب مخصص للمعاملات اليومية.') }}</textarea>
                    @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 space-x-reverse">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition duration-300 flex items-center"
                            style="background-color: #0052CC;">
                        <i class="fas fa-save mr-2"></i>
                        حفظ التعديلات
                    </button>
                    <a href="{{ route('bank-accounts.show', $bankAccount->id ?? 1) }}"
                       class="bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg shadow-md transition duration-300 flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- يمكن إضافة أي سكريبتات خاصة بالتحقق من الحقول أو التفاعلات هنا -->
@endpush
