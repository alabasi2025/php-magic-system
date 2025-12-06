@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventory.items.index') }}">الأصناف</a></li>
                    <li class="breadcrumb-item active">تعديل الصنف</li>
                </ol>
            </nav>
            <h2 class="mb-0">
                <i class="fas fa-edit me-2"></i>
                تعديل الصنف: {{ $item->name }}
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('inventory.items.update', $item) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="sku" class="form-label">رمز الصنف (SKU) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $item->sku) }}" required>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="barcode" class="form-label">الباركود</label>
                                <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode" name="barcode" value="{{ old('barcode', $item->barcode) }}">
                                @error('barcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">اسم الصنف <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $item->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $item->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="unit_id" class="form-label">الوحدة <span class="text-danger">*</span></label>
                                <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                                    <option value="">اختر الوحدة</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id', $item->unit_id) == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="unit_price" class="form-label">سعر الوحدة <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('unit_price') is-invalid @enderror" id="unit_price" name="unit_price" value="{{ old('unit_price', $item->unit_price) }}" required>
                                @error('unit_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="min_stock" class="form-label">الحد الأدنى للمخزون</label>
                                <input type="number" step="0.01" class="form-control @error('min_stock') is-invalid @enderror" id="min_stock" name="min_stock" value="{{ old('min_stock', $item->min_stock) }}">
                                @error('min_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="max_stock" class="form-label">الحد الأقصى للمخزون</label>
                                <input type="number" step="0.01" class="form-control @error('max_stock') is-invalid @enderror" id="max_stock" name="max_stock" value="{{ old('max_stock', $item->max_stock) }}">
                                @error('max_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $item->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ old('status', $item->status) == 'inactive' ? 'selected' : '' }}>معطل</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">صورة الصنف</label>
                            @if($item->image_path)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->name }}" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">اترك الحقل فارغاً إذا لم ترغب في تغيير الصورة</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('inventory.items.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right me-1"></i>
                                رجوع
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                تحديث
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات مهمة
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            رمز الصنف (SKU) يجب أن يكون فريداً
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            الحد الأدنى يستخدم للتنبيهات
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            الأصناف المعطلة لا تظهر في العمليات الجديدة
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h5 class="card-title text-danger">
                        <i class="fas fa-trash me-2"></i>
                        حذف الصنف
                    </h5>
                    <p class="text-muted small">
                        حذف الصنف سيؤدي إلى حذف جميع البيانات المرتبطة به. هذا الإجراء لا يمكن التراجع عنه.
                    </p>
                    <form action="{{ route('inventory.items.destroy', $item) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الصنف؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-trash me-1"></i>
                            حذف الصنف
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
