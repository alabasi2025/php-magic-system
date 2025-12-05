@extends('layouts.app')

@section('content')
<div class="container">
    <h1>تعديل رصيد المخزون: {{ $stockBalance->item->name ?? 'N/A' }} في {{ $stockBalance->warehouse->name ?? 'N/A' }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('stock_balances.update', $stockBalance) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="warehouse_id">المخزن:</label>
            <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                {{-- يجب تعبئة هذا من Controller --}}
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $stockBalance->warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="item_id">الصنف:</label>
            <select name="item_id" id="item_id" class="form-control" required>
                {{-- يجب تعبئة هذا من Controller --}}
                @foreach ($items as $item)
                    <option value="{{ $item->id }}" {{ old('item_id', $stockBalance->item_id) == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="quantity">الكمية الحالية:</label>
            {{-- ملاحظة: في نظام حقيقي، لا يتم تعديل الكمية يدوياً بل من خلال حركات الإدخال/الإخراج --}}
            <input type="number" step="0.01" name="quantity" id="quantity" class="form-control" value="{{ old('quantity', $stockBalance->quantity) }}" required min="0">
        </div>

        <div class="form-group">
            <label for="last_cost">آخر تكلفة:</label>
            <input type="number" step="0.01" name="last_cost" id="last_cost" class="form-control" value="{{ old('last_cost', $stockBalance->last_cost) }}" required min="0">
        </div>

        <div class="form-group">
            <label for="average_cost">متوسط التكلفة (للعرض فقط):</label>
            <input type="number" step="0.01" id="average_cost" class="form-control" value="{{ number_format($stockBalance->average_cost, 2) }}" disabled>
        </div>

        <div class="form-group">
            <label for="total_value">القيمة الإجمالية (للعرض فقط):</label>
            <input type="number" step="0.01" id="total_value" class="form-control" value="{{ number_format($stockBalance->total_value, 2) }}" disabled>
        </div>

        <button type="submit" class="btn btn-primary">تحديث الرصيد</button>
        <a href="{{ route('stock_balances.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
