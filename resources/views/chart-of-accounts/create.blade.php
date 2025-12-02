@extends('layouts.app')

@section('title', 'إضافة حساب جديد')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-0">
                <i class="fas fa-plus-circle"></i> إضافة حساب جديد
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('chart-of-accounts.index') }}">دليل الحسابات</a></li>
                    <li class="breadcrumb-item active">إضافة حساب جديد</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('chart-of-accounts.store') }}" method="POST" id="accountForm">
                        @csrf

                        <!-- معلومات أساسية -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-info-circle"></i> المعلومات الأساسية
                                </h5>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">الوحدة</label>
                                <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                                    <option value="">اختر الوحدة</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">رقم الحساب</label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code') }}" required placeholder="مثال: 1010">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">رقم فريد للحساب</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label required">مستوى الحساب</label>
                                <select name="account_level" id="account_level" class="form-select @error('account_level') is-invalid @enderror" required>
                                    <option value="">اختر المستوى</option>
                                    <option value="parent" {{ old('account_level') == 'parent' ? 'selected' : '' }}>حساب رئيسي</option>
                                    <option value="sub" {{ old('account_level') == 'sub' ? 'selected' : '' }}>حساب فرعي</option>
                                </select>
                                @error('account_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mt-3">
                                <label class="form-label required">اسم الحساب (عربي)</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" required placeholder="مثال: الصندوق الرئيسي">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mt-3">
                                <label class="form-label">اسم الحساب (إنجليزي)</label>
                                <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" 
                                       value="{{ old('name_en') }}" placeholder="Example: Main Cash Box">
                                @error('name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-3">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                          rows="3" placeholder="وصف تفصيلي للحساب">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- الحساب الأب (للحسابات الرئيسية) -->
                        <div class="row mb-4" id="parent_account_section" style="display: none;">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-sitemap"></i> الهيكلية
                                </h5>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحساب الأب</label>
                                <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                    <option value="">لا يوجد (حساب جذر)</option>
                                    @foreach($parentAccounts as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->code }} - {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- التفاصيل المحاسبية (للحسابات الفرعية فقط) -->
                        <div class="row mb-4" id="sub_account_section" style="display: none;">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-calculator"></i> التفاصيل المحاسبية
                                </h5>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع الحساب المحاسبي</label>
                                <select name="account_type" class="form-select @error('account_type') is-invalid @enderror">
                                    <option value="">اختر النوع (اختياري)</option>
                                    <option value="asset" {{ old('account_type') == 'asset' ? 'selected' : '' }}>أصول</option>
                                    <option value="liability" {{ old('account_type') == 'liability' ? 'selected' : '' }}>خصوم</option>
                                    <option value="equity" {{ old('account_type') == 'equity' ? 'selected' : '' }}>حقوق ملكية</option>
                                    <option value="revenue" {{ old('account_type') == 'revenue' ? 'selected' : '' }}>إيرادات</option>
                                    <option value="expense" {{ old('account_type') == 'expense' ? 'selected' : '' }}>مصروفات</option>
                                </select>
                                @error('account_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">يمكن تحديده لاحقاً</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع الحساب التحليلي</label>
                                <select name="analytical_type" class="form-select @error('analytical_type') is-invalid @enderror">
                                    <option value="">اختر النوع (اختياري)</option>
                                    <option value="cash_box" {{ old('analytical_type') == 'cash_box' ? 'selected' : '' }}>صندوق</option>
                                    <option value="bank" {{ old('analytical_type') == 'bank' ? 'selected' : '' }}>بنك</option>
                                    <option value="cashier" {{ old('analytical_type') == 'cashier' ? 'selected' : '' }}>صراف</option>
                                    <option value="wallet" {{ old('analytical_type') == 'wallet' ? 'selected' : '' }}>محفظة</option>
                                    <option value="customer" {{ old('analytical_type') == 'customer' ? 'selected' : '' }}>عميل</option>
                                    <option value="supplier" {{ old('analytical_type') == 'supplier' ? 'selected' : '' }}>مورد</option>
                                    <option value="warehouse" {{ old('analytical_type') == 'warehouse' ? 'selected' : '' }}>مخزن</option>
                                    <option value="employee" {{ old('analytical_type') == 'employee' ? 'selected' : '' }}>موظف</option>
                                    <option value="partner" {{ old('analytical_type') == 'partner' ? 'selected' : '' }}>شريك</option>
                                    <option value="other" {{ old('analytical_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('analytical_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-3">
                                <label class="form-label">العملات المفضلة</label>
                                <div class="border rounded p-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="preferred_currencies[]" value="SAR" id="currency_sar" 
                                               {{ is_array(old('preferred_currencies')) && in_array('SAR', old('preferred_currencies')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="currency_sar">
                                            ريال سعودي (SAR)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="preferred_currencies[]" value="USD" id="currency_usd"
                                               {{ is_array(old('preferred_currencies')) && in_array('USD', old('preferred_currencies')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="currency_usd">
                                            دولار أمريكي (USD)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="preferred_currencies[]" value="EUR" id="currency_eur"
                                               {{ is_array(old('preferred_currencies')) && in_array('EUR', old('preferred_currencies')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="currency_eur">
                                            يورو (EUR)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="preferred_currencies[]" value="GBP" id="currency_gbp"
                                               {{ is_array(old('preferred_currencies')) && in_array('GBP', old('preferred_currencies')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="currency_gbp">
                                            جنيه إسترليني (GBP)
                                        </label>
                                    </div>
                                </div>
                                @error('preferred_currencies')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">حدد العملات المسموح بها لهذا الحساب</small>
                            </div>
                        </div>

                        <!-- الحالة -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-toggle-on"></i> الحالة
                                </h5>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        الحساب نشط
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- الأزرار -->
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ
                                </button>
                                <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> إلغاء
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const accountLevel = document.getElementById('account_level');
    const parentSection = document.getElementById('parent_account_section');
    const subSection = document.getElementById('sub_account_section');

    function toggleSections() {
        const level = accountLevel.value;
        
        if (level === 'parent') {
            parentSection.style.display = 'flex';
            subSection.style.display = 'none';
        } else if (level === 'sub') {
            parentSection.style.display = 'none';
            subSection.style.display = 'flex';
        } else {
            parentSection.style.display = 'none';
            subSection.style.display = 'none';
        }
    }

    accountLevel.addEventListener('change', toggleSections);
    
    // تشغيل عند التحميل
    toggleSections();
});
</script>
@endpush
@endsection
