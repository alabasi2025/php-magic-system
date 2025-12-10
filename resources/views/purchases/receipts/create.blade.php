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
                            <p class="mb-0 opacity-75">قم بتسجيل استلام البضائع من فاتورة الشراء</p>
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
                            <div class="col-md-3">
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
                            
                            <div class="col-md-3">
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
                            
                            <div class="col-md-3">
                                <label for="warehouse_id" class="form-label fw-bold">
                                    المخزن <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('warehouse_id') is-invalid @enderror" 
                                        id="warehouse_id" 
                                        name="warehouse_id" 
                                        required>
                                    <option value="">اختر المخزن</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-3">
                                <label for="purchase_invoice_id" class="form-label fw-bold">
                                    فاتورة الشراء <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('purchase_invoice_id') is-invalid @enderror" 
                                        id="purchase_invoice_id" 
                                        name="purchase_invoice_id" 
                                        required
                                        disabled>
                                    <option value="">اختر المخزن أولاً</option>
                                </select>
                                @error('purchase_invoice_id')
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
                                        required
                                        disabled>
                                    <option value="">اختر الفاتورة أولاً</option>
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="reference_number" class="form-label fw-bold">
                                    رقم إشعار التسليم
                                </label>
                                <input type="text" 
                                       class="form-control @error('reference_number') is-invalid @enderror" 
                                       id="reference_number" 
                                       name="reference_number" 
                                       value="{{ old('reference_number') }}"
                                       placeholder="رقم إشعار التسليم من المورد">
                                @error('reference_number')
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
                        <h5 class="mb-0">
                            <i class="fas fa-boxes text-warning me-2"></i>
                            الأصناف المستلمة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 35%">الصنف</th>
                                        <th style="width: 15%">الكمية المطلوبة</th>
                                        <th style="width: 15%">الكمية المستلمة</th>
                                        <th style="width: 10%">الوحدة</th>
                                        <th style="width: 25%">ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    <tr class="text-center text-muted">
                                        <td colspan="5">
                                            <i class="fas fa-box-open fa-2x mb-2"></i>
                                            <p class="mb-0">اختر فاتورة الشراء لعرض الأصناف</p>
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
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>معتمد (سيتم تحديث المخزون)</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    عند اختيار "معتمد" سيتم إضافة البضاعة للمخزن مباشرة
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- أزرار الحفظ -->
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
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>
                                    حفظ الاستلام
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
$(document).ready(function() {
    let selectedInvoice = null;
    
    // عند اختيار المخزن → تحميل الفواتير
    $('#warehouse_id').on('change', function() {
        const warehouseId = $(this).val();
        const invoiceSelect = $('#purchase_invoice_id');
        const supplierSelect = $('#supplier_id');
        
        // إعادة تعيين الحقول
        invoiceSelect.html('<option value="">جاري التحميل...</option>').prop('disabled', true);
        supplierSelect.html('<option value="">اختر الفاتورة أولاً</option>').prop('disabled', true);
        $('#itemsTableBody').html('<tr class="text-center text-muted"><td colspan="5"><i class="fas fa-box-open fa-2x mb-2"></i><p class="mb-0">اختر فاتورة الشراء لعرض الأصناف</p></td></tr>');
        
        if (!warehouseId) {
            invoiceSelect.html('<option value="">اختر المخزن أولاً</option>');
            return;
        }
        
        // جلب الفواتير من الخادم
        $.ajax({
            url: '{{ route("purchases.receipts.get-invoices-by-warehouse") }}',
            method: 'GET',
            data: { warehouse_id: warehouseId },
            success: function(invoices) {
                invoiceSelect.html('<option value="">اختر فاتورة الشراء</option>');
                
                if (invoices.length === 0) {
                    invoiceSelect.html('<option value="">لا توجد فواتير متاحة لهذا المخزن</option>');
                    return;
                }
                
                invoices.forEach(function(invoice) {
                    invoiceSelect.append(`
                        <option value="${invoice.id}" data-supplier-id="${invoice.supplier_id}" data-supplier-name="${invoice.supplier.name}">
                            ${invoice.invoice_number} - ${invoice.supplier.name} - ${invoice.total_amount} ريال
                        </option>
                    `);
                });
                
                invoiceSelect.prop('disabled', false);
            },
            error: function() {
                invoiceSelect.html('<option value="">حدث خطأ في تحميل الفواتير</option>');
            }
        });
    });
    
    // عند اختيار الفاتورة → تحميل المورد والأصناف
    $('#purchase_invoice_id').on('change', function() {
        const invoiceId = $(this).val();
        const selectedOption = $(this).find('option:selected');
        const supplierId = selectedOption.data('supplier-id');
        const supplierName = selectedOption.data('supplier-name');
        const supplierSelect = $('#supplier_id');
        const itemsBody = $('#itemsTableBody');
        
        if (!invoiceId) {
            supplierSelect.html('<option value="">اختر الفاتورة أولاً</option>').prop('disabled', true);
            itemsBody.html('<tr class="text-center text-muted"><td colspan="5"><i class="fas fa-box-open fa-2x mb-2"></i><p class="mb-0">اختر فاتورة الشراء لعرض الأصناف</p></td></tr>');
            return;
        }
        
        // تعيين المورد
        supplierSelect.html(`<option value="${supplierId}" selected>${supplierName}</option>`).prop('disabled', false);
        
        // جلب أصناف الفاتورة
        $.ajax({
            url: `/purchases/invoices/${invoiceId}`,
            method: 'GET',
            success: function(invoice) {
                selectedInvoice = invoice;
                loadInvoiceItems(invoice);
            },
            error: function() {
                itemsBody.html('<tr class="text-center text-danger"><td colspan="5">حدث خطأ في تحميل الأصناف</td></tr>');
            }
        });
    });
    
    // تحميل أصناف الفاتورة
    function loadInvoiceItems(invoice) {
        const itemsBody = $('#itemsTableBody');
        itemsBody.empty();
        
        if (!invoice.items || invoice.items.length === 0) {
            itemsBody.html('<tr class="text-center text-muted"><td colspan="5">لا توجد أصناف في هذه الفاتورة</td></tr>');
            return;
        }
        
        invoice.items.forEach(function(item, index) {
            const row = `
                <tr>
                    <td>
                        <input type="hidden" name="items[${index}][item_id]" value="${item.item_id}">
                        <input type="hidden" name="items[${index}][unit_id]" value="${item.item.unit_id || ''}">
                        <strong>${item.item.name}</strong>
                        <br><small class="text-muted">${item.item.sku || ''}</small>
                    </td>
                    <td>
                        <input type="number" 
                               class="form-control" 
                               name="items[${index}][quantity_ordered]" 
                               value="${item.quantity}" 
                               readonly>
                    </td>
                    <td>
                        <input type="number" 
                               class="form-control" 
                               name="items[${index}][quantity_received]" 
                               value="${item.quantity}" 
                               min="0" 
                               max="${item.quantity}" 
                               step="0.01" 
                               required>
                    </td>
                    <td>
                        <span class="badge bg-secondary">${item.item.unit?.name || 'وحدة'}</span>
                    </td>
                    <td>
                        <input type="text" 
                               class="form-control" 
                               name="items[${index}][notes]" 
                               placeholder="ملاحظات">
                    </td>
                </tr>
            `;
            itemsBody.append(row);
        });
    }
    
    // التحقق من صحة النموذج قبل الإرسال
    $('#receiptForm').on('submit', function(e) {
        const itemsCount = $('#itemsTableBody tr').not('.text-center').length;
        
        if (itemsCount === 0) {
            e.preventDefault();
            alert('يجب اختيار فاتورة شراء تحتوي على أصناف');
            return false;
        }
        
        return true;
    });
});
</script>
@endpush
@endsection
