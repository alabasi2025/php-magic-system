@extends('layouts.app')

@section('title', 'إضافة فئة جديدة')

@section('content')
<div class="container">
    <h1>إضافة فئة جديدة</h1>
    <form action="{{ route('inventory.categories.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">اسم الفئة</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="parent_id" class="form-label">الفئة الأب (اختياري)</label>
            <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                <option value="">-- اختر فئة أب --</option>
                @foreach ($parentCategories as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                @endforeach
            </select>
            @error('parent_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">الوصف (اختياري)</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">مفعلة</label>
        </div>

        <button type="submit" class="btn btn-success">حفظ الفئة</button>
        <a href="{{ route('inventory.categories.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
