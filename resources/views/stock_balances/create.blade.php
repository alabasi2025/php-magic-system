@extends('layouts.app')

@section('content')
<div class="container">
    <h1>إضافة رصيد مخزون جديد</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('stock_balances.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="warehouse_id">المخزن:</label>
            <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                <option value="">اختر المخزن</option>
                {{-- يجب تعبئة هذا من Controller --}}
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="item_id">الصنف:</label>
            <select name="item_id" id="item_id" class="form-control" required>
                <option value="">اختر الصنف</option>
                {{-- يجب تعبئة هذا من Controller --}}
                @foreach ($items as $item)
                    <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="quantity">الكمية الأولية:</label>
            <input type="number" step="0.01" name="quantity" id="quantity" class="form-control" value="{{ old('quantity', 0) }}" required min="0">
        </div>

        <div class="form-group">
            <label for="last_cost">آخر تكلفة (للحساب الأولي):</label>
            <input type="number" step="0.01" name="last_cost" id="last_cost" class="form-control" value="{{ old('last_cost', 0) }}" required min="0">
        </div>

        {{-- يتم حساب average_cost و total_value تلقائياً في Service/Model --}}
        <input type="hidden" name="average_cost" value="{{ old('last_cost', 0) }}">
        <input type="hidden" name="total_value" value="{{ old('quantity', 0) * old('last_cost', 0) }}">
        <input type="hidden" name="last_updated" value="{{ now() }}">

        <button type="submit" class="btn btn-primary">حفظ الرصيد</button>
        <a href="{{ route('stock_balances.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
