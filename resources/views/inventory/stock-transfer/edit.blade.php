@extends('layouts.app')

@section('title', 'تعديل أمر التحويل')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="card border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-white mb-2 fw-bold">
                        <i class="fas fa-edit me-2"></i>
                        تعديل أمر توريد مخزني
                    </h2>
                    <p class="text-white-50 mb-0">رقم الأمر: {{ $stockTransfer->movement_number }}</p>
                </div>
                <div>
                    <a href="{{ route('inventory.stock-transfer.show', $stockTransfer->id) }}" class="btn btn-light btn-lg rounded-pill px-4">
                        <i class="fas fa-arrow-right me-2"></i>
                        العودة
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($stockTransfer->status !== 'pending')
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>تنبيه!</strong> لا يمكن تعديل الأمر بعد اعتماده أو رفضه.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('inventory.stock-transfer.update', $stockTransfer->id) }}" method="POST" id="editStockInForm">
        @csrf
        @method('PUT')
        
        <!-- المعلومات الأساسية -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    المعلومات الأساسية
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">المخزن <span class="text-danger">*</span></label>
                        <select name="warehouse_id" class="form-select form-select-lg" required {{ $stockTransfer->status !== 'pending' ? 'disabled' : '' }}>
                            <option value="">اختر المخزن</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ $stockTransfer->warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">تاريخ التحويل <span class="text-danger">*</span></label>
                        <input type="date" name="movement_date" class="form-control form-control-lg" 
                               value="{{ \Carbon\Carbon::parse($stockTransfer->movement_date)->format('Y-m-d') }}" 
                               required {{ $stockTransfer->status !== 'pending' ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">رقم الأمر</label>
                        <input type="text" class="form-control form-control-lg" value="{{ $stockTransfer->movement_number }}" disabled>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="أدخل أي ملاحظات إضافية..." {{ $stockTransfer->status !== 'pending' ? 'disabled' : '' }}>{{ $stockTransfer->notes }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- الأصناف -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-boxes text-success me-2"></i>
                    الأصناف
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="itemsTable">
                        <thead style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <tr>
                                <th class="text-white">الصنف <span class="text-warning">*</span></th>
                                <th class="text-white">الكمية <span class="text-warning">*</span></th>
                                <th class="text-white">تكلفة الوحدة <span class="text-warning">*</span></th>
                                <th class="text-white">الإجمالي</th>
                                <th class="text-white">رقم الدفعة</th>
                                <th class="text-white">تاريخ الانتهاء</th>
                                <th class="text-white">إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockTransfer->items as $index => $item)
                            <tr class="item-row">
                                <td>
                                    <select name="items[{{ $index }}][item_id]" class="form-select item-select" required {{ $stockTransfer->status !== 'pending' ? 'disabled' : '' }}>
                                        <option value="">اختر الصنف</option>
                                        @foreach($items as $availableItem)
                                            <option value="{{ $availableItem->id }}" {{ $item->item_id == $availableItem->id ? 'selected' : '' }}>
                                                {{ $availableItem->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity-input" 
                                           value="{{ $item->quantity }}" step="0.001" min="0.001" placeholder="0" required {{ $stockTransfer->status !== 'pending' ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][unit_cost]" class="form-control cost-input" 
                                           value="{{ $item->unit_cost }}" step="0.01" min="0" placeholder="0.00" required {{ $stockTransfer->status !== 'pending' ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    <input type="text" class="form-control total-input" value="{{ number_format($item->total_cost, 2) }}" readonly>
                                </td>
                                <td>
                                    <input type="text" name="items[{{ $index }}][batch_number]" class="form-control" 
                                           value="{{ $item->batch_number }}" placeholder="اختياري" {{ $stockTransfer->status !== 'pending' ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    <input type="date" name="items[{{ $index }}][expiry_date]" class="form-control" 
                                           value="{{ $item->expiry_date }}" {{ $stockTransfer->status !== 'pending' ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    @if($stockTransfer->status === 'pending')
                                    <button type="button" class="btn btn-danger btn-sm rounded-circle" onclick="removeRow(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($stockTransfer->status === 'pending')
                <button type="button" class="btn btn-primary rounded-pill mt-3" onclick="addNewRow()">
                    <i class="fas fa-plus me-2"></i>
                    إضافة صنف جديد
                </button>
                @endif

                <div class="alert alert-info mt-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold">الإجمالي الكلي:</span>
                    <span class="fs-4 fw-bold text-success" id="grandTotal">0.00 ريال</span>
                </div>
            </div>
        </div>

        <!-- الأزرار -->
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('inventory.stock-transfer.show', $stockTransfer->id) }}" class="btn btn-secondary btn-lg rounded-pill px-5">
                        <i class="fas fa-times me-2"></i>
                        إلغاء
                    </a>
                    @if($stockTransfer->status === 'pending')
                    <button type="submit" class="btn btn-success btn-lg rounded-pill px-5">
                        <i class="fas fa-save me-2"></i>
                        حفظ التعديلات
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let itemIndex = {{ $stockTransfer->items->count() }};

// حساب الإجمالي لكل صف
function calculateRowTotal(row) {
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
    const total = quantity * cost;
    row.querySelector('.total-input').value = total.toFixed(2);
    calculateGrandTotal();
}

// حساب الإجمالي الكلي
function calculateGrandTotal() {
    let grandTotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const total = parseFloat(row.querySelector('.total-input').value) || 0;
        grandTotal += total;
    });
    document.getElementById('grandTotal').textContent = grandTotal.toFixed(2) + ' ريال';
}

// إضافة صف جديد
function addNewRow() {
    const tbody = document.querySelector('#itemsTable tbody');
    const newRow = `
        <tr class="item-row">
            <td>
                <select name="items[${itemIndex}][item_id]" class="form-select item-select" required>
                    <option value="">اختر الصنف</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity-input" step="0.001" min="0.001" placeholder="0" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][unit_cost]" class="form-control cost-input" step="0.01" min="0" placeholder="0.00" required>
            </td>
            <td>
                <input type="text" class="form-control total-input" value="0.00" readonly>
            </td>
            <td>
                <input type="text" name="items[${itemIndex}][batch_number]" class="form-control" placeholder="اختياري">
            </td>
            <td>
                <input type="date" name="items[${itemIndex}][expiry_date]" class="form-control">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm rounded-circle" onclick="removeRow(this)">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', newRow);
    itemIndex++;
    attachEventListeners();
}

// حذف صف
function removeRow(button) {
    button.closest('tr').remove();
    calculateGrandTotal();
}

// ربط الأحداث
function attachEventListeners() {
    document.querySelectorAll('.quantity-input, .cost-input').forEach(input => {
        input.removeEventListener('input', handleInput);
        input.addEventListener('input', handleInput);
    });
}

function handleInput(e) {
    calculateRowTotal(e.target.closest('tr'));
}

// تهيئة عند التحميل
document.addEventListener('DOMContentLoaded', function() {
    attachEventListeners();
    calculateGrandTotal();
});
</script>
@endsection
