@extends('layouts.app') {{-- افتراض وجود ملف تخطيط رئيسي (layouts.app) --}}

@section('title', 'إنشاء مؤسسة قابضة جديدة')

@section('content')
    {{-- 8. تعليقات بالعربية: بداية قسم المحتوى --}}
    {{-- دعم RTL والتصميم المتجاوب يتم توفيرهما عبر Bootstrap 5 وسمة dir="rtl" --}}

    <div class="container mt-5" dir="rtl"> {{-- تحديد الاتجاه من اليمين لضمان RTL --}}
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        {{-- 5. Icons من Font Awesome: أيقونة لنموذج الإنشاء --}}
                        <h4 class="mb-0"><i class="fas fa-building me-2"></i> إنشاء مؤسسة قابضة جديدة</h4>
                        <a href="{{ route('organization.holdings.index') }}" class="btn btn-light btn-sm">
                            {{-- أيقونة Font Awesome للعودة --}}
                            <i class="fas fa-arrow-right me-1"></i> العودة للقائمة
                        </a>
                    </div>

                    <div class="card-body">
                        {{-- 6. Flash Messages: عرض رسائل الفلاش (Success/Error) --}}
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

                        {{-- 7. Validation Errors: عرض أخطاء التحقق من الصحة --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0" style="list-style-type: none; padding-right: 0;">
                                    @foreach ($errors->all() as $error)
                                        <li><i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- نموذج الإنشاء --}}
                        <form action="{{ route('organization.holdings.store') }}" method="POST">
                            @csrf
                            {{-- 8. تعليقات بالعربية: حقل رمز المؤسسة --}}
                            <div class="mb-3">
                                <label for="code" class="form-label required">رمز المؤسسة القابضة</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 8. تعليقات بالعربية: حقل الاسم بالعربية --}}
                            <div class="mb-3">
                                <label for="name" class="form-label required">الاسم (بالعربية)</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 8. تعليقات بالعربية: حقل الاسم بالإنجليزية --}}
                            <div class="mb-3">
                                <label for="name_en" class="form-label required">الاسم (بالإنجليزية)</label>
                                <input type="text" class="form-control @error('name_en') is-invalid @enderror" id="name_en" name="name_en" value="{{ old('name_en') }}" required dir="ltr"> {{-- تحديد الاتجاه لليسار للأسماء الإنجليزية --}}
                                @error('name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 8. تعليقات بالعربية: حقل البريد الإلكتروني --}}
                            <div class="mb-3">
                                <label for="email" class="form-label required">البريد الإلكتروني</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required dir="ltr">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 8. تعليقات بالعربية: حقل رقم الهاتف --}}
                            <div class="mb-3">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" dir="ltr">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 8. تعليقات بالعربية: حقل الرقم الضريبي --}}
                            <div class="mb-3">
                                <label for="tax_number" class="form-label">الرقم الضريبي</label>
                                <input type="text" class="form-control @error('tax_number') is-invalid @enderror" id="tax_number" name="tax_number" value="{{ old('tax_number') }}" dir="ltr">
                                @error('tax_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 8. تعليقات بالعربية: حقل حالة التفعيل (is_active) --}}
                            <div class="mb-3 form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="fas fa-toggle-on me-1"></i> مفعلة
                                </label>
                                @error('is_active')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 8. تعليقات بالعربية: زر الحفظ --}}
                            <button type="submit" class="btn btn-success w-100 mt-3">
                                <i class="fas fa-save me-2"></i> حفظ المؤسسة القابضة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- 8. تعليقات بالعربية: يمكن إضافة سكريبتات خاصة بالصفحة هنا --}}
    <script>
        // مثال على سكريبت لتأكيد الإرسال
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                // يمكنك إضافة منطق تحقق إضافي هنا إذا لزم الأمر
                // console.log('Form submitted');
            });
        });
    </script>
@endpush
