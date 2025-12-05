@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إنشاء إذن إخراج جديد</h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('stock_outs.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="warehouse_id" class="form-label">المخزن <span class="text-danger">*</span></label>
                                <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                                    <option value="">اختر المخزن</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="customer_id" class="form-label">العميل <span class="text-danger">*</span></label>
                                <select name="customer_id" id="customer_id" class="form-control" required>
                                    <option value="">اختر العميل</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="date" class="form-label">التاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="date" id="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">مرجع خارجي (اختياري)</label>
                                <input type="text" name="reference" id="reference" class="form-control" value="{{ old('reference') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="notes" class="form-label">ملاحظات (اختياري)</label>
                                <textarea name="notes" id="notes" class="form-control">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <hr>
                        <h4>تفاصيل الإخراج <span class="text-danger">*</span></h4>
                        <div id="details-container">
                            {{-- سيتم إضافة حقول التفاصيل هنا باستخدام JavaScript --}}
                            @if (old('details'))
                                @foreach (old('details') as $index => $detail)
                                    @include('stock_outs.partials.detail_row', ['index' => $index, 'detail' => $detail, 'items' => $items])
                                @endforeach
                            @else
                                @include('stock_outs.partials.detail_row', ['index' => 0, 'detail' => [], 'items' => $items])
                            @endif
                        </div>

                        <button type="button" class="btn btn-secondary mb-3" id="add-detail-row">إضافة صنف</button>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">حفظ وإخراج البضاعة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let detailIndex = {{ old('details') ? count(old('details')) : 1 }};

    document.getElementById('add-detail-row').addEventListener('click', function() {
        const container = document.getElementById('details-container');
        const newRow = document.createElement('div');
        newRow.classList.add('row', 'mb-3', 'detail-row');
        newRow.innerHTML = `@include('stock_outs.partials.detail_row', ['index' => 'INDEX_PLACEHOLDER', 'detail' => [], 'items' => $items])`
            .replace(/INDEX_PLACEHOLDER/g, detailIndex)
            .replace(/&quot;/g, '"'); // لإصلاح مشكلة الـ Blade في الـ JS

        container.appendChild(newRow);
        detailIndex++;
        attachEventListeners(newRow);
    });

    function attachEventListeners(row) {
        const quantityInput = row.querySelector('[name$="[quantity]"]');
        const priceInput = row.querySelector('[name$="[unit_price]"]');
        const totalInput = row.querySelector('[name$="[total_price]"]');
        const removeButton = row.querySelector('.remove-detail-row');

        const calculateTotal = () => {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            totalInput.value = (quantity * price).toFixed(2);
        };

        if (quantityInput) quantityInput.addEventListener('input', calculateTotal);
        if (priceInput) priceInput.addEventListener('input', calculateTotal);

        if (removeButton) {
            removeButton.addEventListener('click', function() {
                row.remove();
            });
        }
    }

    // إرفاق المستمعين للصفوف الموجودة عند تحميل الصفحة
    document.querySelectorAll('.detail-row').forEach(attachEventListeners);
</script>
@endpush
@endsection
