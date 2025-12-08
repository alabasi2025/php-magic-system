@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('inventory.dashboard') }}">المخزون</a></li>
                <li class="breadcrumb-item"><a href="{{ route('inventory.warehouse-groups.index') }}">مجموعات المخازن</a></li>
                <li class="breadcrumb-item active">تعديل: {{ $warehouseGroup->name }}</li>
            </ol>
        </nav>
        <h2 class="mb-1">
            <i class="fas fa-edit text-primary me-2"></i>
            تعديل مجموعة المخازن
        </h2>
        <p class="text-muted mb-0">تحديث بيانات المجموعة: <strong>{{ $warehouseGroup->name }}</strong></p>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">
                <i class="fas fa-exclamation-triangle me-2"></i>
                يرجى تصحيح الأخطاء التالية:
            </h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 modern-card">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات المجموعة
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('inventory.warehouse-groups.update', $warehouseGroup) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Code -->
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label required">كود المجموعة</label>
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code', $warehouseGroup->code) }}" 
                                       placeholder="مثال: WG001"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">كود فريد لتمييز المجموعة</small>
                            </div>

                            <!-- Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label required">اسم المجموعة</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $warehouseGroup->name) }}" 
                                       placeholder="مثال: مخازن المواد الخام"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Account -->
                        <div class="mb-3">
                            <label for="account_id" class="form-label">
                                <i class="fas fa-link text-info me-1"></i>
                                الحساب المحاسبي المرتبط
                            </label>
                            <select class="form-select @error('account_id') is-invalid @enderror" 
                                    id="account_id" 
                                    name="account_id">
                                <option value="">-- اختر الحساب المحاسبي --</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" 
                                            {{ old('account_id', $warehouseGroup->account_id) == $account->id ? 'selected' : '' }}>
                                        {{ $account->code }} - {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('account_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                سيتم ربط جميع المخازن في هذه المجموعة بالحساب المحاسبي المحدد
                            </small>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label required">الحالة</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="active" {{ old('status', $warehouseGroup->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ old('status', $warehouseGroup->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
                                      placeholder="وصف تفصيلي للمجموعة...">{{ old('description', $warehouseGroup->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>
                                حفظ التعديلات
                            </button>
                            <a href="{{ route('inventory.warehouse-groups.index') }}" class="btn btn-secondary px-4">
                                <i class="fas fa-times me-2"></i>
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 modern-card mb-3">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات إضافية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">تاريخ الإنشاء</small>
                        <strong>{{ $warehouseGroup->created_at->format('Y-m-d H:i') }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">آخر تحديث</small>
                        <strong>{{ $warehouseGroup->updated_at->format('Y-m-d H:i') }}</strong>
                    </div>
                    <div>
                        <small class="text-muted d-block">عدد المخازن</small>
                        <strong class="text-primary">{{ $warehouseGroup->warehouses->count() }} مخزن</strong>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 modern-card">
                <div class="card-header bg-gradient-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        تنبيه
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        تغيير الحساب المحاسبي سيؤثر على جميع المخازن المرتبطة بهذه المجموعة.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.modern-card {
    border-radius: 10px;
    overflow: hidden;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #00b4db 0%, #0083b0 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.form-label.required::after {
    content: " *";
    color: #dc3545;
}

.form-control:focus,
.form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>
@endsection
