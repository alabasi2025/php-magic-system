@extends('layouts.app')

@section('title', 'إضافة صنف جديد')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1">
                <i class="fas fa-box-open text-primary me-2"></i>
                إضافة صنف جديد
            </h2>
            <p class="text-muted mb-0">قم بإدخال بيانات الصنف والوحدات المتعددة</p>
        </div>
        <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-2"></i>
            رجوع
        </a>
    </div>

    <form action="{{ route('inventory.items.store') }}" method="POST" enctype="multipart/form-data" id="itemForm">
        @csrf
        
        <div class="row">
            <!-- القسم الأيمن: المعلومات الأساسية -->
            <div class="col-lg-8">
                <!-- المعلومات الأساسية -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            المعلومات الأساسية
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="sku" class="form-label">
                                    رمز الصنف (SKU) <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" 
                                       name="sku" 
                                       value="{{ old('sku') }}" 
                                       required
                                       placeholder="مثال: DIESEL-001">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="barcode" class="form-label">الباركود</label>
                                <input type="text" 
                                       class="form-control @error('barcode') is-invalid @enderror" 
                                       id="barcode" 
                                       name="barcode" 
                                       value="{{ old('barcode') }}"
                                       placeholder="اختياري">
                                @error('barcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="name" class="form-label">
                                    اسم الصنف <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required
                                       placeholder="مثال: الديزل">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">الوصف</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3"
                                          placeholder="وصف تفصيلي للصنف...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الوحدات المتعددة -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-balance-scale me-2"></i>
                            الوحدات المتعددة
                        </h5>
                        <button type="button" class="btn btn-sm btn-light" id="addUnitBtn">
                            <i class="fas fa-plus me-1"></i>
                            إضافة وحدة
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>ملاحظة:</strong> يجب تحديد وحدة رئيسية واحدة على الأقل. الوحدة الرئيسية هي الوحدة الأساسية للصرف والمخزون.
                        </div>

                        <div id="unitsContainer">
                            <!-- سيتم إضافة الوحدات هنا ديناميكياً -->
                        </div>
                    </div>
                </div>

                <!-- المخزون -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-warehouse me-2"></i>
                            إعدادات المخزون
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="min_stock" class="form-label">
                                    الحد الأدنى للمخزون <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('min_stock') is-invalid @enderror" 
                                       id="min_stock" 
                                       name="min_stock" 
                                       value="{{ old('min_stock', 0) }}" 
                                       step="0.01"
                                       required>
                                <small class="text-muted">يستخدم للتنبيهات عند انخفاض المخزون</small>
                                @error('min_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="max_stock" class="form-label">
                                    الحد الأقصى للمخزون <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('max_stock') is-invalid @enderror" 
                                       id="max_stock" 
                                       name="max_stock" 
                                       value="{{ old('max_stock', 0) }}" 
                                       step="0.01"
                                       required>
                                <small class="text-muted">يجب أن يكون أكبر من الحد الأدنى</small>
                                @error('max_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- القسم الأيسر: الصورة والحالة -->
            <div class="col-lg-4">
                <!-- الصورة -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-image me-2"></i>
                            صورة الصنف
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <img id="imagePreview" 
                                 src="https://via.placeholder.com/300x300?text=صورة+الصنف" 
                                 alt="معاينة الصورة" 
                                 class="img-fluid rounded"
                                 style="max-height: 300px;">
                        </div>
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               id="image" 
                               name="image" 
                               accept="image/*">
                        <small class="text-muted d-block mt-2">
                            الصيغ المدعومة: JPEG, PNG, GIF (حد أقصى 2MB)
                        </small>
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- الحالة -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-toggle-on me-2"></i>
                            الحالة
                        </h5>
                    </div>
                    <div class="card-body">
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" 
                                name="status" 
                                required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                نشط
                            </option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                معطل
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i>
                            حفظ الصنف
                        </button>
                        <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-2"></i>
                            إلغاء
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template للوحدة -->
<template id="unitTemplate">
    <div class="unit-row card mb-3" data-unit-index="0">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">الوحدة <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select class="form-select unit-select" name="units[0][unit_id]" required>
                            <option value="">اختر الوحدة...</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                            <option value="new">+ إضافة وحدة جديدة</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label">السعة <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control capacity-input" 
                           name="units[0][capacity]" 
                           value="1" 
                           step="0.0001"
                           min="0.0001"
                           required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">السعر (اختياري)</label>
                    <input type="number" 
                           class="form-control price-input" 
                           name="units[0][price]" 
                           step="0.01"
                           min="0"
                           placeholder="0.00">
                </div>

                <div class="col-md-2">
                    <div class="form-check">
                        <input class="form-check-input primary-checkbox" 
                               type="radio" 
                               name="primary_unit" 
                               value="0" 
                               id="primary_0">
                        <label class="form-check-label" for="primary_0">
                            رئيسية
                        </label>
                    </div>
                </div>

                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-unit-btn w-100">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Modal: إضافة وحدة جديدة -->
<div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addUnitModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>
                    إضافة وحدة جديدة
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUnitForm">
                    <div class="mb-3">
                        <label for="new_unit_name" class="form-label">
                            اسم الوحدة <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="new_unit_name" 
                               name="name" 
                               required
                               placeholder="مثال: دبة">
                    </div>
                    <div class="mb-3">
                        <label for="new_unit_symbol" class="form-label">الرمز</label>
                        <input type="text" 
                               class="form-control" 
                               id="new_unit_symbol" 
                               name="symbol"
                               placeholder="مثال: د">
                    </div>
                    <div class="mb-3">
                        <label for="new_unit_description" class="form-label">الوصف</label>
                        <textarea class="form-control" 
                                  id="new_unit_description" 
                                  name="description" 
                                  rows="2"
                                  placeholder="وصف مختصر..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    إلغاء
                </button>
                <button type="button" class="btn btn-success" id="saveNewUnitBtn">
                    <i class="fas fa-save me-2"></i>
                    حفظ الوحدة
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .unit-row {
        border-left: 4px solid #0d6efd;
        transition: all 0.3s ease;
    }
    
    .unit-row:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
    
    .unit-row.primary-unit {
        border-left-color: #198754;
        background-color: #f8fff8;
    }
    
    .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }
    
    #imagePreview {
        border: 2px dashed #dee2e6;
        padding: 10px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let unitIndex = 0;
    const unitsContainer = document.getElementById('unitsContainer');
    const unitTemplate = document.getElementById('unitTemplate');
    const addUnitBtn = document.getElementById('addUnitBtn');
    
    // إضافة وحدة واحدة افتراضية
    addUnit();
    
    // زر إضافة وحدة
    addUnitBtn.addEventListener('click', function() {
        if (document.querySelectorAll('.unit-row').length < 4) {
            addUnit();
        } else {
            alert('الحد الأقصى 4 وحدات');
        }
    });
    
    // دالة إضافة وحدة
    function addUnit() {
        const clone = unitTemplate.content.cloneNode(true);
        const unitRow = clone.querySelector('.unit-row');
        
        // تحديث الـ index
        unitRow.setAttribute('data-unit-index', unitIndex);
        
        // تحديث الأسماء والـ IDs
        unitRow.querySelectorAll('[name]').forEach(input => {
            input.name = input.name.replace('[0]', `[${unitIndex}]`);
        });
        
        const primaryCheckbox = unitRow.querySelector('.primary-checkbox');
        primaryCheckbox.value = unitIndex;
        primaryCheckbox.id = `primary_${unitIndex}`;
        unitRow.querySelector(`label[for="primary_0"]`).setAttribute('for', `primary_${unitIndex}`);
        
        // إذا كانت أول وحدة، اجعلها رئيسية
        if (unitIndex === 0) {
            primaryCheckbox.checked = true;
            unitRow.classList.add('primary-unit');
            unitRow.querySelector('.capacity-input').value = 1;
            unitRow.querySelector('.capacity-input').setAttribute('readonly', true);
        }
        
        // زر الحذف
        const removeBtn = unitRow.querySelector('.remove-unit-btn');
        removeBtn.addEventListener('click', function() {
            if (document.querySelectorAll('.unit-row').length > 1) {
                unitRow.remove();
                updatePrimaryUnit();
            } else {
                alert('يجب وجود وحدة واحدة على الأقل');
            }
        });
        
        // تغيير الوحدة الرئيسية
        primaryCheckbox.addEventListener('change', function() {
            if (this.checked) {
                document.querySelectorAll('.unit-row').forEach(row => {
                    row.classList.remove('primary-unit');
                    row.querySelector('.capacity-input').removeAttribute('readonly');
                });
                unitRow.classList.add('primary-unit');
                unitRow.querySelector('.capacity-input').value = 1;
                unitRow.querySelector('.capacity-input').setAttribute('readonly', true);
            }
        });
        
        unitsContainer.appendChild(unitRow);
        unitIndex++;
    }
    
    // تحديث الوحدة الرئيسية
    function updatePrimaryUnit() {
        const primaryChecked = document.querySelector('.primary-checkbox:checked');
        if (!primaryChecked) {
            const firstCheckbox = document.querySelector('.primary-checkbox');
            if (firstCheckbox) {
                firstCheckbox.checked = true;
                firstCheckbox.dispatchEvent(new Event('change'));
            }
        }
    }
    
    // معالجة اختيار "إضافة وحدة جديدة"
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('unit-select') && e.target.value === 'new') {
            const modal = new bootstrap.Modal(document.getElementById('addUnitModal'));
            modal.show();
            
            // حفظ مرجع للـ select الحالي
            document.getElementById('addUnitModal').dataset.targetSelect = e.target.dataset.unitIndex || '0';
            
            // إعادة تعيين القيمة
            e.target.value = '';
        }
    });
    
    // حفظ وحدة جديدة
    document.getElementById('saveNewUnitBtn').addEventListener('click', function() {
        const form = document.getElementById('addUnitForm');
        const formData = new FormData(form);
        
        // إرسال AJAX
        fetch('{{ route("inventory.item-units.store-ajax") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // إضافة الوحدة الجديدة لجميع الـ dropdowns
                const newOption = `<option value="${data.unit.id}">${data.unit.name}</option>`;
                document.querySelectorAll('.unit-select').forEach(select => {
                    const newOptElement = document.createElement('option');
                    newOptElement.value = data.unit.id;
                    newOptElement.textContent = data.unit.name;
                    
                    // إضافة قبل خيار "إضافة جديدة"
                    const newOptionElement = select.querySelector('option[value="new"]');
                    select.insertBefore(newOptElement, newOptionElement);
                });
                
                // تحديد الوحدة الجديدة في الـ select الحالي
                const targetSelectIndex = document.getElementById('addUnitModal').dataset.targetSelect;
                const targetSelect = document.querySelector(`[name="units[${targetSelectIndex}][unit_id]"]`);
                if (targetSelect) {
                    targetSelect.value = data.unit.id;
                }
                
                // إغلاق الـ modal
                bootstrap.Modal.getInstance(document.getElementById('addUnitModal')).hide();
                
                // إعادة تعيين النموذج
                form.reset();
                
                // عرض رسالة نجاح
                alert(data.message);
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء حفظ الوحدة');
        });
    });
    
    // معاينة الصورة
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // التحقق من النموذج قبل الإرسال
    document.getElementById('itemForm').addEventListener('submit', function(e) {
        const primaryChecked = document.querySelector('.primary-checkbox:checked');
        if (!primaryChecked) {
            e.preventDefault();
            alert('يجب تحديد وحدة رئيسية واحدة على الأقل');
            return false;
        }
        
        const units = document.querySelectorAll('.unit-row');
        if (units.length === 0) {
            e.preventDefault();
            alert('يجب إضافة وحدة واحدة على الأقل');
            return false;
        }
    });
});
</script>
@endpush
