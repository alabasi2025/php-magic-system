@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="fas fa-edit"></i> تعديل أمر الشراء</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('purchases.dashboard') }}">نظام المشتريات</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchases.orders.index') }}">أوامر الشراء</a></li>
                    <li class="breadcrumb-item active">تعديل أمر الشراء</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('purchases.orders.update', $order->id ?? 1) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- معلومات أساسية -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> المعلومات الأساسية</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="order_number" class="form-label">رقم الأمر <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('order_number') is-invalid @enderror" 
                                       id="order_number" name="order_number" 
                                       value="{{ old('order_number', $order->order_number ?? 'PO-001') }}" required>
                                @error('order_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="order_date" class="form-label">تاريخ الأمر <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('order_date') is-invalid @enderror" 
                                       id="order_date" name="order_date" 
                                       value="{{ old('order_date', $order->order_date ?? date('Y-m-d')) }}" required>
                                @error('order_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="supplier_id" class="form-label">المورد <span class="text-danger">*</span></label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                        id="supplier_id" name="supplier_id" required>
                                    <option value="">اختر المورد</option>
                                    <!-- سيتم تعبئة الموردين من قاعدة البيانات -->
                                    @if(isset($order->supplier_id))
                                        <option value="{{ $order->supplier_id }}" selected>{{ $order->supplier->name ?? 'المورد' }}</option>
                                    @endif
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="delivery_date" class="form-label">تاريخ التسليم المتوقع</label>
                                <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" 
                                       id="delivery_date" name="delivery_date" 
                                       value="{{ old('delivery_date', $order->delivery_date ?? '') }}">
                                @error('delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="payment_terms" class="form-label">شروط الدفع</label>
                                <select class="form-select @error('payment_terms') is-invalid @enderror" 
                                        id="payment_terms" name="payment_terms">
                                    <option value="">اختر شروط الدفع</option>
                                    <option value="cash" {{ old('payment_terms', $order->payment_terms ?? '') == 'cash' ? 'selected' : '' }}>نقدي</option>
                                    <option value="credit_30" {{ old('payment_terms', $order->payment_terms ?? '') == 'credit_30' ? 'selected' : '' }}>آجل 30 يوم</option>
                                    <option value="credit_60" {{ old('payment_terms', $order->payment_terms ?? '') == 'credit_60' ? 'selected' : '' }}>آجل 60 يوم</option>
                                    <option value="credit_90" {{ old('payment_terms', $order->payment_terms ?? '') == 'credit_90' ? 'selected' : '' }}>آجل 90 يوم</option>
                                </select>
                                @error('payment_terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="draft" {{ old('status', $order->status ?? 'pending') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                    <option value="pending" {{ old('status', $order->status ?? 'pending') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                    <option value="approved" {{ old('status', $order->status ?? 'pending') == 'approved' ? 'selected' : '' }}>معتمد</option>
                                    <option value="received" {{ old('status', $order->status ?? 'pending') == 'received' ? 'selected' : '' }}>مستلم</option>
                                    <option value="cancelled" {{ old('status', $order->status ?? 'pending') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3">{{ old('notes', $order->notes ?? '') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- أصناف الأمر -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-list"></i> أصناف الأمر</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="items-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="35%">الصنف</th>
                                        <th width="15%">الكمية</th>
                                        <th width="15%">السعر</th>
                                        <th width="15%">الضريبة %</th>
                                        <th width="15%">الإجمالي</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="items-tbody">
                                    @if(isset($order->items) && count($order->items) > 0)
                                        @foreach($order->items as $index => $item)
                                        <tr class="item-row">
                                            <td>
                                                <select class="form-select form-select-sm item-select" name="items[{{ $index }}][product_id]" required>
                                                    <option value="">اختر الصنف</option>
                                                    <option value="{{ $item->product_id }}" selected>{{ $item->product->name ?? 'الصنف' }}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm item-quantity" 
                                                       name="items[{{ $index }}][quantity]" min="1" value="{{ $item->quantity }}" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm item-price" 
                                                       name="items[{{ $index }}][price]" min="0" step="0.01" value="{{ $item->price }}" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm item-tax" 
                                                       name="items[{{ $index }}][tax_rate]" min="0" max="100" step="0.01" value="{{ $item->tax_rate ?? 0 }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm item-total" 
                                                       readonly value="{{ number_format($item->total, 2) }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-item" {{ $index == 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr class="item-row">
                                            <td>
                                                <select class="form-select form-select-sm item-select" name="items[0][product_id]" required>
                                                    <option value="">اختر الصنف</option>
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
                                                <input type="number" class="form-control form-control-sm item-tax" 
                                                       name="items[0][tax_rate]" min="0" max="100" step="0.01" value="0">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm item-total" 
                                                       readonly value="0.00">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-item" disabled>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6">
                                            <button type="button" class="btn btn-sm btn-success" id="add-item">
                                                <i class="fas fa-plus"></i> إضافة صنف
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ملخص الأمر -->
            <div class="col-lg-4">
                <div class="card mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-calculator"></i> ملخص الأمر</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>المجموع الفرعي:</span>
                            <strong id="subtotal">{{ number_format($order->subtotal ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>الضريبة:</span>
                            <strong id="tax-total">{{ number_format($order->tax_total ?? 0, 2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="h5">الإجمالي الكلي:</span>
                            <strong class="h5 text-primary" id="grand-total">{{ number_format($order->total ?? 0, 2) }}</strong>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning text-dark">
                                <i class="fas fa-save"></i> حفظ التعديلات
                            </button>
                            <a href="{{ route('purchases.orders.show', $order->id ?? 1) }}" class="btn btn-info text-white">
                                <i class="fas fa-eye"></i> عرض الأمر
                            </a>
                            <a href="{{ route('purchases.orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </div>
                </div>

                <!-- معلومات إضافية -->
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> معلومات إضافية</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">تاريخ الإنشاء:</small><br>
                            <strong>{{ $order->created_at ?? now()->format('Y-m-d H:i') }}</strong>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">آخر تحديث:</small><br>
                            <strong>{{ $order->updated_at ?? now()->format('Y-m-d H:i') }}</strong>
                        </div>
                        @if(isset($order->created_by))
                        <div class="mb-2">
                            <small class="text-muted">أنشئ بواسطة:</small><br>
                            <strong>{{ $order->creator->name ?? 'المستخدم' }}</strong>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let itemIndex = {{ isset($order->items) ? count($order->items) : 1 }};

    // إضافة صنف جديد
    document.getElementById('add-item').addEventListener('click', function() {
        const tbody = document.getElementById('items-tbody');
        const newRow = tbody.querySelector('.item-row').cloneNode(true);
        
        // تحديث الأسماء والقيم
        newRow.querySelectorAll('input, select').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[\d+\]/, `[${itemIndex}]`));
            }
            if (input.classList.contains('item-quantity')) {
                input.value = 1;
            } else if (input.classList.contains('item-price') || input.classList.contains('item-tax')) {
                input.value = 0;
            } else if (input.classList.contains('item-total')) {
                input.value = '0.00';
            } else if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            }
        });
        
        newRow.querySelector('.remove-item').disabled = false;
        tbody.appendChild(newRow);
        itemIndex++;
        
        attachItemEvents(newRow);
    });

    // حذف صنف
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const row = e.target.closest('.item-row');
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                calculateTotals();
            }
        }
    });

    // حساب الإجماليات
    function calculateTotals() {
        let subtotal = 0;
        let taxTotal = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const taxRate = parseFloat(row.querySelector('.item-tax').value) || 0;
            
            const itemSubtotal = quantity * price;
            const itemTax = itemSubtotal * (taxRate / 100);
            const itemTotal = itemSubtotal + itemTax;
            
            row.querySelector('.item-total').value = itemTotal.toFixed(2);
            
            subtotal += itemSubtotal;
            taxTotal += itemTax;
        });

        const grandTotal = subtotal + taxTotal;

        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('tax-total').textContent = taxTotal.toFixed(2);
        document.getElementById('grand-total').textContent = grandTotal.toFixed(2);
    }

    // إرفاق أحداث الحساب لصف جديد
    function attachItemEvents(row) {
        row.querySelectorAll('.item-quantity, .item-price, .item-tax').forEach(input => {
            input.addEventListener('input', calculateTotals);
        });
    }

    // إرفاق الأحداث لجميع الصفوف الموجودة
    document.querySelectorAll('.item-row').forEach(row => {
        attachItemEvents(row);
    });

    // حساب الإجماليات عند التحميل
    calculateTotals();
</script>
@endpush
@endsection
