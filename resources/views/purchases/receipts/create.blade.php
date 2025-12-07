@extends('layouts.app')

@section('title', 'تسجيل استلام بضائع جديد')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white shadow-lg">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="mb-2">
                                <i class="fas fa-box-open me-2"></i>
                                تسجيل استلام بضائع جديد
                            </h1>
                            <p class="mb-0 opacity-75">قم بتسجيل استلام البضائع من المورد</p>
                        </div>
                        <a href="{{ route('purchases.receipts.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-right me-2"></i>
                            رجوع
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('purchases.dashboard') }}">نظام المشتريات</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchases.receipts.index') }}">استلام البضائع</a></li>
                    <li class="breadcrumb-item active">تسجيل استلام جديد</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>خطأ!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>يرجى تصحيح الأخطاء التالية:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form method="POST" action="{{ route('purchases.receipts.store') }}" id="receiptForm">
        @csrf
        
        <!-- معلومات الاستلام الأساسية -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            معلومات الاستلام
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="receipt_number" class="form-label fw-bold">
                                    رقم الاستلام
                                    <span class="text-muted small">(تلقائي)</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="receipt_number"
                                       value="AUTO-{{ date('Ymd') }}" 
                                       disabled>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="receipt_date" class="form-label fw-bold">
                                    تاريخ الاستلام <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('receipt_date') is-invalid @enderror" 
                                       id="receipt_date" 
                                       name="receipt_date" 
                                       value="{{ old('receipt_date', date('Y-m-d')) }}"
                                       required>
                                @error('receipt_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="purchase_order_id" class="form-label fw-bold">
                                    أمر الشراء <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('purchase_order_id') is-invalid @enderror" 
                                        id="purchase_order_id" 
                                        name="purchase_order_id" 
                                        required>
                                    <option value="">اختر أمر الشراء</option>
                                    <!-- سيتم ملء الخيارات ديناميكياً -->
                                </select>
                                @error('purchase_order_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات المورد -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-truck text-success me-2"></i>
                            معلومات المورد والشحن
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="supplier_id" class="form-label fw-bold">
                                    المورد <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                        id="supplier_id" 
                                        name="supplier_id" 
                                        required>
                                    <option value="">اختر المورد</option>
                                    <!-- سيتم ملء الخيارات ديناميكياً -->
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="delivery_note" class="form-label fw-bold">
                                    رقم إشعار التسليم
                                </label>
                                <input type="text" 
                                       class="form-control @error('delivery_note') is-invalid @enderror" 
                                       id="delivery_note" 
                                       name="delivery_note" 
                                       value="{{ old('delivery_note') }}"
                                       placeholder="رقم إشعار التسليم من المورد">
                                @error('delivery_note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الأصناف المستلمة -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-boxes text-warning me-2"></i>
                                الأصناف المستلمة
                            </h5>
                            <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                                <i class="fas fa-plus me-1"></i>
                                إضافة صنف
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 30%">الصنف</th>
                                        <th style="width: 15%">الكمية المطلوبة</th>
                                        <th style="width: 15%">الكمية المستلمة</th>
                                        <th style="width: 15%">الوحدة</th>
                                        <th style="width: 20%">ملاحظات</th>
                                        <th style="width: 5%">إجراء</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    <tr class="text-center text-muted">
                                        <td colspan="6">
                                            <i class="fas fa-box-open fa-2x mb-2"></i>
                                            <p class="mb-0">لا توجد أصناف. اضغط "إضافة صنف" للبدء</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الملاحظات والحالة -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-sticky-note text-info me-2"></i>
                            ملاحظات إضافية
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="notes" class="form-label fw-bold">ملاحظات</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="3"
                                          placeholder="أي ملاحظات إضافية حول الاستلام...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="status" class="form-label fw-bold">
                                    حالة الاستلام <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="partial">استلام جزئي</option>
                                    <option value="complete" selected>استلام كامل</option>
                                    <option value="damaged">تالف</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- أزرار الإجراءات -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('purchases.receipts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                إلغاء
                            </a>
                            <div>
                                <button type="submit" class="btn btn-success me-2">
                                    <i class="fas fa-save me-2"></i>
                                    حفظ الاستلام
                                </button>
                                <button type="submit" name="action" value="save_and_approve" class="btn btn-primary">
                                    <i class="fas fa-check-circle me-2"></i>
                                    حفظ واعتماد
                                </button>
                            </div>
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
    let itemCounter = 0;
    
    // إضافة صنف جديد
    document.getElementById('addItemBtn').addEventListener('click', function() {
        const tbody = document.getElementById('itemsTableBody');
        
        // إزالة رسالة "لا توجد أصناف" إذا كانت موجودة
        const emptyRow = tbody.querySelector('tr.text-center');
        if (emptyRow) {
            emptyRow.remove();
        }
        
        itemCounter++;
        const newRow = `
            <tr>
                <td>
                    <select class="form-select form-select-sm" name="items[${itemCounter}][product_id]" required>
                        <option value="">اختر الصنف</option>
                        <!-- سيتم ملء الخيارات ديناميكياً -->
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" name="items[${itemCounter}][ordered_quantity]" min="0" step="0.01" readonly>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" name="items[${itemCounter}][received_quantity]" min="0" step="0.01" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="items[${itemCounter}][unit]" readonly>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="items[${itemCounter}][notes]" placeholder="ملاحظات...">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', newRow);
    });
    
    // حذف صنف
    document.getElementById('itemsTableBody').addEventListener('click', function(e) {
        if (e.target.closest('.remove-item-btn')) {
            const row = e.target.closest('tr');
            row.remove();
            
            // إضافة رسالة "لا توجد أصناف" إذا لم يتبق أي صنف
            const tbody = document.getElementById('itemsTableBody');
            if (tbody.children.length === 0) {
                tbody.innerHTML = `
                    <tr class="text-center text-muted">
                        <td colspan="6">
                            <i class="fas fa-box-open fa-2x mb-2"></i>
                            <p class="mb-0">لا توجد أصناف. اضغط "إضافة صنف" للبدء</p>
                        </td>
                    </tr>
                `;
            }
        }
    });
    
    // التحقق من الفورم قبل الإرسال
    document.getElementById('receiptForm').addEventListener('submit', function(e) {
        const tbody = document.getElementById('itemsTableBody');
        const hasItems = tbody.querySelector('tr:not(.text-center)');
        
        if (!hasItems) {
            e.preventDefault();
            alert('يجب إضافة صنف واحد على الأقل');
            return false;
        }
    });
});
</script>
@endpush
@endsection
