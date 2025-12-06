@extends('layouts.app')

@section('title', 'إضافة صندوق نقدي جديد')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">إضافة صندوق نقدي جديد</h1>
                <p class="text-gray-600">قم بإدخال بيانات الصندوق النقدي الجديد</p>
            </div>
            <a href="{{ route('cash-boxes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 space-x-reverse">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('cash-boxes.store') }}" method="POST" id="cashBoxForm">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- القسم الأيمن: البيانات الأساسية -->
            <div class="lg:col-span-2 space-y-6">
                <!-- بطاقة البيانات الأساسية -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center space-x-3 space-x-reverse mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-cash-register text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">البيانات الأساسية</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- اسم الصندوق -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                اسم الصندوق <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   required
                                   value="{{ old('name') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                   placeholder="مثال: صندوق المبيعات الرئيسي">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- رمز الصندوق -->
                        <div>
                            <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                                رمز الصندوق <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="code" 
                                   name="code" 
                                   required
                                   value="{{ old('code') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                   placeholder="مثال: CB-001">
                            @error('code')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- موقع الصندوق -->
                        <div>
                            <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">
                                موقع الصندوق
                            </label>
                            <input type="text" 
                                   id="location" 
                                   name="location"
                                   value="{{ old('location') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                   placeholder="مثال: الطابق الأول - قسم المبيعات">
                            @error('location')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- الوصف -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                الوصف
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                      placeholder="وصف تفصيلي للصندوق...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- بطاقة الأرصدة والحدود -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center space-x-3 space-x-reverse mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-coins text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">الأرصدة والحدود</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- الرصيد الافتتاحي -->
                        <div>
                            <label for="balance" class="block text-sm font-semibold text-gray-700 mb-2">
                                الرصيد الافتتاحي
                            </label>
                            <input type="number" 
                                   id="balance" 
                                   name="balance" 
                                   step="0.01"
                                   value="{{ old('balance', 0) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                   placeholder="0.00">
                            @error('balance')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- الحد الأدنى -->
                        <div>
                            <label for="min_balance" class="block text-sm font-semibold text-gray-700 mb-2">
                                الحد الأدنى للرصيد
                            </label>
                            <input type="number" 
                                   id="min_balance" 
                                   name="min_balance" 
                                   step="0.01"
                                   value="{{ old('min_balance') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200"
                                   placeholder="0.00">
                            @error('min_balance')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- الحد الأقصى -->
                        <div>
                            <label for="max_balance" class="block text-sm font-semibold text-gray-700 mb-2">
                                الحد الأقصى للرصيد
                            </label>
                            <input type="number" 
                                   id="max_balance" 
                                   name="max_balance" 
                                   step="0.01"
                                   value="{{ old('max_balance') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                                   placeholder="0.00">
                            @error('max_balance')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- بطاقة العملات -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center space-x-3 space-x-reverse mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">العملات المستخدمة</h2>
                    </div>

                    <div id="currenciesContainer">
                        <div class="currency-item mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">العملة</label>
                                    <select name="currencies[0][code]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="SAR">ريال سعودي (SAR)</option>
                                        <option value="USD">دولار أمريكي (USD)</option>
                                        <option value="EUR">يورو (EUR)</option>
                                        <option value="GBP">جنيه إسترليني (GBP)</option>
                                        <option value="AED">درهم إماراتي (AED)</option>
                                        <option value="KWD">دينار كويتي (KWD)</option>
                                        <option value="BHD">دينار بحريني (BHD)</option>
                                        <option value="OMR">ريال عماني (OMR)</option>
                                        <option value="QAR">ريال قطري (QAR)</option>
                                        <option value="EGP">جنيه مصري (EGP)</option>
                                        <option value="JOD">دينار أردني (JOD)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">سعر الصرف</label>
                                    <input type="number" name="currencies[0][exchange_rate]" step="0.0001" value="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="1.0000">
                                </div>
                                <div class="flex items-end">
                                    <label class="flex items-center space-x-2 space-x-reverse">
                                        <input type="checkbox" name="currencies[0][is_default]" value="1" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-sm font-semibold text-gray-700">العملة الأساسية</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="addCurrency" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 space-x-reverse">
                        <i class="fas fa-plus"></i>
                        <span>إضافة عملة</span>
                    </button>
                </div>
            </div>

            <!-- القسم الأيسر: الربط والإعدادات -->
            <div class="space-y-6">
                <!-- بطاقة الربط المحاسبي -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center space-x-3 space-x-reverse mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-link text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">الربط المحاسبي</h2>
                    </div>

                    <div class="space-y-4">
                        <!-- الوحدة التنظيمية -->
                        <div>
                            <label for="unit_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                الوحدة التنظيمية
                            </label>
                            <select id="unit_id" 
                                    name="unit_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                                <option value="">-- اختر الوحدة --</option>
                                @foreach(\App\Models\Unit::all() as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- الحساب في الدليل المحاسبي -->
                        <div>
                            <label for="chart_account_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                الحساب في الدليل المحاسبي <span class="text-red-500">*</span>
                            </label>
                            <select id="chart_account_id" 
                                    name="chart_account_id"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                                <option value="">-- اختر الحساب --</option>
                                @foreach(\App\Models\ChartAccount::all() as $account)
                                    <option value="{{ $account->id }}" {{ old('chart_account_id') == $account->id ? 'selected' : '' }}>{{ $account->account_code }} - {{ $account->account_name }}</option>
                                @endforeach
                            </select>
                            @error('chart_account_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- الحساب الوسيط -->
                        <div>
                            <label for="intermediate_account_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                الحساب الوسيط
                            </label>
                            <select id="intermediate_account_id" 
                                    name="intermediate_account_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                                <option value="">-- اختر الحساب الوسيط --</option>
                                @foreach(\App\Models\ChartAccount::all() as $account)
                                    <option value="{{ $account->id }}" {{ old('intermediate_account_id') == $account->id ? 'selected' : '' }}>{{ $account->account_code }} - {{ $account->account_name }}</option>
                                @endforeach
                            </select>
                            @error('intermediate_account_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- بطاقة المسؤولية -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center space-x-3 space-x-reverse mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-shield text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">المسؤولية</h2>
                    </div>

                    <div>
                        <label for="responsible_user_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            مسؤول الصندوق <span class="text-red-500">*</span>
                        </label>
                        <select id="responsible_user_id" 
                                name="responsible_user_id"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200">
                            <option value="">-- اختر المسؤول --</option>
                            @foreach(\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}" {{ old('responsible_user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('responsible_user_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- بطاقة الحالة -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center space-x-3 space-x-reverse mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-teal-500 to-teal-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-toggle-on text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">الحالة</h2>
                    </div>

                    <div>
                        <label class="flex items-center space-x-3 space-x-reverse cursor-pointer">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                            <span class="text-sm font-semibold text-gray-700">الصندوق نشط</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-2">عند تفعيل هذا الخيار، سيكون الصندوق متاحاً للاستخدام في العمليات المالية</p>
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="space-y-3">
                        <button type="submit" class="w-full bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-6 py-3 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2 space-x-reverse font-semibold">
                            <i class="fas fa-save"></i>
                            <span>حفظ الصندوق</span>
                        </button>
                        <a href="{{ route('cash-boxes.index') }}" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 space-x-reverse font-semibold">
                            <i class="fas fa-times"></i>
                            <span>إلغاء</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currencyIndex = 1;
    
    // إضافة عملة جديدة
    document.getElementById('addCurrency').addEventListener('click', function() {
        const container = document.getElementById('currenciesContainer');
        const newCurrency = `
            <div class="currency-item mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">العملة</label>
                        <select name="currencies[${currencyIndex}][code]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="SAR">ريال سعودي (SAR)</option>
                            <option value="USD">دولار أمريكي (USD)</option>
                            <option value="EUR">يورو (EUR)</option>
                            <option value="GBP">جنيه إسترليني (GBP)</option>
                            <option value="AED">درهم إماراتي (AED)</option>
                            <option value="KWD">دينار كويتي (KWD)</option>
                            <option value="BHD">دينار بحريني (BHD)</option>
                            <option value="OMR">ريال عماني (OMR)</option>
                            <option value="QAR">ريال قطري (QAR)</option>
                            <option value="EGP">جنيه مصري (EGP)</option>
                            <option value="JOD">دينار أردني (JOD)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">سعر الصرف</label>
                        <input type="number" name="currencies[${currencyIndex}][exchange_rate]" step="0.0001" value="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="1.0000">
                    </div>
                    <div class="flex items-end justify-between">
                        <label class="flex items-center space-x-2 space-x-reverse">
                            <input type="checkbox" name="currencies[${currencyIndex}][is_default]" value="1" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm font-semibold text-gray-700">العملة الأساسية</span>
                        </label>
                        <button type="button" class="remove-currency text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newCurrency);
        currencyIndex++;
    });
    
    // حذف عملة
    document.getElementById('currenciesContainer').addEventListener('click', function(e) {
        if (e.target.closest('.remove-currency')) {
            e.target.closest('.currency-item').remove();
        }
    });
});
</script>
@endsection
