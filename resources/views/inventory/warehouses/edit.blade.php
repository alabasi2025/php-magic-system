@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/inventory-enhanced.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header محسن -->
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); padding: 15px; border-radius: 10px;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('inventory.warehouses.index') }}" style="color: #667eea; text-decoration: none; font-weight: 600;">
                            <i class="fas fa-warehouse me-1"></i>
                            المخازن
                        </a>
                    </li>
                    <li class="breadcrumb-item active" style="color: #764ba2; font-weight: 600;">
                        تعديل المخزن
                    </li>
                </ol>
            </nav>
            <div class="d-flex align-items-center">
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-left: 15px;">
                    <i class="fas fa-edit fa-2x text-white"></i>
                </div>
                <div>
                    <h2 class="mb-1">تعديل المخزن: {{ $warehouse->name }}</h2>
                    <p class="text-muted mb-0">تحديث بيانات المخزن</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="stat-card fade-in">
                <form action="{{ route('inventory.warehouses.update', $warehouse) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label>
                                    <i class="fas fa-barcode me-2 text-primary"></i>
                                    رمز المخزن 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control-enhanced @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code', $warehouse->code) }}" 
                                       placeholder="مثال: WH001"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label>
                                    <i class="fas fa-warehouse me-2 text-success"></i>
                                    اسم المخزن 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control-enhanced @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $warehouse->name) }}" 
                                       placeholder="مثال: المخزن الرئيسي"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group-enhanced">
                        <label>
                            <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                            الموقع
                        </label>
                        <input type="text" 
                               class="form-control-enhanced @error('location') is-invalid @enderror" 
                               id="location" 
                               name="location" 
                               value="{{ old('location', $warehouse->location) }}"
                               placeholder="مثال: الرياض - حي الملز">
                        @error('location')
                            <div class="invalid-feedback d-block">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label>
                                    <i class="fas fa-user-tie me-2 text-info"></i>
                                    المسؤول
                                </label>
                                <select class="form-control-enhanced @error('manager_id') is-invalid @enderror" 
                                        id="manager_id" 
                                        name="manager_id">
                                    <option value="">اختر المسؤول</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('manager_id', $warehouse->manager_id) == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('manager_id')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label>
                                    <i class="fas fa-toggle-on me-2 text-warning"></i>
                                    الحالة 
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control-enhanced @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="active" {{ old('status', $warehouse->status) == 'active' ? 'selected' : '' }}>
                                        ✓ نشط
                                    </option>
                                    <option value="inactive" {{ old('status', $warehouse->status) == 'inactive' ? 'selected' : '' }}>
                                        ✗ معطل
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group-enhanced">
                        <label>
                            <i class="fas fa-align-right me-2 text-secondary"></i>
                            الوصف
                        </label>
                        <textarea class="form-control-enhanced @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4"
                                  placeholder="أضف وصفاً تفصيلياً للمخزن...">{{ old('description', $warehouse->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between pt-3" style="border-top: 2px solid #e9ecef;">
                        <a href="{{ route('inventory.warehouses.index') }}" class="btn btn-outline-secondary" style="border-radius: 25px; padding: 12px 30px; font-weight: 600;">
                            <i class="fas fa-arrow-right me-2"></i>
                            رجوع
                        </a>
                        <button type="submit" class="btn btn-inventory-success" style="padding: 12px 40px;">
                            <i class="fas fa-save me-2"></i>
                            تحديث المخزن
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <!-- بطاقة معلومات محسنة -->
            <div class="stat-card fade-in" style="animation-delay: 0.2s; background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);">
                <h5 class="mb-4">
                    <i class="fas fa-info-circle me-2" style="color: #667eea;"></i>
                    معلومات مهمة
                </h5>
                <div class="d-grid gap-3">
                    <div class="p-3" style="background: white; border-radius: 10px; border-right: 4px solid #11998e;">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <strong>رمز المخزن</strong> يجب أن يكون فريداً
                    </div>
                    <div class="p-3" style="background: white; border-radius: 10px; border-right: 4px solid #4facfe;">
                        <i class="fas fa-user-tie text-info me-2"></i>
                        يمكن <strong>تعيين مسؤول</strong> لكل مخزن
                    </div>
                    <div class="p-3" style="background: white; border-radius: 10px; border-right: 4px solid #f5576c;">
                        <i class="fas fa-times-circle text-danger me-2"></i>
                        المخازن <strong>المعطلة</strong> لا تظهر في العمليات
                    </div>
                </div>
            </div>

            <!-- بطاقة نصائح -->
            <div class="stat-card fade-in mt-4" style="animation-delay: 0.3s; background: linear-gradient(135deg, rgba(17, 153, 142, 0.05) 0%, rgba(56, 239, 125, 0.05) 100%);">
                <h5 class="mb-4">
                    <i class="fas fa-lightbulb me-2" style="color: #f5576c;"></i>
                    نصائح مفيدة
                </h5>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="fas fa-arrow-left text-success me-2"></i>
                        استخدم أسماء واضحة ومميزة للمخازن
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-arrow-left text-success me-2"></i>
                        أضف موقع المخزن بدقة لسهولة التتبع
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-arrow-left text-success me-2"></i>
                        تعيين مسؤول يساعد في المساءلة
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
