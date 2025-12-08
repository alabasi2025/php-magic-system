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

                <!-- الوحدات المتعددة - جدول -->
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
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>ملاحظة:</strong> حدد الوحدة الرئيسية التي سيتم الصرف والحساب بها. السعة تُحسب بالنسبة للوحدة الرئيسية.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="unitsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="35%">الوحدة <span class="text-danger">*</span></th>
                                        <th width="20%">السعة <span class="text-danger">*</span></th>
                                        <th width="20%">السعر (اختياري)</th>
                                        <th width="15%" class="text-center">رئيسية؟</th>
                                        <th width="10%" class="text-center">حذف</th>
                                    </tr>
                                </thead>
                                <tbody id="unitsTableBody">
                                    <!-- سيتم إضافة الصفوف هنا ديناميكياً -->
                                </tbody>
                            </table>
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
    #unitsTable tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    #unitsTable tbody tr.primary-row {
        background-color: #d1f4e0;
    }
    
    #unitsTable tbody tr.primary-row:hover {
        background-color: #c0efd4;
    }
    
    .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }
    
    #imagePreview {
        border: 2px dashed #dee2e6;
        padding: 10px;
    }
    
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let unitIndex = 0;
    const unitsTableBody = document.getElementById('unitsTableBody');
    const addUnitBtn = document.getElementById('addUnitBtn');
    
    // إضافة صف واحد افتراضي
    addUnitRow();
    
    // زر إضافة وحدة
    addUnitBtn.addEventListener('click', function() {
        if (document.querySelectorAll('#unitsTableBody tr').length < 6) {
            addUnitRow();
        } else {
            alert('الحد الأقصى 6 وحدات');
        }
    });
    
    // دالة إضافة صف وحدة
    function addUnitRow() {
        const row = document.createElement('tr');
        row.setAttribute('data-unit-index', unitIndex);
        
        // إذا كان أول صف، اجعله رئيسي
        const isPrimary = (unitIndex === 0);
        if (isPrimary) {
            row.classList.add('primary-row');
        }
        
        row.innerHTML = `
            <td>
                <select class="form-select unit-select" name="units[${unitIndex}][unit_id]" required>
                    <option value="">اختر الوحدة...</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                    <option value="new" class="text-success fw-bold">+ إضافة وحدة جديدة</option>
                </select>
            </td>
            <td>
                <input type="number" 
                       class="form-control capacity-input" 
                       name="units[${unitIndex}][capacity]" 
                       value="${isPrimary ? '1' : ''}" 
                       step="0.0001"
                       min="0.0001"
                       ${isPrimary ? 'readonly' : ''}
                       placeholder="مثال: 20"
                       required>
            </td>
            <td>
                <input type="number" 
                       class="form-control price-input" 
                       name="units[${unitIndex}][price]" 
                       step="0.01"
                       min="0"
                       placeholder="0.00">
            </td>
            <td class="text-center">
                <div class="form-check d-inline-block">
                    <input class="form-check-input primary-radio" 
                           type="radio" 
                           name="primary_unit" 
                           value="${unitIndex}" 
                           id="primary_${unitIndex}"
                           ${isPrimary ? 'checked' : ''}>
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-unit-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        unitsTableBody.appendChild(row);
        
        // معالجة زر الحذف
        const removeBtn = row.querySelector('.remove-unit-btn');
        removeBtn.addEventListener('click', function() {
            if (document.querySelectorAll('#unitsTableBody tr').length > 1) {
                row.remove();
                updatePrimaryUnit();
            } else {
                alert('يجب وجود وحدة واحدة على الأقل');
            }
        });
        
        // معالجة تغيير الوحدة الرئيسية
        const primaryRadio = row.querySelector('.primary-radio');
        primaryRadio.addEventListener('change', function() {
            if (this.checked) {
                // إزالة التنسيق من جميع الصفوف
                document.querySelectorAll('#unitsTableBody tr').forEach(r => {
                    r.classList.remove('primary-row');
                    const capacityInput = r.querySelector('.capacity-input');
                    capacityInput.removeAttribute('readonly');
                });
                
                // إضافة التنسيق للصف الحالي
                row.classList.add('primary-row');
                const capacityInput = row.querySelector('.capacity-input');
                capacityInput.value = 1;
                capacityInput.setAttribute('readonly', true);
            }
        });
        
        // معالجة اختيار "إضافة وحدة جديدة"
        const unitSelect = row.querySelector('.unit-select');
        unitSelect.addEventListener('change', function() {
            if (this.value === 'new') {
                const modal = new bootstrap.Modal(document.getElementById('addUnitModal'));
                modal.show();
                document.getElementById('addUnitModal').dataset.targetRow = unitIndex;
                this.value = '';
            }
        });
        
        // إذا كان الصف الأول (الرئيسي)، اختر أول وحدة متاحة
        if (isPrimary) {
            // انتظر قليلاً ثم حدد أول وحدة
            setTimeout(function() {
                if (unitSelect.options.length > 2) {
                    unitSelect.selectedIndex = 1;
                    // Trigger change event
                    unitSelect.dispatchEvent(new Event('change'));
                }
            }, 100);
        }
        
        unitIndex++;
    }
    
    // تحديث الوحدة الرئيسية
    function updatePrimaryUnit() {
        const primaryChecked = document.querySelector('.primary-radio:checked');
        if (!primaryChecked) {
            const firstRadio = document.querySelector('.primary-radio');
            if (firstRadio) {
                firstRadio.checked = true;
                firstRadio.dispatchEvent(new Event('change'));
            }
        }
    }
    
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
                document.querySelectorAll('.unit-select').forEach(select => {
                    const newOption = document.createElement('option');
                    newOption.value = data.unit.id;
                    newOption.textContent = data.unit.name;
                    
                    // إضافة قبل خيار "إضافة جديدة"
                    const newOptionElement = select.querySelector('option[value="new"]');
                    select.insertBefore(newOption, newOptionElement);
                });
                
                // تحديد الوحدة الجديدة في الصف الحالي
                const targetRowIndex = document.getElementById('addUnitModal').dataset.targetRow;
                const targetRow = document.querySelector(`tr[data-unit-index="${targetRowIndex}"]`);
                if (targetRow) {
                    const targetSelect = targetRow.querySelector('.unit-select');
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
        const primaryChecked = document.querySelector('.primary-radio:checked');
        if (!primaryChecked) {
            e.preventDefault();
            alert('يجب تحديد وحدة رئيسية واحدة على الأقل');
            return false;
        }
        
        const rows = document.querySelectorAll('#unitsTableBody tr');
        if (rows.length === 0) {
            e.preventDefault();
            alert('يجب إضافة وحدة واحدة على الأقل');
            return false;
        }
        
        // التحقق من اختيار الوحدات
        let allUnitsSelected = true;
        rows.forEach(row => {
            const select = row.querySelector('.unit-select');
            if (!select.value || select.value === 'new') {
                allUnitsSelected = false;
            }
        });
        
        if (!allUnitsSelected) {
            e.preventDefault();
            alert('يجب اختيار وحدة لكل صف');
            return false;
        }
    });
});
</script>
@endpush
