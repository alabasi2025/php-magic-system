@extends('layouts.app')
@section('title', 'إضافة صندوق نقدي جديد')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('cash-boxes.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-right text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">إضافة صندوق نقدي جديد</h1>
        </div>
        <p class="text-gray-600 mr-12">قم بإنشاء صندوق نقدي جديد وربطه بحساب وسيط من دليل الحسابات</p>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
            <p class="text-red-700">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
        <div class="flex items-center mb-2">
            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
            <p class="text-red-700 font-semibold">يوجد أخطاء في النموذج:</p>
        </div>
        <ul class="list-disc list-inside text-red-600 mr-8">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('cash-boxes.store') }}" method="POST" id="cashBoxForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- الوحدة التنظيمية -->
                <div class="col-span-2">
                    <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-2">
                        الوحدة التنظيمية <span class="text-red-500">*</span>
                    </label>
                    <select name="unit_id" 
                            id="unit_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        <option value="">-- اختر الوحدة التنظيمية --</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }} ({{ $unit->code }})
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i> سيتم فلترة الحسابات الوسيطة بناءً على الوحدة المختارة
                    </p>
                </div>

                <!-- الحساب الوسيط -->
                <div class="col-span-2">
                    <label for="intermediate_account_id" class="block text-sm font-medium text-gray-700 mb-2">
                        الحساب الوسيط <span class="text-red-500">*</span>
                    </label>
                    <select name="intermediate_account_id" 
                            id="intermediate_account_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                            disabled>
                        <option value="">-- اختر الوحدة التنظيمية أولاً --</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i> الحسابات الوسيطة المتاحة للصناديق (غير المرتبطة بصناديق أخرى)
                    </p>
                    <div id="accountInfo" class="hidden mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <p class="text-sm text-blue-800">
                            <strong>الدليل:</strong> <span id="chartGroupName"></span><br>
                            <strong>الرمز:</strong> <span id="accountCode"></span>
                        </p>
                    </div>
                </div>

                <!-- اسم الصندوق -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        اسم الصندوق <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="مثال: صندوق الاستقبال الرئيسي"
                           required>
                </div>

                <!-- رمز الصندوق -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        رمز الصندوق <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="code" 
                           id="code" 
                           value="{{ old('code') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="مثال: CASH-001"
                           required>
                </div>

                <!-- الرصيد الافتتاحي -->
                <div>
                    <label for="balance" class="block text-sm font-medium text-gray-700 mb-2">
                        الرصيد الافتتاحي (ريال)
                    </label>
                    <input type="number" 
                           name="balance" 
                           id="balance" 
                           value="{{ old('balance', 0) }}"
                           step="0.01"
                           min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="0.00">
                </div>

                <!-- الحالة -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="is_active" class="mr-3 text-sm font-medium text-gray-700">
                        الصندوق نشط
                    </label>
                </div>

                <!-- الوصف -->
                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        الوصف
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="وصف إضافي عن الصندوق...">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- الأزرار -->
            <div class="flex items-center justify-end space-x-4 space-x-reverse mt-6 pt-6 border-t">
                <a href="{{ route('cash-boxes.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>حفظ الصندوق
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const unitSelect = document.getElementById('unit_id');
    const accountSelect = document.getElementById('intermediate_account_id');
    const accountInfo = document.getElementById('accountInfo');
    
    // Initial data
    const initialAccounts = @json($intermediateAccounts);
    
    // Listen to unit change
    unitSelect.addEventListener('change', function() {
        const unitId = this.value;
        
        if (!unitId) {
            accountSelect.disabled = true;
            accountSelect.innerHTML = '<option value="">-- اختر الوحدة التنظيمية أولاً --</option>';
            accountInfo.classList.add('hidden');
            return;
        }
        
        // Filter accounts by unit
        const filteredAccounts = initialAccounts.filter(account => {
            return account.chart_group && account.chart_group.unit_id == unitId;
        });
        
        // Update account select
        accountSelect.innerHTML = '<option value="">-- اختر الحساب الوسيط --</option>';
        
        if (filteredAccounts.length === 0) {
            accountSelect.innerHTML += '<option value="" disabled>لا توجد حسابات وسيطة متاحة لهذه الوحدة</option>';
            accountSelect.disabled = true;
        } else {
            filteredAccounts.forEach(account => {
                const option = document.createElement('option');
                option.value = account.id;
                option.textContent = `${account.name} (${account.code})`;
                option.dataset.chartGroup = account.chart_group ? account.chart_group.name : '';
                option.dataset.code = account.code;
                accountSelect.appendChild(option);
            });
            accountSelect.disabled = false;
        }
        
        accountInfo.classList.add('hidden');
    });
    
    // Listen to account change
    accountSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value && selectedOption.dataset.chartGroup) {
            document.getElementById('chartGroupName').textContent = selectedOption.dataset.chartGroup;
            document.getElementById('accountCode').textContent = selectedOption.dataset.code;
            accountInfo.classList.remove('hidden');
        } else {
            accountInfo.classList.add('hidden');
        }
    });
    
    // Trigger change if unit is already selected (for old input)
    if (unitSelect.value) {
        unitSelect.dispatchEvent(new Event('change'));
        
        // Restore selected account if exists
        const oldAccountId = '{{ old("intermediate_account_id") }}';
        if (oldAccountId) {
            setTimeout(() => {
                accountSelect.value = oldAccountId;
                accountSelect.dispatchEvent(new Event('change'));
            }, 100);
        }
    }
});
</script>
@endsection
