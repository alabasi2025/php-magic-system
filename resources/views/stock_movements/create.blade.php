@extends('layouts.app')

@section('content')
<div class="container">
    <h1>تسجيل حركة مخزون جديدة</h1>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('stock_movements.store') }}">
        @csrf

        <div class="row">
            {{-- المخزن --}}
            <div class="col-md-6 mb-3">
                <label for="warehouse_id" class="form-label">المخزن <span class="text-danger">*</span></label>
                <select name="warehouse_id" id="warehouse_id" class="form-control @error('warehouse_id') is-invalid @enderror" required>
                    <option value="">اختر المخزن</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
                @error('warehouse_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- الصنف --}}
            <div class="col-md-6 mb-3">
                <label for="item_id" class="form-label">الصنف <span class="text-danger">*</span></label>
                <select name="item_id" id="item_id" class="form-control @error('item_id') is-invalid @enderror" required>
                    <option value="">اختر الصنف</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                </select>
                @error('item_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            {{-- نوع الحركة --}}
            <div class="col-md-4 mb-3">
                <label for="movement_type" class="form-label">نوع الحركة <span class="text-danger">*</span></label>
                <select name="movement_type" id="movement_type" class="form-control @error('movement_type') is-invalid @enderror" required>
                    <option value="">اختر النوع</option>
                    @foreach (['in' => 'دخول (إضافة)', 'out' => 'خروج (صرف)', 'adjustment' => 'تسوية', 'transfer' => 'نقل'] as $key => $value)
                        <option value="{{ $key }}" {{ old('movement_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                @error('movement_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- الكمية --}}
            <div class="col-md-4 mb-3">
                <label for="quantity" class="form-label">الكمية <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0.01" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required>
                @error('quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- سعر الوحدة (اختياري) --}}
            <div class="col-md-4 mb-3">
                <label for="unit_price" class="form-label">سعر الوحدة (اختياري)</label>
                <input type="number" step="0.01" min="0" name="unit_price" id="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price') }}">
                @error('unit_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            {{-- نوع المرجع --}}
            <div class="col-md-6 mb-3">
                <label for="reference_type" class="form-label">نوع المستند المرجعي <span class="text-danger">*</span></label>
                <input type="text" name="reference_type" id="reference_type" class="form-control @error('reference_type') is-invalid @enderror" value="{{ old('reference_type', 'ManualAdjustment') }}" required>
                <small class="form-text text-muted">مثال: PurchaseOrder, SalesInvoice, ManualAdjustment</small>
                @error('reference_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- معرف المرجع --}}
            <div class="col-md-6 mb-3">
                <label for="reference_id" class="form-label">معرف المستند المرجعي <span class="text-danger">*</span></label>
                <input type="number" name="reference_id" id="reference_id" class="form-control @error('reference_id') is-invalid @enderror" value="{{ old('reference_id', 0) }}" required>
                <small class="form-text text-muted">معرف السجل في الجدول المرجعي.</small>
                @error('reference_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-success">تسجيل الحركة</button>
        <a href="{{ route('stock_movements.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
