@extends('layouts.app')

@section('content')
<div class="container">
    <h1>إنشاء عملية جرد جديدة</h1>

    <form action="{{ route('stock_counts.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="warehouse_id" class="form-label">المخزن</label>
            <select class="form-control @error('warehouse_id') is-invalid @enderror" id="warehouse_id" name="warehouse_id" required>
                <option value="">اختر المخزن</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                @endforeach
            </select>
            @error('warehouse_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">تاريخ الجرد</label>
            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
            @error('date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">ملاحظات</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes">{{ old('notes') }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <h2>الأصناف المراد جردها</h2>
        <table class="table table-bordered" id="items-table">
            <thead>
                <tr>
                    <th>الصنف</th>
                    <th>الكمية في النظام (افتراضية)</th>
                    <th>ملاحظات الصنف</th>
                    <th><button type="button" class="btn btn-sm btn-success" id="add-item">+</button></th>
                </tr>
            </thead>
            <tbody>
                <!-- سيتم إضافة الصفوف هنا بواسطة JavaScript -->
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">إنشاء الجرد</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemsTableBody = document.getElementById('items-table').querySelector('tbody');
        const addItemButton = document.getElementById('add-item');
        const items = @json($items); // قائمة الأصناف من المتحكم

        let itemIndex = 0;

        function createItemRow(item = null) {
            const row = itemsTableBody.insertRow();
            row.innerHTML = `
                <td>
                    <select class="form-control" name="items[${itemIndex}][item_id]" required>
                        <option value="">اختر صنف</option>
                        ${items.map(i => `<option value="${i.id}" ${item && item.item_id == i.id ? 'selected' : ''}>${i.name}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control" name="items[${itemIndex}][system_quantity]" value="${item ? item.system_quantity : 0}" required>
                </td>
                <td>
                    <input type="text" class="form-control" name="items[${itemIndex}][notes]" value="${item ? item.notes : ''}">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-item">حذف</button>
                </td>
            `;
            itemIndex++;
        }

        addItemButton.addEventListener('click', () => createItemRow());

        itemsTableBody.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item')) {
                e.target.closest('tr').remove();
            }
        });

        // إضافة صف افتراضي عند التحميل
        if (itemsTableBody.rows.length === 0) {
            createItemRow();
        }
    });
</script>
@endsection
