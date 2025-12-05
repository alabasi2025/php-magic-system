@extends('layouts.app')

@section('title', 'تحويل الوحدات')

@section('content')
<div class="container">
    <h1>تحويل الوحدات</h1>
    <a href="{{ route('inventory.units.index') }}" class="btn btn-secondary mb-3">العودة إلى إدارة الوحدات</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('inventory.units.convert') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="quantity" class="form-label">الكمية</label>
                <input type="number" step="0.0001" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" required>
                @error('quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="from_unit_id" class="form-label">من وحدة</label>
                <select class="form-select @error('from_unit_id') is-invalid @enderror" id="from_unit_id" name="from_unit_id" required>
                    <option value="">-- اختر وحدة --</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('from_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->symbol }})</option>
                    @endforeach
                </select>
                @error('from_unit_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="to_unit_id" class="form-label">إلى وحدة</label>
                <select class="form-select @error('to_unit_id') is-invalid @enderror" id="to_unit_id" name="to_unit_id" required>
                    <option value="">-- اختر وحدة --</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('to_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->symbol }})</option>
                    @endforeach
                </select>
                @error('to_unit_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-success">تحويل</button>
    </form>

    @if (session('converted_quantity'))
        <div class="mt-4 p-3 border rounded bg-light">
            <h4>نتيجة التحويل:</h4>
            <p class="lead">{{ session('converted_quantity') }}</p>
        </div>
    @endif
</div>
@endsection
