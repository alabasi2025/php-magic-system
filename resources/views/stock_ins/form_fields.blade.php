{{-- هذا الملف يستخدم في كل من create.blade.php و edit.blade.php --}}
@php
    // تحديد النموذج إذا كان موجوداً (في حالة التعديل)
    $stockIn = $stockIn ?? null;
    $details = old('details', $stockIn ? $stockIn->details->toArray() : [[]]);
@endphp

<div class="row">
    {{-- حقل المخزن --}}
    <div class="col-md-4 mb-3">
        <label for="warehouse_id" class="form-label">المخزن <span class="text-danger">*</span></label>
        <select class="form-control" id="warehouse_id" name="warehouse_id" required>
            <option value="">اختر المخزن</option>
            @foreach ($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $stockIn->warehouse_id ?? '') == $warehouse->id ? 'selected' : '' }}>
                    {{ $warehouse->name }}
                </option>
            @endforeach
        </select>
        @error('warehouse_id')<div class="text-danger">{{ $message }}</div>@enderror
    </div>

    {{-- حقل المورد --}}
    <div class="col-md-4 mb-3">
        <label for="supplier_id" class="form-label">المورد <span class="text-danger">*</span></label>
        <select class="form-control" id="supplier_id" name="supplier_id" required>
            <option value="">اختر المورد</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ old('supplier_id', $stockIn->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->name }}
                </option>
            @endforeach
        </select>
        @error('supplier_id')<div class="text-danger">{{ $message }}</div>@enderror
    </div>

    {{-- حقل التاريخ --}}
    <div class="col-md-4 mb-3">
        <label for="date" class="form-label">تاريخ الإدخال <span class="text-danger">*</span></label>
        <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $stockIn->date->format('Y-m-d') ?? date('Y-m-d')) }}" required>
        @error('date')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    {{-- حقل المرجع --}}
    <div class="col-md-6 mb-3">
        <label for="reference" class="form-label">المرجع الخارجي (رقم الفاتورة)</label>
        <input type="text" class="form-control" id="reference" name="reference" value="{{ old('reference', $stockIn->reference ?? '') }}">
        @error('reference')<div class="text-danger">{{ $message }}</div>@enderror
    </div>

    {{-- حقل الملاحظات --}}
    <div class="col-md-6 mb-3">
        <label for="notes" class="form-label">ملاحظات</label>
        <textarea class="form-control" id="notes" name="notes" rows="1">{{ old('notes', $stockIn->notes ?? '') }}</textarea>
        @error('notes')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
</div>

<hr>
<h3>تفاصيل الأصناف <span class="text-danger">*</span></h3>
@error('details')<div class="text-danger mb-3">يجب إضافة صنف واحد على الأقل.</div>@enderror

<div id="details-container">
    @foreach ($details as $index => $detail)
        <div class="row detail-row mb-3 border p-3 rounded" data-index="{{ $index }}">
            <input type="hidden" name="details[{{ $index }}][id]" value="{{ $detail['id'] ?? '' }}">
            
            {{-- حقل الصنف --}}
            <div class="col-md-4">
                <label for="item_id_{{ $index }}" class="form-label">الصنف</label>
                <select class="form-control item-select" id="item_id_{{ $index }}" name="details[{{ $index }}][item_id]" required>
                    <option value="">اختر الصنف</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" {{ old("details.{$index}.item_id", $detail['item_id'] ?? '') == $item->id ? 'selected' : '' }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
                @error("details.{$index}.item_id")<div class="text-danger">{{ $message }}</div>@enderror
            </div>

            {{-- حقل الكمية --}}
            <div class="col-md-2">
                <label for="quantity_{{ $index }}" class="form-label">الكمية</label>
                <input type="number" step="0.01" min="0.01" class="form-control quantity-input" id="quantity_{{ $index }}" name="details[{{ $index }}][quantity]" value="{{ old("details.{$index}.quantity", $detail['quantity'] ?? '') }}" required>
                @error("details.{$index}.quantity")<div class="text-danger">{{ $message }}</div>@enderror
            </div>

            {{-- حقل سعر الوحدة --}}
            <div class="col-md-3">
                <label for="unit_price_{{ $index }}" class="form-label">سعر الوحدة</label>
                <input type="number" step="0.01" min="0" class="form-control unit-price-input" id="unit_price_{{ $index }}" name="details[{{ $index }}][unit_price]" value="{{ old("details.{$index}.unit_price", $detail['unit_price'] ?? '') }}" required>
                @error("details.{$index}.unit_price")<div class="text-danger">{{ $message }}</div>@enderror
            </div>

            {{-- حقل الإجمالي --}}
            <div class="col-md-2">
                <label class="form-label">الإجمالي</label>
                <p class="form-control-plaintext total-price-display">{{ number_format(($detail['quantity'] ?? 0) * ($detail['unit_price'] ?? 0), 2) }}</p>
            </div>

            {{-- زر الحذف --}}
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-detail-row w-100">حذف</button>
            </div>
        </div>
    @endforeach
</div>

<button type="button" id="add-detail-row" class="btn btn-secondary mt-3">إضافة صنف</button>

<div class="text-end mt-3">
    <h4>الإجمالي الكلي: <span id="grand-total">{{ number_format($stockIn->total_amount ?? 0, 2) }}</span></h4>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let detailIndex = {{ count($details) }};
        const detailsContainer = document.getElementById('details-container');
        const addButton = document.getElementById('add-detail-row');
        const grandTotalDisplay = document.getElementById('grand-total');

        // قالب لصف جديد
        const detailRowTemplate = `
            <div class="row detail-row mb-3 border p-3 rounded" data-index="__INDEX__">
                <input type="hidden" name="details[__INDEX__][id]" value="">
                
                <div class="col-md-4">
                    <label for="item_id___INDEX__" class="form-label">الصنف</label>
                    <select class="form-control item-select" id="item_id___INDEX__" name="details[__INDEX__][item_id]" required>
                        <option value="">اختر الصنف</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="quantity___INDEX__" class="form-label">الكمية</label>
                    <input type="number" step="0.01" min="0.01" class="form-control quantity-input" id="quantity___INDEX__" name="details[__INDEX__][quantity]" value="" required>
                </div>

                <div class="col-md-3">
                    <label for="unit_price___INDEX__" class="form-label">سعر الوحدة</label>
                    <input type="number" step="0.01" min="0" class="form-control unit-price-input" id="unit_price___INDEX__" name="details[__INDEX__][unit_price]" value="" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">الإجمالي</label>
                    <p class="form-control-plaintext total-price-display">0.00</p>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-detail-row w-100">حذف</button>
                </div>
            </div>
        `;

        // دالة لإضافة صف جديد
        addButton.addEventListener('click', function () {
            const newRowHtml = detailRowTemplate.replace(/__INDEX__/g, detailIndex);
            detailsContainer.insertAdjacentHTML('beforeend', newRowHtml);
            detailIndex++;
            attachEventListeners();
            updateGrandTotal();
        });

        // دالة لحساب الإجمالي للصف الواحد
        function calculateRowTotal(row) {
            const quantityInput = row.querySelector('.quantity-input');
            const priceInput = row.querySelector('.unit-price-input');
            const totalDisplay = row.querySelector('.total-price-display');

            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = quantity * price;

            totalDisplay.textContent = total.toFixed(2);
            return total;
        }

        // دالة لحساب الإجمالي الكلي
        function updateGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.detail-row').forEach(row => {
                grandTotal += calculateRowTotal(row);
            });
            grandTotalDisplay.textContent = grandTotal.toFixed(2);
        }

        // دالة لإرفاق مستمعي الأحداث
        function attachEventListeners() {
            // إرفاق مستمعي الأحداث لجميع حقول الكمية والسعر
            detailsContainer.querySelectorAll('.quantity-input, .unit-price-input').forEach(input => {
                input.removeEventListener('input', updateGrandTotal); // تجنب التكرار
                input.addEventListener('input', updateGrandTotal);
            });

            // إرفاق مستمعي الأحداث لأزرار الحذف
            detailsContainer.querySelectorAll('.remove-detail-row').forEach(button => {
                button.removeEventListener('click', removeDetailRow); // تجنب التكرار
                button.addEventListener('click', removeDetailRow);
            });
        }

        // دالة لحذف صف التفاصيل
        function removeDetailRow(event) {
            const row = event.target.closest('.detail-row');
            if (detailsContainer.querySelectorAll('.detail-row').length > 1) {
                row.remove();
                updateGrandTotal();
            } else {
                alert('يجب أن يحتوي إذن الإدخال على صنف واحد على الأقل.');
            }
        }

        // إرفاق المستمعين عند تحميل الصفحة لأول مرة
        attachEventListeners();
        updateGrandTotal(); // حساب الإجمالي الأولي
    });
</script>
@endpush
