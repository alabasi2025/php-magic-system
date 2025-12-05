@extends('layouts.app')

@section('title', 'تعديل الوحدة: ' . $unit->name)

@section('content')
<div class="container">
    <h1>تعديل الوحدة: {{ $unit->name }}</h1>
    <form action="{{ route('inventory.units.update', $unit) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">اسم الوحدة</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $unit->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="symbol" class="form-label">رمز الوحدة</label>
            <input type="text" class="form-control @error('symbol') is-invalid @enderror" id="symbol" name="symbol" value="{{ old('symbol', $unit->symbol) }}" required>
            @error('symbol')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_base_unit" name="is_base_unit" value="1" {{ old('is_base_unit', $unit->is_base_unit) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_base_unit">هل هي وحدة أساسية؟ (معامل التحويل سيكون 1.0)</label>
        </div>

        <div class="mb-3">
            <label for="base_unit_id" class="form-label">الوحدة الأساسية (إذا لم تكن أساسية)</label>
            <select class="form-select @error('base_unit_id') is-invalid @enderror" id="base_unit_id" name="base_unit_id">
                <option value="">-- اختر وحدة أساسية --</option>
                @foreach ($baseUnits as $baseUnit)
                    <option value="{{ $baseUnit->id }}" {{ old('base_unit_id', $unit->base_unit_id) == $baseUnit->id ? 'selected' : '' }}>{{ $baseUnit->name }} ({{ $baseUnit->symbol }})</option>
                @endforeach
            </select>
            @error('base_unit_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="conversion_factor" class="form-label">معامل التحويل إلى الوحدة الأساسية</label>
            <input type="number" step="0.0001" class="form-control @error('conversion_factor') is-invalid @enderror" id="conversion_factor" name="conversion_factor" value="{{ old('conversion_factor', $unit->conversion_factor) }}" required>
            @error('conversion_factor')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">مثال: إذا كانت الوحدة هي "جرام" والوحدة الأساسية هي "كيلوغرام"، فإن معامل التحويل هو 0.001.</small>
        </div>

        <button type="submit" class="btn btn-success">تحديث الوحدة</button>
        <a href="{{ route('inventory.units.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
