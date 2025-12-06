@extends('layouts.app')

@section('title', 'تعديل سند الصرف')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-500 to-rose-600 rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-edit text-white text-xl"></i>
                        </div>
                        تعديل سند الصرف
                    </h1>
                    <p class="text-red-100 mt-2">تعديل بيانات سند الصرف رقم: {{ $payment->payment_number }}</p>
                </div>
                <a href="{{ route('cash-payments.index') }}" 
                   class="bg-white/20 hover:bg-white/30 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-arrow-right"></i>
                    <span>رجوع</span>
                </a>
            </div>
        </div>

        @if(session('error'))
        <div class="bg-red-50 border-r-4 border-red-500 p-4 mb-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 text-2xl ml-3"></i>
                <p class="text-red-700 font-semibold">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('cash-payments.update', $payment->id) }}" id="paymentForm">
            @csrf
            @method('PUT')
            
            <!-- Payment Info -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    معلومات السند
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            رقم السند
                        </label>
                        <input type="text" 
                               value="{{ $payment->payment_number }}" 
                               disabled
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-500 font-bold">
                    </div>
                    
                    <div>
                        <label for="payment_date" class="block text-sm font-semibold text-gray-700 mb-2">
                            تاريخ السند *
                        </label>
                        <input type="date" 
                               id="payment_date" 
                               name="payment_date" 
                               value="{{ old('payment_date', $payment->payment_date) }}"
                               required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-300">
                        @error('payment_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">
                            التصنيف *
                        </label>
                        <select id="category" 
                                name="category" 
                                required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-300">
                            <option value="">اختر التصنيف</option>
                            <option value="purchases" {{ old('category', $payment->category) == 'purchases' ? 'selected' : '' }}>مشتريات</option>
                            <option value="expenses" {{ old('category', $payment->category) == 'expenses' ? 'selected' : '' }}>مصروفات</option>
                            <option value="loan_repayment" {{ old('category', $payment->category) == 'loan_repayment' ? 'selected' : '' }}>سداد قرض</option>
                            <option value="salary" {{ old('category', $payment->category) == 'salary' ? 'selected' : '' }}>رواتب</option>
                            <option value="other" {{ old('category', $payment->category) == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                        @error('category')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Account Selection (Paid From) -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-wallet text-purple-500"></i>
                    الحساب الصارف
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="account_type" class="block text-sm font-semibold text-gray-700 mb-2">
                            نوع الحساب *
                        </label>
                        <select id="account_type" 
                                name="account_type" 
                                required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-300">
                            <option value="">اختر نوع الحساب</option>
                            <option value="cash_box" {{ old('account_type', $payment->account_type) == 'cash_box' ? 'selected' : '' }}>صندوق نقدي</option>
                            <option value="bank_account" {{ old('account_type', $payment->account_type) == 'bank_account' ? 'selected' : '' }}>حساب بنكي</option>
                        </select>
                        @error('account_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="account_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            الحساب *
                        </label>
                        <select id="account_id" 
                                name="account_id" 
                                required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-300">
                            <option value="{{ old('account_id', $payment->account_id) }}" selected>
                                {{ $payment->account_type == 'cash_box' ? $payment->cashBox->name : $payment->bankAccount->name }}
                            </option>
                        </select>
                        @error('account_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Amount & Payment Method -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-money-bill-wave text-red-500"></i>
                    المبلغ وطريقة الدفع
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">
                            المبلغ *
                        </label>
                        <input type="number" 
                               id="amount" 
                               name="amount" 
                               step="0.01"
                               min="0.01"
                               value="{{ old('amount', $payment->amount) }}"
                               required
                               placeholder="0.00"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-300 text-lg font-bold">
                        @error('amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="currency" class="block text-sm font-semibold text-gray-700 mb-2">
                            العملة *
                        </label>
                        <select id="currency" 
                                name="currency" 
                                required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-300">
                            <option value="SAR" {{ old('currency', $payment->currency) == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                            <option value="USD" {{ old('currency', $payment->currency) == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                            <option value="EUR" {{ old('currency', $payment->currency) == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                        </select>
                        @error('currency')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-2">
                            طريقة الدفع *
                        </label>
                        <select id="payment_method" 
                                name="payment_method" 
                                required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-300">
                            <option value="cash" {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}>نقدي</option>
                            <option value="check" {{ old('payment_method', $payment->payment_method) == 'check' ? 'selected' : '' }}>شيك</option>
                            <option value="transfer" {{ old('payment_method', $payment->payment_method) == 'transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                            <option value="card" {{ old('payment_method', $payment->payment_method) == 'card' ? 'selected' : '' }}>بطاقة</option>
                        </select>
                        @error('payment_method')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Check Details (Conditional) -->
                <div id="checkDetails" class="mt-6 p-4 bg-blue-50 rounded-xl border-2 border-blue-200 {{ $payment->payment_method == 'check' ? '' : 'hidden' }}">
                    <h3 class="font-bold text-blue-800 mb-4">تفاصيل الشيك</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="check_number" class="block text-sm font-semibold text-gray-700 mb-2">رقم الشيك</label>
                            <input type="text" 
                                   id="check_number" 
                                   name="check_number" 
                                   value="{{ old('check_number', $payment->check_number) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition-all">
                        </div>
                        <div>
                            <label for="check_date" class="block text-sm font-semibold text-gray-700 mb-2">تاريخ الشيك</label>
                            <input type="date" 
                                   id="check_date" 
                                   name="check_date" 
                                   value="{{ old('check_date', $payment->check_date) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition-all">
                        </div>
                        <div>
                            <label for="check_bank" class="block text-sm font-semibold text-gray-700 mb-2">البنك</label>
                            <input type="text" 
                                   id="check_bank" 
                                   name="check_bank" 
                                   value="{{ old('check_bank', $payment->check_bank) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Transfer Details (Conditional) -->
                <div id="transferDetails" class="mt-6 p-4 bg-purple-50 rounded-xl border-2 border-purple-200 {{ $payment->payment_method == 'transfer' ? '' : 'hidden' }}">
                    <h3 class="font-bold text-purple-800 mb-4">تفاصيل التحويل</h3>
                    <div>
                        <label for="transfer_reference" class="block text-sm font-semibold text-gray-700 mb-2">رقم المرجع</label>
                        <input type="text" 
                               id="transfer_reference" 
                               name="transfer_reference" 
                               value="{{ old('transfer_reference', $payment->transfer_reference) }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 transition-all">
                    </div>
                </div>

                <!-- Card Details (Conditional) -->
                <div id="cardDetails" class="mt-6 p-4 bg-indigo-50 rounded-xl border-2 border-indigo-200 {{ $payment->payment_method == 'card' ? '' : 'hidden' }}">
                    <h3 class="font-bold text-indigo-800 mb-4">تفاصيل البطاقة</h3>
                    <div>
                        <label for="card_reference" class="block text-sm font-semibold text-gray-700 mb-2">رقم المرجع</label>
                        <input type="text" 
                               id="card_reference" 
                               name="card_reference" 
                               value="{{ old('card_reference', $payment->card_reference) }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Paid To -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-orange-500"></i>
                    المصروف له
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="paid_to" class="block text-sm font-semibold text-gray-700 mb-2">
                            الاسم *
                        </label>
                        <input type="text" 
                               id="paid_to" 
                               name="paid_to" 
                               value="{{ old('paid_to', $payment->paid_to) }}"
                               required
                               placeholder="اسم المورد أو الجهة..."
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all duration-300">
                        @error('paid_to')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="reference_number" class="block text-sm font-semibold text-gray-700 mb-2">
                            رقم المرجع
                        </label>
                        <input type="text" 
                               id="reference_number" 
                               name="reference_number" 
                               value="{{ old('reference_number', $payment->reference_number) }}"
                               placeholder="رقم الفاتورة أو المرجع..."
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all duration-300">
                        @error('reference_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-file-alt text-indigo-500"></i>
                    الوصف والملاحظات
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            الوصف *
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3" 
                                  required
                                  placeholder="وصف مختصر للسند..."
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-300">{{ old('description', $payment->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                            ملاحظات إضافية
                        </label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="2" 
                                  placeholder="ملاحظات اختيارية..."
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-300">{{ old('notes', $payment->notes) }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between gap-4">
                    <a href="{{ route('cash-payments.index') }}" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-8 py-3 rounded-xl font-semibold transition-all duration-300">
                        إلغاء
                    </a>
                    
                    <button type="submit" 
                            class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white px-8 py-3 rounded-xl font-semibold shadow-lg transition-all duration-300">
                        <i class="fas fa-save ml-2"></i>
                        حفظ التعديلات
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const accountType = document.getElementById('account_type');
    const accountId = document.getElementById('account_id');
    const paymentMethod = document.getElementById('payment_method');
    const checkDetails = document.getElementById('checkDetails');
    const transferDetails = document.getElementById('transferDetails');
    const cardDetails = document.getElementById('cardDetails');

    // Assuming $cashBoxes and $bankAccounts are passed from the controller
    const cashBoxes = @json($cashBoxes ?? []);
    const bankAccounts = @json($bankAccounts ?? []);
    const currentAccountId = '{{ old('account_id', $payment->account_id) }}';

    // Function to update the account dropdown
    function updateAccountDropdown(selectedType, selectedId = null) {
        accountId.innerHTML = '<option value="">اختر الحساب</option>';
        accountId.disabled = false;

        const accounts = selectedType === 'cash_box' ? cashBoxes : bankAccounts;
        
        if (accounts.length === 0) {
            accountId.disabled = true;
            accountId.innerHTML = '<option value="">لا توجد حسابات متاحة</option>';
            return;
        }

        accounts.forEach(account => {
            const option = document.createElement('option');
            option.value = account.id;
            option.textContent = account.name + (account.code ? ' (' + account.code + ')' : '');
            if (account.id == selectedId) {
                option.selected = true;
            }
            accountId.appendChild(option);
        });
    }

    // Initial load: Populate the account dropdown based on the current account type
    const initialAccountType = accountType.value;
    if (initialAccountType) {
        // We pass the currentAccountId to ensure the correct account is selected
        updateAccountDropdown(initialAccountType, currentAccountId);
    } else {
        accountId.disabled = true;
    }

    // Handle account type change
    accountType.addEventListener('change', function() {
        updateAccountDropdown(this.value);
    });

    // Handle payment method change
    paymentMethod.addEventListener('change', function() {
        checkDetails.classList.add('hidden');
        transferDetails.classList.add('hidden');
        cardDetails.classList.add('hidden');

        if (this.value === 'check') {
            checkDetails.classList.remove('hidden');
        } else if (this.value === 'transfer') {
            transferDetails.classList.remove('hidden');
        } else if (this.value === 'card') {
            cardDetails.classList.remove('hidden');
        }
    });
});
</script>
@endpush
@endsection
