@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 md:p-8" dir="rtl">
    <!-- العنوان والعودة -->
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-3xl font-extrabold text-gray-900">
            <i class="fas fa-university ml-2 text-[#0052CC]"></i>
            إضافة حساب بنكي جديد
        </h1>
        <a href="{{ route('bank_accounts.index') }}" class="text-lg font-medium text-[#0052CC] hover:text-blue-700 transition duration-150 ease-in-out flex items-center">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة إلى قائمة البنوك
        </a>
    </div>

    <!-- بطاقة النموذج الرئيسية -->
    <div class="bg-white shadow-2xl rounded-xl overflow-hidden">
        <div class="p-6 md:p-10">
            <form action="{{ route('bank_accounts.store') }}" method="POST">
                @csrf

                <!-- قسم المعلومات الأساسية -->
                <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-2">
                    <i class="fas fa-info-circle ml-2 text-gray-500"></i>
                    المعلومات الأساسية للبنك
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- اسم البنك -->
                    <div>
                        <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">اسم البنك <span class="text-red-500">*</span></label>
                        <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}" required
                               class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#0052CC] focus:border-[#0052CC] transition duration-150 ease-in-out @error('bank_name') border-red-500 @enderror"
                               placeholder="مثال: البنك الأهلي التجاري">
                        @error('bank_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- رقم الحساب -->
                    <div>
                        <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1">رقم الحساب <span class="text-red-500">*</span></label>
                        <input type="text" name="account_number" id="account_number" value="{{ old('account_number') }}" required
                               class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#0052CC] focus:border-[#0052CC] transition duration-150 ease-in-out @error('account_number') border-red-500 @enderror"
                               placeholder="مثال: 1234567890">
                        @error('account_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- IBAN -->
                    <div>
                        <label for="iban" class="block text-sm font-medium text-gray-700 mb-1">IBAN</label>
                        <input type="text" name="iban" id="iban" value="{{ old('iban') }}"
                               class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#0052CC] focus:border-[#0052CC] transition duration-150 ease-in-out @error('iban') border-red-500 @enderror"
                               placeholder="مثال: SA0380000000608010167519">
                        @error('iban')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- نوع الحساب -->
                    <div>
                        <label for="account_type" class="block text-sm font-medium text-gray-700 mb-1">نوع الحساب <span class="text-red-500">*</span></label>
                        <select name="account_type" id="account_type" required
                                class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#0052CC] focus:border-[#0052CC] transition duration-150 ease-in-out @error('account_type') border-red-500 @enderror">
                            <option value="">اختر نوع الحساب</option>
                            <option value="current" {{ old('account_type') == 'current' ? 'selected' : '' }}>جاري</option>
                            <option value="savings" {{ old('account_type') == 'savings' ? 'selected' : '' }}>توفير</option>
                            <option value="investment" {{ old('account_type') == 'investment' ? 'selected' : '' }}>استثماري</option>
                        </select>
                        @error('account_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- قسم المعلومات المالية -->
                <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-2">
                    <i class="fas fa-money-bill-wave ml-2 text-gray-500"></i>
                    المعلومات المالية والمحاسبية
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- العملة -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">العملة <span class="text-red-500">*</span></label>
                        <select name="currency" id="currency" required
                                class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#0052CC] focus:border-[#0052CC] transition duration-150 ease-in-out @error('currency') border-red-500 @enderror">
                            <option value="">اختر العملة</option>
                            <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                            <!-- يمكن إضافة المزيد من العملات هنا -->
                        </select>
                        @error('currency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- الرصيد الافتتاحي -->
                    <div>
                        <label for="opening_balance" class="block text-sm font-medium text-gray-700 mb-1">الرصيد الافتتاحي <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="opening_balance" id="opening_balance" value="{{ old('opening_balance', 0.00) }}" required
                               class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#0052CC] focus:border-[#0052CC] transition duration-150 ease-in-out @error('opening_balance') border-red-500 @enderror"
                               placeholder="0.00">
                        @error('opening_balance')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- الحساب المحاسبي المرتبط -->
                    <div>
                        <label for="accounting_account_id" class="block text-sm font-medium text-gray-700 mb-1">الحساب المحاسبي المرتبط</label>
                        <select name="accounting_account_id" id="accounting_account_id"
                                class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#0052CC] focus:border-[#0052CC] transition duration-150 ease-in-out @error('accounting_account_id') border-red-500 @enderror">
                            <option value="">اختر حساباً محاسبياً (اختياري)</option>
                            <!-- مثال: يجب ملء هذه الخيارات من قاعدة البيانات -->
                            <option value="1" {{ old('accounting_account_id') == '1' ? 'selected' : '' }}>1010 - الصندوق الرئيسي</option>
                            <option value="2" {{ old('accounting_account_id') == '2' ? 'selected' : '' }}>1020 - بنك الرياض</option>
                        </select>
                        @error('accounting_account_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- قسم الحالة والملاحظات -->
                <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-2">
                    <i class="fas fa-cogs ml-2 text-gray-500"></i>
                    الإعدادات الإضافية
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- الحالة -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                                class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#0052CC] focus:border-[#0052CC] transition duration-150 ease-in-out @error('status') border-red-500 @enderror">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- الملاحظات -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">الملاحظات</label>
                        <textarea name="notes" id="notes" rows="4"
                                  class="w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#0052CC] focus:border-[#0052CC] transition duration-150 ease-in-out @error('notes') border-red-500 @enderror"
                                  placeholder="أضف أي ملاحظات مهمة حول هذا الحساب البنكي">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="flex justify-start space-x-4 space-x-reverse pt-6 border-t">
                    <button type="submit"
                            class="px-6 py-3 bg-[#0052CC] text-white font-bold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-[#0052CC]/50 transition duration-150 ease-in-out flex items-center">
                        <i class="fas fa-save ml-2"></i>
                        حفظ البنك الجديد
                    </button>
                    <a href="{{ route('bank_accounts.index') }}"
                       class="px-6 py-3 bg-gray-200 text-gray-800 font-bold rounded-lg shadow-md hover:bg-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-400/50 transition duration-150 ease-in-out flex items-center">
                        <i class="fas fa-times-circle ml-2"></i>
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<!-- تضمين مكتبة Font Awesome (افتراضياً يتم تضمينها في layout.app) -->
<!-- لتضمين Tailwind CSS، يجب أن يكون ملف app.css أو ما شابه مدمجاً في layout.app -->
