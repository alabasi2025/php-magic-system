@extends('layouts.app')

@section('title', 'تعديل فاتورة المشتريات')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        تعديل فاتورة المشتريات #{{ $invoice->invoice_number ?? '' }}
                    </h4>
                    <div>
                        <a href="{{ route('purchases.invoices.show', $invoice->id ?? 0) }}" class="btn btn-info btn-sm me-2">
                            <i class="fas fa-eye me-1"></i>
                            عرض
                        </a>
                        <a href="{{ route('purchases.invoices.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-right me-1"></i>
                            العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>يرجى تصحيح الأخطاء التالية:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('purchases.invoices.update', $invoice->id ?? 0) }}" method="POST" id="invoiceForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- معلومات الفاتورة الأساسية -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-info-circle text-warning me-2"></i>
                                    معلومات الفاتورة
                                </h5>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="invoice_number" class="form-label">
                                    <i class="fas fa-hashtag me-1"></i>
                                    رقم الفاتورة <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" 
                                       id="invoice_number" name="invoice_number" 
                                       value="{{ old('invoice_number', $invoice->invoice_number ?? '') }}" required>
                                @error('invoice_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="invoice_date" class="form-label">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    تاريخ الفاتورة <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" 
                                       id="invoice_date" name="invoice_date" 
                                       value="{{ old('invoice_date', $invoice->invoice_date ?? date('Y-m-d')) }}" required>
                                @error('invoice_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="supplier_id" class="form-label">
                                    <i class="fas fa-truck me-1"></i>
                                    المورد <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                        id="supplier_id" name="supplier_id" required>
                                    <option value="">اختر المورد</option>
                                    @foreach($suppliers ?? [] as $supplier)
                                        <option value="{{ $supplier->id }}" 
                                            {{ old('supplier_id', $invoice->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="payment_method" class="form-label">
                                    <i class="fas fa-credit-card me-1"></i>
                                    طريقة الدفع <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('payment_method') is-invalid @enderror" 
                                        id="payment_method" name="payment_method" required>
                                    <option value="">اختر طريقة الدفع</option>
                                    <option value="cash" {{ old('payment_method', $invoice->payment_method ?? '') == 'cash' ? 'selected' : '' }}>نقداً</option>
                                    <option value="credit" {{ old('payment_method', $invoice->payment_method ?? '') == 'credit' ? 'selected' : '' }}>آجل</option>
                                    <option value="bank_transfer" {{ old('payment_method', $invoice->payment_method ?? '') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                    <option value="check" {{ old('payment_method', $invoice->payment_method ?? '') == 'check' ? 'selected' : '' }}>شيك</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">
                                    <i class="fas fa-flag me-1"></i>
                                    الحالة <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="draft" {{ old('status', $invoice->status ?? '') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                    <option value="pending" {{ old('status', $invoice->status ?? '') == 'pending' ? 'selected' : '' }}>معلقة</option>
                                    <option value="approved" {{ old('status', $invoice->status ?? '') == 'approved' ? 'selected' : '' }}>معتمدة</option>
                                    <option value="cancelled" {{ old('status', $invoice->status ?? '') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">
                                    <i class="fas fa-sticky-note me-1"></i>
                                    ملاحظات
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3">{{ old('notes', $invoice->notes ?? '') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- أصناف الفاتورة -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-boxes text-warning me-2"></i>
                                    أصناف الفاتورة
                                </h5>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="itemsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="30%">الصنف</th>
                                                <th width="15%">الكمية</th>
                                                <th width="15%">السعر</th>
                                                <th width="15%">الخصم</th>
                                                <th width="15%">الإجمالي</th>
                                                <th width="10%">
                                                    <button type="button" class="btn btn-success btn-sm" id="addItemBtn">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsTableBody">
                                            @forelse(($invoice->items ?? []) as $index => $item)
                                            <tr class="item-row">
                                                <td>
                                                    <select class="form-select form-select-sm item-select" name="items[{{ $index }}][product_id]" required>
                                                        <option value="">اختر الصنف</option>
                                                        @foreach($products ?? [] as $product)
                                                            <option value="{{ $product->id }}" 
                                                                data-price="{{ $product->purchase_price ?? 0 }}"
                                                                {{ ($item->product_id ?? '') == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm item-quantity" 
                                                           name="items[{{ $index }}][quantity]" min="1" 
                                                           value="{{ $item->quantity ?? 1 }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm item-price" 
                                                           name="items[{{ $index }}][price]" min="0" step="0.01" 
                                                           value="{{ $item->price ?? 0 }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm item-discount" 
                                                           name="items[{{ $index }}][discount]" min="0" step="0.01" 
                                                           value="{{ $item->discount ?? 0 }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm item-total" 
                                                           name="items[{{ $index }}][total]" readonly 
                                                           value="{{ $item->total ?? 0 }}">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm remove-item-btn">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr class="item-row">
                                                <td>
                                                    <select class="form-select form-select-sm item-select" name="items[0][product_id]" required>
                                                        <option value="">اختر الصنف</option>
                                                        @foreach($products ?? [] as $product)
                                                            <option value="{{ $product->id }}" data-price="{{ $product->purchase_price ?? 0 }}">
                                                                {{ $product->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm item-quantity" 
                                                           name="items[0][quantity]" min="1" value="1" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm item-price" 
                                                           name="items[0][price]" min="0" step="0.01" value="0" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm item-discount" 
                                                           name="items[0][discount]" min="0" step="0.01" value="0">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm item-total" 
                                                           name="items[0][total]" readonly value="0">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm remove-item-btn">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- الإجماليات -->
                        <div class="row mb-4">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <strong>المجموع الفرعي:</strong>
                                            <span id="subtotal">0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <strong>الخصم الإجمالي:</strong>
                                            <span id="totalDiscount">0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <label for="tax_rate" class="mb-0">الضريبة (%):</label>
                                            <input type="number" class="form-control form-control-sm w-50" 
                                                   id="tax_rate" name="tax_rate" min="0" step="0.01" 
                                                   value="{{ old('tax_rate', $invoice->tax_rate ?? 0) }}">
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <strong>قيمة الضريبة:</strong>
                                            <span id="taxAmount">0.00</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <strong class="text-warning fs-5">الإجمالي النهائي:</strong>
                                            <strong class="text-warning fs-5" id="grandTotal">0.00</strong>
                                        </div>
                                        <input type="hidden" name="total_amount" id="total_amount" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('purchases.invoices.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-1"></i>
                                        حفظ التعديلات
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ count($invoice->items ?? []) }};

    // إضافة صف جديد
    document.getElementById('addItemBtn').addEventListener('click', function() {
        const tbody = document.getElementById('itemsTableBody');
        const newRow = tbody.querySelector('.item-row').cloneNode(true);
        
        // تحديث الأسماء والقيم
        newRow.querySelectorAll('input, select').forEach(function(input) {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[\d+\]/, '[' + itemIndex + ']'));
            }
            if (input.type !== 'number') {
                input.value = '';
            } else {
                input.value = input.classList.contains('item-quantity') ? '1' : '0';
            }
        });
        
        tbody.appendChild(newRow);
        itemIndex++;
        calculateTotals();
    });

    // حذف صف
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item-btn')) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('.item-row').remove();
                calculateTotals();
            } else {
                alert('يجب أن يحتوي الفاتورة على صنف واحد على الأقل');
            }
        }
    });

    // تحديث السعر عند اختيار الصنف
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const price = selectedOption.getAttribute('data-price') || 0;
            const row = e.target.closest('.item-row');
            row.querySelector('.item-price').value = price;
            calculateRowTotal(row);
        }
    });

    // حساب إجمالي الصف
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('item-quantity') || 
            e.target.classList.contains('item-price') || 
            e.target.classList.contains('item-discount')) {
            const row = e.target.closest('.item-row');
            calculateRowTotal(row);
        }
        
        if (e.target.id === 'tax_rate') {
            calculateTotals();
        }
    });

    function calculateRowTotal(row) {
        const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const discount = parseFloat(row.querySelector('.item-discount').value) || 0;
        
        const total = (quantity * price) - discount;
        row.querySelector('.item-total').value = total.toFixed(2);
        
        calculateTotals();
    }

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
    
    // حساب الإجماليات عند تحميل الصفحة
    calculateTotals();
});
</script>
@endpush
@endsection
