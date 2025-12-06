@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventory.warehouses.index') }}">المخازن</a></li>
                    <li class="breadcrumb-item active">تعديل المخزن</li>
                </ol>
            </nav>
            <h2 class="mb-0">
                <i class="fas fa-edit me-2"></i>
                تعديل المخزن: {{ $warehouse->name }}
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('inventory.warehouses.update', $warehouse) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="code" class="form-label">رمز المخزن <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $warehouse->code) }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="name" class="form-label">اسم المخزن <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $warehouse->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">الموقع</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $warehouse->location) }}">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="manager_id" class="form-label">المسؤول</label>
                                <select class="form-select @error('manager_id') is-invalid @enderror" id="manager_id" name="manager_id">
                                    <option value="">اختر المسؤول</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('manager_id', $warehouse->manager_id) == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('manager_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status', $warehouse->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="inactive" {{ old('status', $warehouse->status) == 'inactive' ? 'selected' : '' }}>معطل</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $warehouse->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('inventory.warehouses.index') }}" class="btn btn-secondary">
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
                            رمز المخزن يجب أن يكون فريداً
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            يمكن تعيين مسؤول لكل مخزن
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            المخازن المعطلة لا تظهر في العمليات الجديدة
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h5 class="card-title text-danger">
                        <i class="fas fa-trash me-2"></i>
                        حذف المخزن
                    </h5>
                    <p class="text-muted small">
                        حذف المخزن سيؤدي إلى حذف جميع البيانات المرتبطة به. هذا الإجراء لا يمكن التراجع عنه.
                    </p>
                    <form action="{{ route('inventory.warehouses.destroy', $warehouse) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المخزن؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-trash me-1"></i>
                            حذف المخزن
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
