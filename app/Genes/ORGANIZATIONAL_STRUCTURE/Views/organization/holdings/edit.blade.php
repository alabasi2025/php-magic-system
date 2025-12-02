{{-- /home/ubuntu/php-magic-system/resources/views/organization/holdings/edit.blade.php --}}
{{-- نموذج تعديل بيانات الهيكل التنظيمي (Holding) --}}

@extends('layouts.app') {{-- افتراض استخدام ملف تخطيط رئيسي اسمه app.blade.php --}}

@section('title', 'تعديل الهيكل التنظيمي')

@section('content')
<div class="container mt-5" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-end">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i> {{-- أيقونة Font Awesome --}}
                        تعديل بيانات الهيكل التنظيمي
                    </h4>
                </div>
                <div class="card-body text-end">

                    {{-- عرض رسائل الفلاش (Flash Messages) --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- نموذج التعديل --}}
                    {{-- يفترض أن المتغير $holding يحتوي على بيانات الهيكل التنظيمي الحالي --}}
                    <form action="{{ route('organization.holdings.update', $holding->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- استخدام طريقة PUT لتعديل البيانات --}}

                        {{-- حقل الكود (code) --}}
                        <div class="mb-3">
                            <label for="code" class="form-label">كود الهيكل التنظيمي:</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $holding->code) }}" required>
                            {{-- عرض أخطاء التحقق (Validation Errors) --}}
                            @error('code')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- حقل الاسم بالعربية (name) --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم (بالعربية):</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $holding->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- حقل الاسم بالإنجليزية (name_en) --}}
                        <div class="mb-3">
                            <label for="name_en" class="form-label">الاسم (بالإنجليزية):</label>
                            <input type="text" class="form-control @error('name_en') is-invalid @enderror" id="name_en" name="name_en" value="{{ old('name_en', $holding->name_en) }}">
                            @error('name_en')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- حقل البريد الإلكتروني (email) --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني:</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $holding->email) }}">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- حقل الهاتف (phone) --}}
                        <div class="mb-3">
                            <label for="phone" class="form-label">رقم الهاتف:</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $holding->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- حقل الرقم الضريبي (tax_number) --}}
                        <div class="mb-3">
                            <label for="tax_number" class="form-label">الرقم الضريبي:</label>
                            <input type="text" class="form-control @error('tax_number') is-invalid @enderror" id="tax_number" name="tax_number" value="{{ old('tax_number', $holding->tax_number) }}">
                            @error('tax_number')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- حقل حالة النشاط (is_active) --}}
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox" id="is_active" name="is_active" role="switch" value="1" {{ old('is_active', $holding->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">نشط</label>
                            @error('is_active')
                                <div class="invalid-feedback d-block"> {{-- d-block لجعل الرسالة تظهر أسفل الـ switch --}}
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- أزرار الإجراءات --}}
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>
                                حفظ التعديلات
                            </button>
                            <a href="{{ route('organization.holdings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right me-2"></i> {{-- أيقونة للعودة (سهم لليمين في RTL) --}}
                                إلغاء والعودة
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- يمكن إضافة قسم للسكريبتات إذا لزم الأمر --}}
@push('scripts')
<script>
    // أي سكريبتات إضافية خاصة بهذه الصفحة
</script>
@endpush
