@extends('layouts.app')

@section('title', 'إضافة حساب وسيط جديد')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-2">
                <a href="{{ route('intermediate-accounts.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-right"></i>
                </a>
                <div>
                    <h2 class="mb-1 fw-bold">
                        <i class="fas fa-plus-circle text-primary me-2"></i>
                        إضافة حساب وسيط جديد
                    </h2>
                    <p class="text-muted mb-0">قم بإنشاء حساب وسيط جديد وتحديد نوعه (صناديق، بنوك، محافظ، صرافات)</p>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <h6 class="alert-heading">
                <i class="fas fa-exclamation-circle me-2"></i>
                يوجد أخطاء في النموذج:
            </h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('intermediate-accounts.store') }}" method="POST">
                @csrf

                <div class="row g-4">
                    <!-- الوحدة التنظيمية -->
                    <div class="col-md-6">
                        <label for="unit_id" class="form-label fw-bold">
                            الوحدة التنظيمية <span class="text-danger">*</span>
                        </label>
                        <select name="unit_id" id="unit_id" class="form-select" required>
                            <option value="">-- اختر الوحدة --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->code }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">اختر الوحدة التنظيمية التي سيتبع لها الحساب</small>
                    </div>

                    <!-- الدليل المحاسبي -->
                    <div class="col-md-6">
                        <label for="chart_group_id" class="form-label fw-bold">
                            الدليل المحاسبي <span class="text-danger">*</span>
                        </label>
                        <select name="chart_group_id" id="chart_group_id" class="form-select" required>
                            <option value="">-- اختر الدليل --</option>
                        </select>
                        <small class="text-muted">اختر الدليل المحاسبي الذي سيحتوي على الحساب</small>
                    </div>

                    <!-- الحساب الأب (اختياري) -->
                    <div class="col-md-6">
                        <label for="parent_id" class="form-label fw-bold">
                            الحساب الأب (اختياري)
                        </label>
                        <select name="parent_id" id="parent_id" class="form-select">
                            <option value="">-- لا يوجد --</option>
                        </select>
                        <small class="text-muted">اختر الحساب الأب إذا كان هذا الحساب فرعياً</small>
                    </div>

                    <!-- نوع الحساب الوسيط -->
                    <div class="col-md-6">
                        <label for="intermediate_for" class="form-label fw-bold">
                            نوع الحساب الوسيط <span class="text-danger">*</span>
                        </label>
                        <select name="intermediate_for" id="intermediate_for" class="form-select" required>
                            <option value="">-- اختر النوع --</option>
                            <option value="cash_boxes" {{ old('intermediate_for') == 'cash_boxes' ? 'selected' : '' }}>
                                <i class="fas fa-cash-register"></i> صناديق نقدية
                            </option>
                            <option value="banks" {{ old('intermediate_for') == 'banks' ? 'selected' : '' }}>
                                <i class="fas fa-university"></i> بنوك
                            </option>
                            <option value="wallets" {{ old('intermediate_for') == 'wallets' ? 'selected' : '' }}>
                                <i class="fas fa-wallet"></i> محافظ إلكترونية
                            </option>
                            <option value="atms" {{ old('intermediate_for') == 'atms' ? 'selected' : '' }}>
                                <i class="fas fa-credit-card"></i> صرافات آلية
                            </option>
                        </select>
                        <small class="text-muted">حدد نوع الكيان الذي سيرتبط بهذا الحساب</small>
                    </div>

                    <!-- كود الحساب -->
                    <div class="col-md-6">
                        <label for="code" class="form-label fw-bold">
                            كود الحساب <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="code" 
                               id="code" 
                               class="form-control" 
                               placeholder="مثال: 001-001" 
                               value="{{ old('code') }}" 
                               required>
                        <small class="text-muted">كود فريد للحساب (يجب أن يكون مختلفاً عن باقي الحسابات)</small>
                    </div>

                    <!-- اسم الحساب -->
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold">
                            اسم الحساب <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control" 
                               placeholder="مثال: صناديق التحصيل والعهدة" 
                               value="{{ old('name') }}" 
                               required>
                        <small class="text-muted">اسم واضح ومميز للحساب</small>
                    </div>

                    <!-- الوصف -->
                    <div class="col-12">
                        <label for="description" class="form-label fw-bold">
                            الوصف (اختياري)
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="وصف تفصيلي للحساب الوسيط...">{{ old('description') }}</textarea>
                        <small class="text-muted">معلومات إضافية عن الحساب</small>
                    </div>

                    <!-- الحالة -->
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="is_active" 
                                   id="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_active">
                                الحساب نشط
                            </label>
                        </div>
                        <small class="text-muted">الحسابات النشطة فقط تظهر في قوائم الاختيار</small>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-4 pt-4 border-top">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>
                        حفظ الحساب
                    </button>
                    <a href="{{ route('intermediate-accounts.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const unitSelect = document.getElementById('unit_id');
    const chartGroupSelect = document.getElementById('chart_group_id');
    const parentSelect = document.getElementById('parent_id');

    // Load chart groups when unit changes
    unitSelect.addEventListener('change', function() {
        const unitId = this.value;
        chartGroupSelect.innerHTML = '<option value="">-- اختر الدليل --</option>';
        parentSelect.innerHTML = '<option value="">-- لا يوجد --</option>';

        if (unitId) {
            fetch(`/api/chart-groups/by-unit/${unitId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(group => {
                        const option = document.createElement('option');
                        option.value = group.id;
                        option.textContent = `${group.name} (${group.code})`;
                        chartGroupSelect.appendChild(option);
                    });
                });
        }
    });

    // Load parent accounts when chart group changes
    chartGroupSelect.addEventListener('change', function() {
        const chartGroupId = this.value;
        parentSelect.innerHTML = '<option value="">-- لا يوجد --</option>';

        if (chartGroupId) {
            fetch(`/api/chart-accounts/by-group/${chartGroupId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(account => {
                        const option = document.createElement('option');
                        option.value = account.id;
                        option.textContent = `${account.name} (${account.code})`;
                        parentSelect.appendChild(option);
                    });
                });
        }
    });

    // Trigger change if unit is pre-selected
    if (unitSelect.value) {
        unitSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
