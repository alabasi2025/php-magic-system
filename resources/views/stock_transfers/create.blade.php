@extends('layouts.app')

@section('content')
<div class="container">
    <h1>إنشاء طلب تحويل مخزون جديد</h1>

    <form action="{{ route('stock_transfers.store') }}" method="POST">
        @csrf

        <div class="card mb-4">
            <div class="card-header">بيانات التحويل الأساسية</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="from_warehouse_id" class="form-label">المخزن المصدر (من)</label>
                        <select class="form-control @error('from_warehouse_id') is-invalid @enderror" id="from_warehouse_id" name="from_warehouse_id" required>
                            <option value="">اختر المخزن</option>
                            {{-- يجب تمرير $warehouses من المتحكم --}}
                            {{-- @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('from_warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach --}}
                        </select>
                        @error('from_warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="to_warehouse_id" class="form-label">المخزن المستقبل (إلى)</label>
                        <select class="form-control @error('to_warehouse_id') is-invalid @enderror" id="to_warehouse_id" name="to_warehouse_id" required>
                            <option value="">اختر المخزن</option>
                            {{-- @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('to_warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach --}}
                        </select>
                        @error('to_warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="date" class="form-label">تاريخ التحويل</label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">ملاحظات</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">تفاصيل المواد المحولة</div>
            <div class="card-body">
                {{-- هذا الجزء يتطلب استخدام JavaScript لإضافة وحذف صفوف التفاصيل --}}
                <table class="table table-sm" id="details-table">
                    <thead>
                        <tr>
                            <th>المادة</th>
                            <th>الكمية</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- مثال لصف واحد (يجب تكراره باستخدام JS) --}}
                        <tr>
                            <td>
                                <select name="details[0][item_id]" class="form-control item-select" required>
                                    <option value="">اختر المادة</option>
                                    {{-- يجب تمرير $items من المتحكم --}}
                                    {{-- @foreach ($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach --}}
                                </select>
                                @error('details.0.item_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </td>
                            <td>
                                <input type="number" name="details[0][quantity]" class="form-control" step="0.01" min="0.01" required>
                                @error('details.0.quantity') <div class="text-danger">{{ $message }}</div> @enderror
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-detail">حذف</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-success btn-sm" id="add-detail">إضافة مادة</button>
                @error('details') <div class="text-danger mt-2">{{ $message }}</div> @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary">حفظ طلب التحويل</button>
        <a href="{{ route('stock_transfers.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>

{{-- مثال بسيط لـ JavaScript لإدارة صفوف التفاصيل --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let detailIndex = 1;
        const detailsTableBody = document.querySelector('#details-table tbody');
        const addButton = document.querySelector('#add-detail');

        addButton.addEventListener('click', function () {
            const newRow = detailsTableBody.querySelector('tr').cloneNode(true);
            
            // تحديث أسماء الحقول
            newRow.querySelectorAll('input, select').forEach(function(element) {
                element.name = element.name.replace(/details\[\d+\]/, `details[${detailIndex}]`);
                element.value = ''; // مسح القيمة
                element.classList.remove('is-invalid'); // إزالة حالة الخطأ
            });

            // إضافة مستمع حدث الحذف للصف الجديد
            newRow.querySelector('.remove-detail').addEventListener('click', function() {
                newRow.remove();
            });

            detailsTableBody.appendChild(newRow);
            detailIndex++;
        });

        // إضافة مستمع حدث الحذف للصفوف الموجودة مسبقاً (إذا كان هناك بيانات قديمة)
        detailsTableBody.querySelectorAll('.remove-detail').forEach(function(button) {
            button.addEventListener('click', function() {
                button.closest('tr').remove();
            });
        });
    });
</script>
@endpush
@endsection
