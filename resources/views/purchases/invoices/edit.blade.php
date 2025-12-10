@extends('layouts.app')

@section('title', 'تعديل فاتورة المشتريات')

@push('styles')
<style>
    /* ===== Luxury Invoice Edit Design ===== */
    
    .luxury-container {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    .luxury-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        animation: slideIn 0.5s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .luxury-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        padding: 2.5rem 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .luxury-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
        animation: pulse 3s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    .luxury-header h2 {
        color: #ffffff;
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
    }
    
    .luxury-header .subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.95rem;
        margin-top: 0.5rem;
        position: relative;
        z-index: 1;
    }
    
    .luxury-body {
        padding: 2.5rem;
    }
    
    .section-card {
        background: #f8fafc;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .section-card:hover {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .section-title {
        color: #f59e0b;
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid #1e3a8a;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .section-title i {
        color: #1e3a8a;
        font-size: 1.5rem;
    }
    
    .luxury-input {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #ffffff;
    }
    
    .luxury-input:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        outline: none;
    }
    
    .luxury-label {
        color: #334155;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .luxury-label i {
        color: #64748b;
    }
    
    .required-star {
        color: #ef4444;
        font-weight: bold;
    }
    
    .items-table {
        background: #ffffff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
    
    .items-table thead {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff;
    }
    
    .items-table thead th {
        padding: 1rem;
        font-weight: 600;
        border: none;
    }
    
    .items-table tbody tr {
        transition: all 0.3s ease;
    }
    
    .items-table tbody tr:hover {
        background: #fef3c7;
    }
    
    .items-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    .btn-add-item {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
    }
    
    .btn-add-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(16, 185, 129, 0.4);
        color: #ffffff;
    }
    
    .btn-remove-item {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #ffffff;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .btn-remove-item:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
    }
    
    .totals-card {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-radius: 15px;
        padding: 2rem;
        border: 2px solid #fbbf24;
    }
    
    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #fbbf24;
    }
    
    .total-row:last-child {
        border-bottom: none;
        margin-top: 1rem;
        padding-top: 1.5rem;
        border-top: 3px solid #f59e0b;
    }
    
    .total-label {
        font-weight: 600;
        color: #92400e;
        font-size: 1.05rem;
    }
    
    .total-value {
        font-weight: 700;
        color: #f59e0b;
        font-size: 1.1rem;
    }
    
    .grand-total .total-label,
    .grand-total .total-value {
        font-size: 1.5rem;
        color: #92400e;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: #ffffff;
        border: none;
        padding: 1rem 3rem;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 700;
        transition: all 0.3s ease;
        box-shadow: 0 8px 16px rgba(30, 58, 138, 0.4);
        position: relative;
        overflow: hidden;
    }
    
    .btn-submit::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .btn-submit:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(30, 58, 138, 0.5);
    }
    
    .btn-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    .btn-cancel {
        background: #ffffff;
        color: #64748b;
        border: 2px solid #cbd5e1;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-cancel:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        color: #475569;
    }
    
    .btn-view {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        border: 2px solid rgba(255, 255, 255, 0.3);
        padding: 0.5rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
        margin-left: 0.5rem;
    }
    
    .btn-view:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        color: #ffffff;
    }
    
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }
    
    .alert-luxury {
        border-radius: 12px;
        border: none;
        padding: 1.25rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }
    
    .back-button {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        border: 2px solid rgba(255, 255, 255, 0.3);
        padding: 0.5rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
    }
    
    .back-button:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        color: #ffffff;
        transform: translateX(5px);
    }
</style>
@endpush

@section('content')
<div class="luxury-container">
    <div class="container">
        <div class="luxury-card">
            <!-- Header -->
            <div class="luxury-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2>
                            <i class="fas fa-edit me-3"></i>
                            تعديل فاتورة المشتريات #{{ $invoice->invoice_number }}
                        </h2>
                        <p class="subtitle mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            قم بتعديل البيانات المطلوبة ثم احفظ التغييرات
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('purchases.invoices.show', $invoice->id) }}" class="btn-view">
                            <i class="fas fa-eye me-2"></i>
                            عرض
                        </a>
                        <a href="{{ route('purchases.invoices.index') }}" class="back-button">
                            <i class="fas fa-arrow-right me-2"></i>
                            العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="luxury-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-luxury alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            يرجى تصحيح الأخطاء التالية:
                        </h5>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('purchases.invoices.update', $invoice->id) }}" method="POST" id="invoiceForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- معلومات الفاتورة الأساسية -->
                    <div class="section-card">
                        <h3 class="section-title">
                            <i class="fas fa-info-circle"></i>
                            معلومات الفاتورة الأساسية
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="invoice_number" class="luxury-label">
                                    <i class="fas fa-hashtag"></i>
                                    رقم الفاتورة
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control luxury-input @error('invoice_number') is-invalid @enderror" 
                                       id="invoice_number" 
                                       name="invoice_number" 
                                       value="{{ old('invoice_number', $invoice->invoice_number) }}" 
                                       placeholder="مثال: INV-2025-001"
                                       required>
                                @error('invoice_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="invoice_date" class="luxury-label">
                                    <i class="fas fa-calendar-alt"></i>
                                    تاريخ الفاتورة
                                    <span class="required-star">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control luxury-input @error('invoice_date') is-invalid @enderror" 
                                       id="invoice_date" 
                                       name="invoice_date" 
                                       value="{{ old('invoice_date', $invoice->invoice_date) }}" 
                                       required>
                                @error('invoice_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="status" class="luxury-label">
                                    <i class="fas fa-flag"></i>
                                    الحالة
                                    <span class="required-star">*</span>
                                </label>
                                <select class="form-select luxury-input @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="draft" {{ old('status', $invoice->status) == 'draft' ? 'selected' : '' }}>مسودة</option>
                                    <option value="pending" {{ old('status', $invoice->status) == 'pending' ? 'selected' : '' }}>معلقة</option>
                                    <option value="approved" {{ old('status', $invoice->status) == 'approved' ? 'selected' : '' }}>معتمدة</option>
                                    <option value="cancelled" {{ old('status', $invoice->status) == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="supplier_id" class="luxury-label">
                                    <i class="fas fa-truck"></i>
                                    المورد
                                    <span class="required-star">*</span>
                                </label>
                                <select class="form-select luxury-input @error('supplier_id') is-invalid @enderror" 
                                        id="supplier_id" 
                                        name="supplier_id" 
                                        required>
                                    <option value="">اختر المورد</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $invoice->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="warehouse_id" class="luxury-label">
                                    <i class="fas fa-warehouse"></i>
                                    المخزن
                                    <span class="required-star">*</span>
                                </label>
                                <select class="form-select luxury-input @error('warehouse_id') is-invalid @enderror" 
                                        id="warehouse_id" 
                                        name="warehouse_id" 
                                        required>
                                    <option value="">اختر المخزن</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $invoice->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="payment_method" class="luxury-label">
                                    <i class="fas fa-credit-card"></i>
                                    طريقة الدفع
                                    <span class="required-star">*</span>
                                </label>
                                <select class="form-select luxury-input @error('payment_method') is-invalid @enderror" 
                                        id="payment_method" 
                                        name="payment_method" 
                                        required>
                                    <option value="">اختر طريقة الدفع</option>
                                    <option value="cash" {{ old('payment_method', $invoice->payment_method) == 'cash' ? 'selected' : '' }}>نقداً</option>
                                    <option value="credit" {{ old('payment_method', $invoice->payment_method) == 'credit' ? 'selected' : '' }}>آجل</option>
                                    <option value="bank_transfer" {{ old('payment_method', $invoice->payment_method) == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                    <option value="cheque" {{ old('payment_method', $invoice->payment_method) == 'cheque' ? 'selected' : '' }}>شيك</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <label for="notes" class="luxury-label">
                                    <i class="fas fa-sticky-note"></i>
                                    ملاحظات
                                </label>
                                <textarea class="form-control luxury-input @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="3" 
                                          placeholder="أضف أي ملاحظات إضافية هنا...">{{ old('notes', $invoice->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- أصناف الفاتورة -->
                    <div class="section-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="section-title mb-0">
                                <i class="fas fa-boxes"></i>
                                أصناف الفاتورة
                            </h3>
                            <button type="button" class="btn btn-add-item" id="addItemBtn">
                                <i class="fas fa-plus-circle me-2"></i>
                                إضافة صنف
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table items-table">
                                <thead>
                                    <tr>
                                        <th style="width: 30%;">الصنف</th>
                                        <th style="width: 15%;">الكمية</th>
                                        <th style="width: 15%;">السعر</th>
                                        <th style="width: 15%;">الخصم</th>
                                        <th style="width: 15%;">الإجمالي</th>
                                        <th style="width: 10%;">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    @foreach($invoice->items as $index => $item)
                                    <tr class="item-row">
                                        <td>
                                            <select class="form-select luxury-input item-select" name="items[{{ $index }}][item_id]" required>
                                                <option value="">اختر الصنف</option>
                                                @foreach($items as $availableItem)
                                                    <option value="{{ $availableItem->id }}" 
                                                            data-price="{{ $availableItem->purchase_price }}"
                                                            {{ $item->item_id == $availableItem->id ? 'selected' : '' }}>
                                                        {{ $availableItem->name }} ({{ $availableItem->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control luxury-input item-quantity" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="1" step="0.01" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control luxury-input item-price" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}" min="0" step="0.01" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control luxury-input item-discount" name="items[{{ $index }}][discount]" value="{{ $item->discount ?? 0 }}" min="0" step="0.01">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control luxury-input item-total" value="{{ ($item->quantity * $item->unit_price) - ($item->discount ?? 0) }}" readonly>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-remove-item" onclick="removeItem(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- الإجماليات -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="totals-card">
                                <div class="total-row">
                                    <span class="total-label">
                                        <i class="fas fa-calculator me-2"></i>
                                        المجموع الفرعي:
                                    </span>
                                    <span class="total-value" id="subtotal">0.00</span>
                                </div>
                                <div class="total-row">
                                    <span class="total-label">
                                        <i class="fas fa-tag me-2"></i>
                                        الخصم الإجمالي:
                                    </span>
                                    <span class="total-value" id="totalDiscount">0.00</span>
                                </div>
                                <div class="total-row">
                                    <span class="total-label">
                                        <i class="fas fa-percentage me-2"></i>
                                        الضريبة (%):
                                    </span>
                                    <input type="number" class="form-control luxury-input" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $invoice->tax_rate ?? 0) }}" min="0" max="100" step="0.01" style="width: 100px; display: inline-block;">
                                </div>
                                <div class="total-row">
                                    <span class="total-label">
                                        <i class="fas fa-receipt me-2"></i>
                                        قيمة الضريبة:
                                    </span>
                                    <span class="total-value" id="taxAmount">0.00</span>
                                </div>
                                <div class="total-row grand-total">
                                    <span class="total-label">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        الإجمالي النهائي:
                                    </span>
                                    <span class="total-value" id="grandTotal">0.00</span>
                                </div>
                                <input type="hidden" name="total_amount" id="total_amount" value="{{ $invoice->total_amount }}">
                            </div>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <a href="{{ route('purchases.invoices.index') }}" class="btn btn-cancel me-3">
                                <i class="fas fa-times me-2"></i>
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-submit" id="submitBtn">
                                <span id="submitBtnText">
                                    <i class="fas fa-save me-2"></i>
                                    حفظ التعديلات
                                </span>
                                <span id="submitBtnLoading" class="d-none">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    جاري الحفظ...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let itemIndex = {{ count($invoice->items) }};

    // إضافة صنف جديد
    document.getElementById('addItemBtn').addEventListener('click', function() {
        const tbody = document.getElementById('itemsTableBody');
        const newRow = `
            <tr class="item-row">
                <td>
                    <select class="form-select luxury-input item-select" name="items[${itemIndex}][item_id]" required>
                        <option value="">اختر الصنف</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-price="{{ $item->purchase_price }}">
                                {{ $item->name }} ({{ $item->code }})
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control luxury-input item-quantity" name="items[${itemIndex}][quantity]" value="1" min="1" step="0.01" required>
                </td>
                <td>
                    <input type="number" class="form-control luxury-input item-price" name="items[${itemIndex}][unit_price]" value="0" min="0" step="0.01" required>
                </td>
                <td>
                    <input type="number" class="form-control luxury-input item-discount" name="items[${itemIndex}][discount]" value="0" min="0" step="0.01">
                </td>
                <td>
                    <input type="number" class="form-control luxury-input item-total" value="0" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-remove-item" onclick="removeItem(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', newRow);
        itemIndex++;
        attachEventListeners();
    });

    // حذف صنف
    function removeItem(button) {
        const row = button.closest('tr');
        const tbody = document.getElementById('itemsTableBody');
        if (tbody.children.length > 1) {
            row.remove();
            calculateTotals();
        } else {
            alert('يجب أن يكون هناك صنف واحد على الأقل');
        }
    }

    // حساب إجمالي الصف
    function calculateRowTotal(row) {
        const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const discount = parseFloat(row.querySelector('.item-discount').value) || 0;
        
        const total = (quantity * price) - discount;
        row.querySelector('.item-total').value = total.toFixed(2);
        
        calculateTotals();
    }

    // حساب الإجماليات
    function calculateTotals() {
        let subtotal = 0;
        let totalDiscount = 0;
        
        document.querySelectorAll('.item-row').forEach(function(row) {
            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const discount = parseFloat(row.querySelector('.item-discount').value) || 0;
            
            subtotal += (quantity * price);
            totalDiscount += discount;
        });
        
        const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
        const taxAmount = ((subtotal - totalDiscount) * taxRate) / 100;
        const grandTotal = (subtotal - totalDiscount) + taxAmount;
        
        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('totalDiscount').textContent = totalDiscount.toFixed(2);
        document.getElementById('taxAmount').textContent = taxAmount.toFixed(2);
        document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
        document.getElementById('total_amount').value = grandTotal.toFixed(2);
    }

    // ربط الأحداث
    function attachEventListeners() {
        // عند اختيار صنف
        document.querySelectorAll('.item-select').forEach(function(select) {
            select.removeEventListener('change', handleItemSelect);
            select.addEventListener('change', handleItemSelect);
        });
        
        // عند تغيير الكمية أو السعر أو الخصم
        document.querySelectorAll('.item-quantity, .item-price, .item-discount').forEach(function(input) {
            input.removeEventListener('input', handleInputChange);
            input.addEventListener('input', handleInputChange);
        });
    }

    function handleItemSelect(e) {
        const row = e.target.closest('tr');
        const selectedOption = e.target.options[e.target.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || 0;
        row.querySelector('.item-price').value = price;
        calculateRowTotal(row);
    }

    function handleInputChange(e) {
        const row = e.target.closest('tr');
        calculateRowTotal(row);
    }

    // عند تغيير نسبة الضريبة
    document.getElementById('tax_rate').addEventListener('input', calculateTotals);

    // عند إرسال النموذج
    document.getElementById('invoiceForm').addEventListener('submit', function(e) {
        // التحقق من وجود صنف واحد على الأقل
        const items = document.querySelectorAll('.item-row');
        let hasValidItem = false;
        
        items.forEach(function(row) {
            const itemSelect = row.querySelector('.item-select');
            if (itemSelect && itemSelect.value) {
                hasValidItem = true;
            }
        });
        
        if (!hasValidItem) {
            e.preventDefault();
            alert('يرجى إضافة صنف واحد على الأقل للفاتورة');
            return false;
        }
        
        // عرض loading state
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        const submitBtnLoading = document.getElementById('submitBtnLoading');
        
        submitBtn.disabled = true;
        submitBtnText.classList.add('d-none');
        submitBtnLoading.classList.remove('d-none');
    });

    // تهيئة الأحداث عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        attachEventListeners();
        calculateTotals();
    });
</script>
@endpush
