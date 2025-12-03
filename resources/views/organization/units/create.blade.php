@extends('layouts.app')

@section('title', 'إنشاء وحدة تنظيمية جديدة')

@section('content')
<div class="container-fluid" dir="rtl">
    
    {{-- العنوان والتنقل --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle text-success"></i> إنشاء وحدة تنظيمية جديدة
        </h1>
        <a href="{{ route('organization.units.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-right fa-sm ml-1"></i> العودة إلى قائمة الوحدات
        </a>
    </div>

    {{-- رسائل الفلاش --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-times-circle ml-2"></i> 
            <strong>خطأ!</strong> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- البطاقة الرئيسية --}}
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-success">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-edit ml-1"></i> نموذج إنشاء وحدة تنظيمية
                    </h6>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('organization.units.store') }}" method="POST" id="createUnitForm">
                        @csrf

                        {{-- معلومات أساسية --}}
                        <div class="border-bottom pb-3 mb-4">
                            <h6 class="text-primary font-weight-bold">
                                <i class="fas fa-info-circle ml-1"></i> المعلومات الأساسية
                            </h6>
                        </div>

                        <div class="row">
                            {{-- الكيان القابض --}}
                            <div class="col-md-6 mb-4">
                                <label for="holding_id" class="form-label font-weight-bold">
                                    <i class="fas fa-building text-primary ml-1"></i>
                                    الكيان القابض <span class="text-danger">*</span>
                                </label>
                                <select class="form-control form-control-lg @error('holding_id') is-invalid @enderror" 
                                        id="holding_id" 
                                        name="holding_id" 
                                        required>
                                    <option value="">اختر الكيان القابض</option>
                                    @foreach ($holdings as $holding)
                                        <option value="{{ $holding->id }}" {{ old('holding_id') == $holding->id ? 'selected' : '' }}>
                                            {{ $holding->name }} ({{ $holding->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('holding_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle ml-1"></i> اختر الشركة القابضة التي تتبع لها هذه الوحدة
                                </small>
                            </div>

                            {{-- رمز الوحدة --}}
                            <div class="col-md-6 mb-4">
                                <label for="code" class="form-label font-weight-bold">
                                    <i class="fas fa-barcode text-info ml-1"></i>
                                    رمز الوحدة <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code') }}" 
                                       required 
                                       maxlength="20"
                                       placeholder="مثال: UNIT-001">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle ml-1"></i> رمز فريد للوحدة (حروف وأرقام)
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            {{-- اسم الوحدة --}}
                            <div class="col-md-12 mb-4">
                                <label for="name" class="form-label font-weight-bold">
                                    <i class="fas fa-tag text-success ml-1"></i>
                                    اسم الوحدة (بالعربية) <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required
                                       placeholder="مثال: وحدة المبيعات">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- تفاصيل إضافية --}}
                        <div class="border-bottom pb-3 mb-4 mt-4">
                            <h6 class="text-primary font-weight-bold">
                                <i class="fas fa-cogs ml-1"></i> التفاصيل الإضافية
                            </h6>
                        </div>

                        <div class="row">
                            {{-- نوع الوحدة --}}
                            <div class="col-md-6 mb-4">
                                <label for="type" class="form-label font-weight-bold">
                                    <i class="fas fa-layer-group text-warning ml-1"></i>
                                    نوع الوحدة <span class="text-danger">*</span>
                                </label>
                                <select class="form-control form-control-lg @error('type') is-invalid @enderror" 
                                        id="type" 
                                        name="type" 
                                        required>
                                    <option value="">اختر نوع الوحدة</option>
                                    <option value="company" {{ old('type') == 'company' ? 'selected' : '' }}>
                                        <i class="fas fa-building"></i> شركة
                                    </option>
                                    <option value="branch" {{ old('type') == 'branch' ? 'selected' : '' }}>
                                        <i class="fas fa-code-branch"></i> فرع
                                    </option>
                                    <option value="department" {{ old('type') == 'department' ? 'selected' : '' }}>
                                        <i class="fas fa-sitemap"></i> قسم
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- مدير الوحدة --}}
                            <div class="col-md-6 mb-4">
                                <label for="manager_id" class="form-label font-weight-bold">
                                    <i class="fas fa-user-tie text-secondary ml-1"></i>
                                    مدير الوحدة
                                </label>
                                <select class="form-control form-control-lg @error('manager_id') is-invalid @enderror" 
                                        id="manager_id" 
                                        name="manager_id">
                                    <option value="">لا يوجد مدير محدد</option>
                                    @foreach ($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('manager_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle ml-1"></i> اختياري - يمكن تحديده لاحقاً
                                </small>
                            </div>
                        </div>

                        {{-- حالة التفعيل --}}
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <div class="custom-control custom-switch custom-control-lg">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-bold" for="is_active">
                                        <i class="fas fa-toggle-on text-success ml-1"></i>
                                        الوحدة مفعلة ونشطة
                                    </label>
                                </div>
                                <small class="form-text text-muted mr-5">
                                    إذا كانت الوحدة غير نشطة، لن تظهر في التقارير والعمليات اليومية
                                </small>
                            </div>
                        </div>

                        {{-- أزرار الإجراءات --}}
                        <div class="border-top pt-4 mt-4">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <button type="submit" class="btn btn-success btn-lg btn-block shadow">
                                        <i class="fas fa-save ml-2"></i> حفظ الوحدة الجديدة
                                    </button>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <a href="{{ route('organization.units.index') }}" class="btn btn-secondary btn-lg btn-block">
                                        <i class="fas fa-times ml-2"></i> إلغاء
                                    </a>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            {{-- بطاقة المساعدة --}}
            <div class="card shadow-sm border-right-info mb-4">
                <div class="card-body">
                    <h6 class="text-info font-weight-bold mb-3">
                        <i class="fas fa-lightbulb ml-1"></i> نصائح مفيدة
                    </h6>
                    <ul class="mb-0 text-muted">
                        <li>تأكد من اختيار رمز فريد للوحدة لتجنب التكرار</li>
                        <li>يمكنك تعديل جميع البيانات لاحقاً من صفحة التعديل</li>
                        <li>الوحدات غير النشطة لن تظهر في التقارير الافتراضية</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .custom-control-lg .custom-control-label {
        font-size: 1.1rem;
        padding-top: 0.25rem;
    }
    
    .custom-control-lg .custom-control-input {
        width: 3rem;
        height: 1.5rem;
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
    }
    
    .border-right-info {
        border-right: 0.25rem solid #36b9cc !important;
    }
    
    .form-control-lg {
        font-size: 1rem;
    }
    
    label.font-weight-bold {
        color: #5a5c69;
    }
</style>
@endpush

@push('scripts')
<script>
    // تفعيل التحقق من الصحة
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
@endpush
