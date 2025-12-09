@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-plus-circle text-primary me-2"></i>
                        إضافة صنف جديد
                    </h2>
                    <p class="text-muted mb-0">قم بإدخال بيانات الصنف بدقة</p>
                </div>
                <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('inventory.items.store') }}" method="POST" enctype="multipart/form-data" id="itemForm">
        @csrf
        
        <div class="row">
            <!-- Main Form Section -->
            <div class="col-lg-8">
                <!-- Basic Information Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            المعلومات الأساسية
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- SKU -->
                            <div class="col-md-6">
                                <label for="sku" class="form-label fw-bold">
                                    <i class="fas fa-barcode text-primary me-1"></i>
                                    رمز الصنف (SKU)
                                    <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control form-control-lg @error('sku') is-invalid @enderror" 
                                    id="sku" 
                                    name="sku" 
                                    value="{{ old('sku') }}" 
                                    placeholder="مثال: DIESEL-001"
                                    required
                                    autofocus>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">رمز فريد لتعريف الصنف</small>
                            </div>

                            <!-- Barcode -->
                            <div class="col-md-6">
                                <label for="barcode" class="form-label fw-bold">
                                    <i class="fas fa-qrcode text-primary me-1"></i>
                                    الباركود
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control form-control-lg @error('barcode') is-invalid @enderror" 
                                    id="barcode" 
                                    name="barcode" 
                                    value="{{ old('barcode') }}"
                                    placeholder="اختياري">
                                @error('barcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">رمز الباركود (اختياري)</small>
                            </div>

                            <!-- Item Name -->
                            <div class="col-12">
                                <label for="name" class="form-label fw-bold">
                                    <i class="fas fa-tag text-primary me-1"></i>
                                    اسم الصنف
                                    <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name') }}" 
                                    placeholder="مثال: ديزل"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-bold">
                                    <i class="fas fa-align-left text-primary me-1"></i>
                                    الوصف
                                </label>
                                <textarea 
                                    class="form-control @error('description') is-invalid @enderror" 
                                    id="description" 
                                    name="description" 
                                    rows="3"
                                    placeholder="وصف تفصيلي للصنف (اختياري)">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Unit Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-dollar-sign me-2"></i>
                            السعر والوحدة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Unit (Fixed to Liter) -->
                            <div class="col-md-6">
                                <label for="unit_id" class="form-label fw-bold">
                                    <i class="fas fa-balance-scale text-success me-1"></i>
                                    الوحدة
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                                    <option value="">اختر الوحدة</option>
                                    @foreach($units as $unit)
                                        @if($unit->name === 'لتر')
                                            <option value="{{ $unit->id }}" selected>{{ $unit->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">الوحدة الافتراضية: لتر</small>
                            </div>

                            <!-- Unit Price -->
                            <div class="col-md-6">
                                <label for="unit_price" class="form-label fw-bold">
                                    <i class="fas fa-money-bill-wave text-success me-1"></i>
                                    سعر اللتر
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        class="form-control @error('unit_price') is-invalid @enderror" 
                                        id="unit_price" 
                                        name="unit_price" 
                                        value="{{ old('unit_price', 0) }}" 
                                        placeholder="0.00"
                                        required>
                                    <span class="input-group-text">ريال</span>
                                    @error('unit_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Management Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-warehouse me-2"></i>
                            إدارة المخزون
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Min Stock -->
                            <div class="col-md-6">
                                <label for="min_stock" class="form-label fw-bold">
                                    <i class="fas fa-arrow-down text-warning me-1"></i>
                                    الحد الأدنى للمخزون
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        class="form-control @error('min_stock') is-invalid @enderror" 
                                        id="min_stock" 
                                        name="min_stock" 
                                        value="{{ old('min_stock', 0) }}" 
                                        placeholder="0"
                                        required>
                                    <span class="input-group-text">لتر</span>
                                    @error('min_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">يُستخدم للتنبيهات عند انخفاض المخزون</small>
                            </div>

                            <!-- Max Stock -->
                            <div class="col-md-6">
                                <label for="max_stock" class="form-label fw-bold">
                                    <i class="fas fa-arrow-up text-warning me-1"></i>
                                    الحد الأقصى للمخزون
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        class="form-control @error('max_stock') is-invalid @enderror" 
                                        id="max_stock" 
                                        name="max_stock" 
                                        value="{{ old('max_stock', 0) }}" 
                                        placeholder="0"
                                        required>
                                    <span class="input-group-text">لتر</span>
                                    @error('max_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">يجب أن يكون أكبر من الحد الأدنى</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image & Status Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-image me-2"></i>
                            الصورة والحالة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Image Upload -->
                            <div class="col-md-6">
                                <label for="image" class="form-label fw-bold">
                                    <i class="fas fa-camera text-info me-1"></i>
                                    صورة الصنف
                                </label>
                                <input 
                                    type="file" 
                                    class="form-control @error('image') is-invalid @enderror" 
                                    id="image" 
                                    name="image" 
                                    accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">JPEG, PNG, GIF (حد أقصى 2MB)</small>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-bold">
                                    <i class="fas fa-toggle-on text-info me-1"></i>
                                    الحالة
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                        <i class="fas fa-check-circle"></i> نشط
                                    </option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                        <i class="fas fa-times-circle"></i> معطل
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('inventory.items.index') }}" class="btn btn-lg btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-lg btn-primary px-5">
                                <i class="fas fa-save me-2"></i>
                                حفظ الصنف
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Section -->
            <div class="col-lg-4">
                <!-- Help Card -->
                <div class="card shadow-sm mb-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            نصائح مهمة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>ملاحظة:</strong> جميع الحقول المميزة بـ <span class="text-danger">*</span> إلزامية
                        </div>
                        
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                    <div>
                                        <strong>رمز SKU</strong>
                                        <p class="mb-0 text-muted small">يجب أن يكون فريداً ولا يتكرر</p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                    <div>
                                        <strong>الوحدة</strong>
                                        <p class="mb-0 text-muted small">محددة مسبقاً باللتر لجميع الأصناف</p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                    <div>
                                        <strong>حدود المخزون</strong>
                                        <p class="mb-0 text-muted small">الحد الأقصى يجب أن يكون أكبر من الحد الأدنى</p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                    <div>
                                        <strong>الصورة</strong>
                                        <p class="mb-0 text-muted small">اختيارية، الصيغ المدعومة: JPEG, PNG, GIF</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Quick Stats Card -->
                <div class="card shadow-sm border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            إحصائيات سريعة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <h3 class="text-success mb-1">{{ $totalItems ?? 0 }}</h3>
                            <p class="text-muted mb-0">إجمالي الأصناف</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('itemForm');
    const minStock = document.getElementById('min_stock');
    const maxStock = document.getElementById('max_stock');
    
    form.addEventListener('submit', function(e) {
        const minValue = parseFloat(minStock.value);
        const maxValue = parseFloat(maxStock.value);
        
        if (maxValue <= minValue) {
            e.preventDefault();
            alert('الحد الأقصى للمخزون يجب أن يكون أكبر من الحد الأدنى');
            maxStock.focus();
            return false;
        }
    });
    
    // Auto-generate SKU suggestion
    const nameInput = document.getElementById('name');
    const skuInput = document.getElementById('sku');
    
    nameInput.addEventListener('blur', function() {
        if (!skuInput.value) {
            const name = this.value.trim();
            if (name) {
                const sku = name.substring(0, 3).toUpperCase() + '-' + Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                skuInput.value = sku;
            }
        }
    });
});
</script>
@endpush
@endsection
