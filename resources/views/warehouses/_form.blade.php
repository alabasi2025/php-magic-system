{{-- نموذج جزئي لإضافة وتعديل المخازن --}}
{{-- يتوقع متغير $warehouse (للتعديل) أو نموذج جديد (للإضافة) ومتغير $managers --}}

@php
    $isEdit = isset($warehouse) && $warehouse->exists;
    $action = $isEdit ? route('warehouses.update', $warehouse) : route('warehouses.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @method($method)

    {{-- رسائل الأخطاء العامة --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        {{-- حقل رمز المخزن --}}
        <div class="col-md-6 mb-3">
            <label for="code" class="form-label">رمز المخزن <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $warehouse->code ?? '') }}" required>
            @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- حقل اسم المخزن --}}
        <div class="col-md-6 mb-3">
            <label for="name" class="form-label">اسم المخزن <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $warehouse->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row">
        {{-- حقل الموقع --}}
        <div class="col-md-6 mb-3">
            <label for="location" class="form-label">الموقع</label>
            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $warehouse->location ?? '') }}">
            @error('location')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- حقل الهاتف --}}
        <div class="col-md-6 mb-3">
            <label for="phone" class="form-label">رقم الهاتف</label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $warehouse->phone ?? '') }}">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- حقل العنوان --}}
    <div class="mb-3">
        <label for="address" class="form-label">العنوان التفصيلي</label>
        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $warehouse->address ?? '') }}</textarea>
        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="row">
        {{-- حقل البريد الإلكتروني --}}
        <div class="col-md-6 mb-3">
            <label for="email" class="form-label">البريد الإلكتروني</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $warehouse->email ?? '') }}">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- حقل مدير المخزن --}}
        <div class="col-md-6 mb-3">
            <label for="manager_id" class="form-label">مدير المخزن</label>
            <select class="form-select @error('manager_id') is-invalid @enderror" id="manager_id" name="manager_id">
                <option value="">-- اختر مديراً --</option>
                @foreach ($managers as $manager)
                    <option value="{{ $manager->id }}" {{ old('manager_id', $warehouse->manager_id ?? '') == $manager->id ? 'selected' : '' }}>
                        {{ $manager->name }}
                    </option>
                @endforeach
            </select>
            @error('manager_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row">
        {{-- حقل السعة التخزينية --}}
        <div class="col-md-6 mb-3">
            <label for="capacity" class="form-label">السعة التخزينية</label>
            <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity', $warehouse->capacity ?? '') }}" min="0">
            @error('capacity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- حقل قيمة المخزون الحالية (للعرض فقط في الإضافة، يمكن تعديله في التحديث إذا لزم الأمر) --}}
        <div class="col-md-6 mb-3">
            <label for="current_stock_value" class="form-label">القيمة الحالية للمخزون</label>
            <input type="number" step="0.01" class="form-control @error('current_stock_value') is-invalid @enderror" id="current_stock_value" name="current_stock_value" value="{{ old('current_stock_value', $warehouse->current_stock_value ?? 0.00) }}" min="0" {{ $isEdit ? '' : 'readonly' }}>
            @error('current_stock_value')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- حقل حالة التفعيل (يظهر فقط في التعديل) --}}
    @if ($isEdit)
        <div class="mb-3 form-check">
            <input type="hidden" name="is_active" value="0"> {{-- لضمان إرسال القيمة 0 إذا لم يتم تحديد المربع --}}
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $warehouse->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">المخزن نشط</label>
        </div>
    @endif

    <button type="submit" class="btn btn-success">{{ $isEdit ? 'حفظ التعديلات' : 'إنشاء المخزن' }}</button>
    <a href="{{ route('warehouses.index') }}" class="btn btn-secondary">إلغاء</a>
</form>
