{{-- نموذج جزئي يستخدم في إنشاء وتعديل الأصناف --}}
<div class="row">
    {{-- حقل رمز الصنف --}}
    <div class="col-md-4 mb-3">
        <label for="code" class="form-label">رمز الصنف <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $item->code ?? '') }}" required>
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- حقل الباركود --}}
    <div class="col-md-4 mb-3">
        <label for="barcode" class="form-label">الباركود</label>
        <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode" name="barcode" value="{{ old('barcode', $item->barcode ?? '') }}">
        @error('barcode')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- حقل اسم الصنف --}}
    <div class="col-md-4 mb-3">
        <label for="name" class="form-label">اسم الصنف <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $item->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    {{-- حقل الفئة --}}
    <div class="col-md-6 mb-3">
        <label for="category_id" class="form-label">الفئة <span class="text-danger">*</span></label>
        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
            <option value="">اختر الفئة</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $item->category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- حقل الوحدة --}}
    <div class="col-md-6 mb-3">
        <label for="unit_id" class="form-label">وحدة القياس <span class="text-danger">*</span></label>
        <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
            <option value="">اختر الوحدة</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}" {{ old('unit_id', $item->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                    {{ $unit->name }}
                </option>
            @endforeach
        </select>
        @error('unit_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    {{-- حقل سعر التكلفة --}}
    <div class="col-md-6 mb-3">
        <label for="cost_price" class="form-label">سعر التكلفة <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror" id="cost_price" name="cost_price" value="{{ old('cost_price', $item->cost_price ?? '') }}" required min="0">
        @error('cost_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- حقل سعر البيع --}}
    <div class="col-md-6 mb-3">
        <label for="selling_price" class="form-label">سعر البيع <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ old('selling_price', $item->selling_price ?? '') }}" required min="0">
        @error('selling_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    {{-- حقل الحد الأدنى للمخزون --}}
    <div class="col-md-4 mb-3">
        <label for="min_stock" class="form-label">الحد الأدنى للمخزون</label>
        <input type="number" class="form-control @error('min_stock') is-invalid @enderror" id="min_stock" name="min_stock" value="{{ old('min_stock', $item->min_stock ?? 0) }}" min="0">
        @error('min_stock')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- حقل الحد الأقصى للمخزون --}}
    <div class="col-md-4 mb-3">
        <label for="max_stock" class="form-label">الحد الأقصى للمخزون</label>
        <input type="number" class="form-control @error('max_stock') is-invalid @enderror" id="max_stock" name="max_stock" value="{{ old('max_stock', $item->max_stock ?? 0) }}" min="0">
        @error('max_stock')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- حقل مستوى إعادة الطلب --}}
    <div class="col-md-4 mb-3">
        <label for="reorder_level" class="form-label">مستوى إعادة الطلب</label>
        <input type="number" class="form-control @error('reorder_level') is-invalid @enderror" id="reorder_level" name="reorder_level" value="{{ old('reorder_level', $item->reorder_level ?? 0) }}" min="0">
        @error('reorder_level')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    {{-- حقل الوصف --}}
    <div class="col-md-6 mb-3">
        <label for="description" class="form-label">الوصف</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $item->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- حقل الصورة --}}
    <div class="col-md-6 mb-3">
        <label for="image" class="form-label">صورة الصنف</label>
        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if (isset($item) && $item->image)
            <div class="mt-2">
                <img src="{{ asset('storage/' . $item->image) }}" alt="صورة الصنف" style="max-width: 100px; height: auto;">
                <p class="text-muted">الصورة الحالية</p>
            </div>
        @endif
    </div>
</div>

{{-- حقل حالة التفعيل --}}
<div class="mb-3 form-check">
    <input type="hidden" name="is_active" value="0"> {{-- قيمة افتراضية لـ is_active --}}
    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $item->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">مفعل</label>
    @error('is_active')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<button type="submit" class="btn btn-success">{{ isset($item) ? 'تحديث الصنف' : 'حفظ الصنف' }}</button>
<a href="{{ route('items.index') }}" class="btn btn-secondary">إلغاء</a>
