@extends('layouts.app')

@section('title', 'إضافة فاتورة مشتريات جديدة')

@push('styles')
<style>
    /* ===== Luxury Invoice Design ===== */
    
    .luxury-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
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
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
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
        color: rgba(255, 255, 255, 0.8);
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
        color: #1e3a8a;
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid #f59e0b;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .section-title i {
        color: #f59e0b;
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
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
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
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
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
        background: #f1f5f9;
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
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 15px;
        padding: 2rem;
        border: 2px solid #cbd5e1;
    }
    
    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #cbd5e1;
    }
    
    .total-row:last-child {
        border-bottom: none;
        margin-top: 1rem;
        padding-top: 1.5rem;
        border-top: 3px solid #1e3a8a;
    }
    
    .total-label {
        font-weight: 600;
        color: #475569;
        font-size: 1.05rem;
    }
    
    .total-value {
        font-weight: 700;
        color: #1e3a8a;
        font-size: 1.1rem;
    }
    
    .grand-total .total-label,
    .grand-total .total-value {
        font-size: 1.5rem;
        color: #1e3a8a;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff;
        border: none;
        padding: 1rem 3rem;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 700;
        transition: all 0.3s ease;
        box-shadow: 0 8px 16px rgba(245, 158, 11, 0.4);
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
        box-shadow: 0 12px 24px rgba(245, 158, 11, 0.5);
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
    
    .d-none {
        display: none !important;
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
                            <i class="fas fa-file-invoice-dollar me-3"></i>
                            إضافة فاتورة مشتريات جديدة
                        </h2>
                        <p class="subtitle mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            قم بملء جميع الحقول المطلوبة لإنشاء فاتورة مشتريات جديدة
                        </p>
                    </div>
                    <a href="{{ route('purchases.invoices.index') }}" class="back-button">
                        <i class="fas fa-arrow-right me-2"></i>
                        العودة للقائمة
                    </a>
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

                <form action="{{ route('purchases.invoices.store') }}" method="POST" id="invoiceForm">
                    @csrf
                    
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
                                    <span class="text-muted" style="font-size: 0.85em;">(تلقائي)</span>
                                </label>
                                <input type="text" 
                                       class="form-control luxury-input @error('invoice_number') is-invalid @enderror" 
                                       id="invoice_number" 
                                       name="invoice_number" 
                                       value="{{ old('invoice_number') }}" 
                                       placeholder="سيتم توليده تلقائياً إذا ترك فارغاً">
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
                                       value="{{ old('invoice_date', date('Y-m-d')) }}" 
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
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>معتمدة</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="invoice_type_id" class="luxury-label">
                                    <i class="fas fa-tag"></i>
                                    نوع الفاتورة
                                    <span class="required-star">*</span>
                                </label>
                                <select class="form-select luxury-input @error('invoice_type_id') is-invalid @enderror" 
                                        id="invoice_type_id" 
                                        name="invoice_type_id" 
                                        required>
                                    <option value="">اختر نوع الفاتورة</option>
                                    @foreach($invoiceTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('invoice_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('invoice_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
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
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
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
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
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
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقداً</option>
                                    <option value="credit" {{ old('payment_method') == 'credit' ? 'selected' : '' }}>آجل</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                    <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>شيك</option>
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
                                          placeholder="أضف أي ملاحظات إضافية هنا...">{{ old('notes') }}</textarea>
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
                                    <tr class="item-row">
                                        <td>
                                            <select class="form-select luxury-input item-select" name="items[0][item_id]" required>
                                                <option value="">اختر الصنف</option>
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}" data-price="{{ $item->purchase_price }}">
                                                        {{ $item->name }} ({{ $item->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control luxury-input item-quantity" name="items[0][quantity]" value="1" min="1" step="0.01" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control luxury-input item-price" name="items[0][unit_price]" value="0" min="0" step="0.01" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control luxury-input item-discount" name="items[0][discount]" value="0" min="0" step="0.01">
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
                                    <input type="number" class="form-control luxury-input" id="tax_rate" name="tax_rate" value="0" min="0" max="100" step="0.01" style="width: 100px; display: inline-block;">
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
                                <input type="hidden" name="total_amount" id="total_amount" value="0">
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
                                    حفظ الفاتورة
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

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #1e293b; border: 2px solid #ef4444; border-radius: 15px;">
            <div class="modal-header" style="border-bottom: 1px solid #374151;">
                <h5 class="modal-title" id="errorModalLabel" style="color: #ef4444;">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    حدث خطأ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="color: #e5e7eb;">
                <div id="errorContent" style="background: #0f172a; padding: 15px; border-radius: 8px; font-family: monospace; white-space: pre-wrap; word-wrap: break-word; max-height: 400px; overflow-y: auto;"></div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #374151;">
                <button type="button" class="btn" style="background: #3b82f6; color: white;" onclick="copyErrorToClipboard()">
                    <i class="fas fa-copy me-2"></i>
                    نسخ الخطأ
                </button>
                <button type="button" class="btn" style="background: #6b7280; color: white;" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let itemIndex = 1;

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
        e.preventDefault();
        
        // التحقق من الحقول المطلوبة
        const invoiceTypeId = document.getElementById('invoice_type_id').value;
        const supplierId = document.getElementById('supplier_id').value;
        const warehouseId = document.getElementById('warehouse_id').value;
        const paymentMethod = document.getElementById('payment_method').value;
        
        let errors = [];
        
        if (!invoiceTypeId) {
            errors.push('يرجى اختيار نوع الفاتورة');
        }
        if (!supplierId) {
            errors.push('يرجى اختيار المورد');
        }
        if (!warehouseId) {
            errors.push('يرجى اختيار المخزن');
        }
        if (!paymentMethod) {
            errors.push('يرجى اختيار طريقة الدفع');
        }
        
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
            errors.push('يرجى إضافة صنف واحد على الأقل للفاتورة');
        }
        
        // عرض الأخطاء إن وجدت
        if (errors.length > 0) {
            console.log('أخطاء في النموذج:', errors);
            showErrorModal('يرجى تصحيح الأخطاء التالية:\n\n' + errors.join('\n'));
            return false;
        }
        
        // عرض loading state
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        const submitBtnLoading = document.getElementById('submitBtnLoading');
        
        submitBtn.disabled = true;
        submitBtnText.classList.add('d-none');
        submitBtnLoading.classList.remove('d-none');
        
        // إرسال النموذج عبر AJAX
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw data;
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.redirect) {
                window.location.href = data.redirect;
            } else if (data.success) {
                alert(data.message || 'تم حفظ الفاتورة بنجاح');
                window.location.href = '{{ route("purchases.invoices.index") }}';
            }
        })
        .catch(error => {
            console.log('خطأ في حفظ الفاتورة:', error);
            
            // إعادة تفعيل الزر
            submitBtn.disabled = false;
            submitBtnText.classList.remove('d-none');
            submitBtnLoading.classList.add('d-none');
            
            // عرض رسالة الخطأ
            let errorMessage = 'حدث خطأ أثناء حفظ الفاتورة:';
            
            if (error.errors) {
                // أخطاء التحقق من الصحة
                errorMessage += '\n\n';
                for (let field in error.errors) {
                    errorMessage += '• ' + error.errors[field].join('\n• ') + '\n';
                }
            } else if (error.message) {
                errorMessage += '\n' + error.message;
            } else {
                errorMessage += '\nخطأ غير معروف. يرجى المحاولة مرة أخرى.';
            }
            
            showErrorModal(errorMessage);
        });
    });

    // تهيئة الأحداث عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        attachEventListeners();
        calculateTotals();
    });
    
    // عرض رسالة الخطأ في modal
    function showErrorModal(message) {
        document.getElementById('errorContent').textContent = message;
        const modal = new bootstrap.Modal(document.getElementById('errorModal'));
        modal.show();
    }
    
    // نسخ رسالة الخطأ
    function copyErrorToClipboard() {
        const errorText = document.getElementById('errorContent').textContent;
        navigator.clipboard.writeText(errorText).then(function() {
            alert('تم نسخ رسالة الخطأ إلى الحافظة');
        }, function() {
            alert('فشل نسخ رسالة الخطأ');
        });
    }
</script>
@endpush
