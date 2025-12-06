@extends('layouts.app')

@section('title', 'سند قبض جديد')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-plus-circle text-white text-xl"></i>
                        </div>
                        إنشاء سند قبض جديد
                    </h1>
                    <p class="text-green-100 mt-2">قم بإنشاء سند قبض نقدي أو بنكي</p>
                </div>
                <a href="{{ route('cash-receipts.index') }}" 
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

        <form method="POST" action="{{ route('cash-receipts.store') }}" id="receiptForm">
            @csrf
            
            <!-- Receipt Info -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    معلومات السند
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            رقم السند
                            <span class="text-gray-400 text-xs">(تلقائي)</span>
                        </label>
                        <input type="text" 
                               value="{{ $receiptNumber }}" 
                               disabled
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-500 font-bold">
                    </div>
                    
                    <div>
                        <label for="receipt_date" class="block text-sm font-semibold text-gray-700 mb-2">
                            تاريخ السند *
                        </label>
                        <input type="date" 
                               id="receipt_date" 
                               name="receipt_date" 
                               value="{{ date('Y-m-d') }}"
                               required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-300">
                    </div>
                    
                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">
                            التصنيف *
                        </label>
                        <select id="category" 
                                name="category" 
                                required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-300">
                            <option value="">اختر التصنيف</option>
                            <option value="sales">مبيعات</option>
                            <option value="services">خدمات</option>
                            <option value="loan">قرض</option>
                            <option value="investment">استثمار</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Account Selection -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-wallet text-purple-500"></i>
                    الحساب المستلم
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
                            <option value="cash_box">صندوق نقدي</option>
                            <option value="bank_account">حساب بنكي</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="account_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            الحساب *
                        </label>
                        <select id="account_id" 
                                name="account_id" 
                                required
                                disabled
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-300">
                            <option value="">اختر الحساب أولاً</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Amount & Payment Method -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-money-bill-wave text-green-500"></i>
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
                               required
                               placeholder="0.00"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-300 text-lg font-bold">
                    </div>
                    
                    <div>
                        <label for="currency" class="block text-sm font-semibold text-gray-700 mb-2">
                            العملة *
                        </label>
                        <select id="currency" 
                                name="currency" 
                                required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-300">
                            <option value="SAR">ريال سعودي (SAR)</option>
                            <option value="USD">دولار أمريكي (USD)</option>
                            <option value="EUR">يورو (EUR)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-2">
                            طريقة الدفع *
                        </label>
                        <select id="payment_method" 
                                name="payment_method" 
                                required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-300">
                            <option value="cash">نقدي</option>
                            <option value="check">شيك</option>
                            <option value="transfer">تحويل بنكي</option>
                            <option value="card">بطاقة</option>
                        </select>
                    </div>
                </div>

                <!-- Check Details (Hidden by default) -->
                <div id="checkDetails" class="hidden mt-6 p-4 bg-blue-50 rounded-xl border-2 border-blue-200">
                    <h3 class="font-bold text-blue-800 mb-4">تفاصيل الشيك</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="check_number" class="block text-sm font-semibold text-gray-700 mb-2">رقم الشيك</label>
                            <input type="text" 
                                   id="check_number" 
                                   name="check_number" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition-all">
                        </div>
                        <div>
                            <label for="check_date" class="block text-sm font-semibold text-gray-700 mb-2">تاريخ الشيك</label>
                            <input type="date" 
                                   id="check_date" 
                                   name="check_date" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition-all">
                        </div>
                        <div>
                            <label for="check_bank" class="block text-sm font-semibold text-gray-700 mb-2">البنك</label>
                            <input type="text" 
                                   id="check_bank" 
                                   name="check_bank" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Transfer Details (Hidden by default) -->
                <div id="transferDetails" class="hidden mt-6 p-4 bg-purple-50 rounded-xl border-2 border-purple-200">
                    <h3 class="font-bold text-purple-800 mb-4">تفاصيل التحويل</h3>
                    <div>
                        <label for="transfer_reference" class="block text-sm font-semibold text-gray-700 mb-2">رقم المرجع</label>
                        <input type="text" 
                               id="transfer_reference" 
                               name="transfer_reference" 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 transition-all">
                    </div>
                </div>

                <!-- Card Details (Hidden by default) -->
                <div id="cardDetails" class="hidden mt-6 p-4 bg-indigo-50 rounded-xl border-2 border-indigo-200">
                    <h3 class="font-bold text-indigo-800 mb-4">تفاصيل البطاقة</h3>
                    <div>
                        <label for="card_reference" class="block text-sm font-semibold text-gray-700 mb-2">رقم المرجع</label>
                        <input type="text" 
                               id="card_reference" 
                               name="card_reference" 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Received From -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-orange-500"></i>
                    المستلم منه
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="received_from" class="block text-sm font-semibold text-gray-700 mb-2">
                            الاسم *
                        </label>
                        <input type="text" 
                               id="received_from" 
                               name="received_from" 
                               required
                               placeholder="اسم العميل أو الجهة..."
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all duration-300">
                    </div>
                    
                    <div>
                        <label for="reference_number" class="block text-sm font-semibold text-gray-700 mb-2">
                            رقم المرجع
                        </label>
                        <input type="text" 
                               id="reference_number" 
                               name="reference_number" 
                               placeholder="رقم الفاتورة أو المرجع..."
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all duration-300">
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
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-300"></textarea>
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                            ملاحظات إضافية
                        </label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="2" 
                                  placeholder="ملاحظات اختيارية..."
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-300"></textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between gap-4">
                    <a href="{{ route('cash-receipts.index') }}" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-8 py-3 rounded-xl font-semibold transition-all duration-300">
                        إلغاء
                    </a>
                    
                    <button type="submit" 
                            class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-semibold shadow-lg transition-all duration-300">
                        <i class="fas fa-save ml-2"></i>
                        حفظ السند
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

    const cashBoxes = @json($cashBoxes);
    const bankAccounts = @json($bankAccounts);

    // Handle account type change
    accountType.addEventListener('change', function() {
        accountId.innerHTML = '<option value="">اختر الحساب</option>';
        accountId.disabled = false;

        const accounts = this.value === 'cash_box' ? cashBoxes : bankAccounts;
        
        accounts.forEach(account => {
            const option = document.createElement('option');
            option.value = account.id;
            option.textContent = account.name + (account.code ? ' (' + account.code + ')' : '');
            accountId.appendChild(option);
        });
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
