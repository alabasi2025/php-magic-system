@extends('layouts.app')

@section('title', 'إنشاء أمر تحويل مخزني')

@section('content')
<style>
    .create-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }
    
    .create-header h1 {
        color: white;
        font-weight: 700;
        font-size: 2rem;
        margin: 0;
    }
    
    .form-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .form-section {
        padding: 2rem;
        border-bottom: 2px solid #f1f5f9;
    }
    
    .form-section:last-child {
        border-bottom: none;
    }
    
    .section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .section-title i {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .form-label {
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    
    .form-control, .form-select {
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    .warehouse-card {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        position: relative;
    }
    
    .warehouse-card .icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 1rem;
    }
    
    .warehouse-card .label {
        font-weight: 700;
        color: #1e293b;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }
    
    .transfer-arrow {
        text-align: center;
        padding: 2rem 0;
    }
    
    .transfer-arrow i {
        font-size: 3rem;
        color: #667eea;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .items-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    
    .items-table thead th {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 1rem;
        font-weight: 600;
        color: #1e293b;
        border: none;
        font-size: 0.9rem;
    }
    
    .items-table thead th:first-child {
        border-top-right-radius: 12px;
    }
    
    .items-table thead th:last-child {
        border-top-left-radius: 12px;
    }
    
    .items-table tbody tr {
        background: #f8fafc;
        transition: all 0.3s ease;
    }
    
    .items-table tbody tr:hover {
        background: #f1f5f9;
    }
    
    .items-table tbody td {
        padding: 0.75rem;
        border: none;
    }
    
    .items-table tbody tr td:first-child {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    
    .items-table tbody tr td:last-child {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }
    
    .btn-add-item {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 12px;
        padding: 0.75rem 2rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-add-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        color: white;
    }
    
    .btn-remove-item {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-remove-item:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(250, 112, 154, 0.4);
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 12px;
        padding: 1rem 3rem;
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        color: white;
    }
    
    .btn-cancel {
        background: #64748b;
        border: none;
        border-radius: 12px;
        padding: 1rem 3rem;
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }
    
    .btn-cancel:hover {
        background: #475569;
        color: white;
    }
    
    .total-section {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 1.5rem;
        border-radius: 16px;
        margin-top: 1rem;
    }
    
    .total-label {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1e293b;
    }
    
    .total-value {
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>

<div class="container-fluid" dir="rtl">
    
    {{-- Header --}}
    <div class="create-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <i class="fas fa-exchange-alt ml-2"></i>
                    إنشاء أمر تحويل مخزني جديد
                </h1>
                <p class="text-white-50 mb-0 mt-2">
                    <i class="fas fa-info-circle ml-1"></i>
                    رقم الأمر التالي: <strong>{{ $nextNumber }}</strong>
                </p>
            </div>
            <div>
                <a href="{{ route('inventory.stock-transfer.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('inventory.stock-transfer.store') }}" method="POST" id="stockTransferForm">
        @csrf
        
        <div class="form-card">
            
            {{-- Warehouse Selection --}}
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-warehouse"></i>
                    <span>المخازن</span>
                </div>
                
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <div class="warehouse-card">
                            <div class="icon">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            <div class="label">المخزن المصدر</div>
                            <select name="warehouse_id" id="fromWarehouse" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                                <option value="">اختر المخزن المصدر</option>
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
                    </div>

                    <div class="col-md-2">
                        <div class="transfer-arrow">
                            <i class="fas fa-arrow-left"></i>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="warehouse-card">
                            <div class="icon">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            <div class="label">المخزن الوجهة</div>
                            <select name="to_warehouse_id" id="toWarehouse" class="form-select @error('to_warehouse_id') is-invalid @enderror" required>
                                <option value="">اختر المخزن الوجهة</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('to_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('to_warehouse_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Basic Information --}}
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-info-circle"></i>
                    <span>المعلومات الأساسية</span>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="fas fa-calendar ml-1"></i>
                            تاريخ التحويل <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="movement_date" class="form-control @error('movement_date') is-invalid @enderror" 
                               value="{{ old('movement_date', date('Y-m-d')) }}" required>
                        @error('movement_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="fas fa-hashtag ml-1"></i>
                            رقم الأمر
                        </label>
                        <input type="text" class="form-control" value="{{ $nextNumber }}" readonly style="background: #f1f5f9; font-weight: 700; color: #667eea;">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <i class="fas fa-sticky-note ml-1"></i>
                            ملاحظات
                        </label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                  rows="3" placeholder="أدخل أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Items Section --}}
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-boxes"></i>
                    <span>الأصناف المحولة</span>
                </div>

                <div class="table-responsive">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 40%;">الصنف <span class="text-danger">*</span></th>
                                <th style="width: 20%;">الكمية <span class="text-danger">*</span></th>
                                <th style="width: 20%;">تكلفة الوحدة <span class="text-danger">*</span></th>
                                <th style="width: 20%;">الإجمالي</th>
                                <th style="width: 10%;" class="text-center">إجراء</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][item_id]" class="form-select form-select-sm item-select" required>
                                        <option value="">اختر الصنف</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" data-price="{{ $item->unit_price ?? 0 }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control form-control-sm item-quantity" 
                                           step="0.001" min="0.001" placeholder="0" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][unit_cost]" class="form-control form-control-sm item-cost" 
                                           step="0.01" min="0" placeholder="0.00" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm item-total" 
                                           readonly style="background: #f1f5f9; font-weight: 700; color: #667eea;">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-remove-item btn-sm" onclick="removeItem(this)" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="button" class="btn btn-add-item" onclick="addItem()">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة صنف جديد
                    </button>
                </div>

                {{-- Total --}}
                <div class="total-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="total-label">
                            <i class="fas fa-calculator ml-2"></i>
                            الإجمالي الكلي:
                        </span>
                        <span class="total-value" id="grandTotal">0.00 ريال</span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="form-section">
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save ml-2"></i>
                        حفظ أمر التحويل
                    </button>
                    <a href="{{ route('inventory.stock-transfer.index') }}" class="btn btn-cancel">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                </div>
            </div>

        </div>
    </form>

</div>

<script>
let itemIndex = 1;

// Add new item row
function addItem() {
    const tbody = document.getElementById('itemsTableBody');
    const newRow = document.createElement('tr');
    newRow.className = 'item-row';
    newRow.innerHTML = `
        <td>
            <select name="items[${itemIndex}][item_id]" class="form-select form-select-sm item-select" required>
                <option value="">اختر الصنف</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" data-price="{{ $item->unit_price ?? 0 }}">
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm item-quantity" 
                   step="0.001" min="0.001" placeholder="0" required>
        </td>
        <td>
            <input type="number" name="items[${itemIndex}][unit_cost]" class="form-control form-control-sm item-cost" 
                   step="0.01" min="0" placeholder="0.00" required>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm item-total" 
                   readonly style="background: #f1f5f9; font-weight: 700; color: #667eea;">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-remove-item btn-sm" onclick="removeItem(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    itemIndex++;
    
    updateRemoveButtons();
    attachItemEventListeners(newRow);
}

// Remove item row
function removeItem(button) {
    const row = button.closest('tr');
    row.remove();
    updateRemoveButtons();
    calculateGrandTotal();
}

// Update remove buttons state
function updateRemoveButtons() {
    const rows = document.querySelectorAll('.item-row');
    const removeButtons = document.querySelectorAll('.btn-remove-item');
    
    removeButtons.forEach((btn, index) => {
        btn.disabled = rows.length === 1;
    });
}

// Calculate item total
function calculateItemTotal(row) {
    const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
    const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
    const total = quantity * cost;
    
    row.querySelector('.item-total').value = total.toFixed(2);
    
    calculateGrandTotal();
}

// Calculate grand total
function calculateGrandTotal() {
    let grandTotal = 0;
    
    document.querySelectorAll('.item-row').forEach(row => {
        const total = parseFloat(row.querySelector('.item-total').value) || 0;
        grandTotal += total;
    });
    
    document.getElementById('grandTotal').textContent = grandTotal.toFixed(2) + ' ريال';
}

// Attach event listeners to item row
function attachItemEventListeners(row) {
    const quantityInput = row.querySelector('.item-quantity');
    const costInput = row.querySelector('.item-cost');
    const itemSelect = row.querySelector('.item-select');
    
    quantityInput.addEventListener('input', () => calculateItemTotal(row));
    costInput.addEventListener('input', () => calculateItemTotal(row));
    
    itemSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        if (price && !costInput.value) {
            costInput.value = parseFloat(price).toFixed(2);
            calculateItemTotal(row);
        }
    });
}

// Initialize event listeners on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.item-row').forEach(row => {
        attachItemEventListeners(row);
    });
    
    updateRemoveButtons();
    
    // Prevent selecting same warehouse
    const fromWarehouse = document.getElementById('fromWarehouse');
    const toWarehouse = document.getElementById('toWarehouse');
    
    fromWarehouse.addEventListener('change', validateWarehouses);
    toWarehouse.addEventListener('change', validateWarehouses);
});

// Validate warehouses
function validateWarehouses() {
    const fromWarehouse = document.getElementById('fromWarehouse');
    const toWarehouse = document.getElementById('toWarehouse');
    
    if (fromWarehouse.value && toWarehouse.value && fromWarehouse.value === toWarehouse.value) {
        alert('لا يمكن تحويل الأصناف إلى نفس المخزن!');
        toWarehouse.value = '';
    }
}

// Form validation
document.getElementById('stockTransferForm').addEventListener('submit', function(e) {
    const fromWarehouse = document.getElementById('fromWarehouse').value;
    const toWarehouse = document.getElementById('toWarehouse').value;
    
    if (!fromWarehouse || !toWarehouse) {
        e.preventDefault();
        alert('يجب اختيار المخزن المصدر والمخزن الوجهة');
        return false;
    }
    
    if (fromWarehouse === toWarehouse) {
        e.preventDefault();
        alert('لا يمكن تحويل الأصناف إلى نفس المخزن!');
        return false;
    }
    
    const rows = document.querySelectorAll('.item-row');
    
    if (rows.length === 0) {
        e.preventDefault();
        alert('يجب إضافة صنف واحد على الأقل');
        return false;
    }
    
    let isValid = true;
    rows.forEach(row => {
        const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
        const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
        
        if (quantity <= 0 || cost < 0) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('يرجى التأكد من إدخال الكمية والتكلفة لجميع الأصناف');
        return false;
    }
});
</script>
@endsection
